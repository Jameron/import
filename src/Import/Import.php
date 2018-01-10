<?php 

namespace Jameron\Import;

use DB;
use Validator;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Jameron\Import\Repositories\ImportRepository;

class Import
{

    /**
     * Import configuration settings
     *
     * @var array
     */
    protected $config;

    /**
     * Errors captured during validation or saving.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * The number of new models inserted.
     *
     * @var integer
     */
    protected $new_models_count;

    /**
     * The number of new related models inserted.
     *
     * @var integer
     */
    protected $new_related_models = [];

    /**
     * The csv data as an array.
     *
     * @var array
     */
    protected $csv_data;

    /**
     * The csv data first row cleaned up as an array.
     *
     * @var array
     */
    protected $column_headers;

    /**
     * Constructor for Import.
     *
     * @return void
     */
    public function __construct()
    {
        $this->config = config('import');
        $this->errors = collect();
        $this->new_models_count = 0;
    }

    /**
     * Inserts data from csv file to a database
     *
     * @param  string|array $values
     * @return Snap\Taxonomy\Models\Role
     */
    public function import($csv_file)
    {
        $csv_rows = array_map('str_getcsv', file($csv_file));
        $raw_column_headers = array_shift($csv_rows);
        $num_rows = count($csv_rows);

        $this->setColumnHeaders($raw_column_headers);
        $this->cleanCsvHeadersData($this->column_headers);
        $this->parseRows($csv_rows);

        $error_bags = $this->errors->flatten();
        $model_as_array = explode("\\", $this->config['import_model']);
        $model_name = end($model_as_array);
        $num_skipped_rows = $num_rows - $this->new_models_count;

        return [
            'error_bags' => $error_bags,
            'new_models_count' => $this->new_models_count,
            'model_name' => $model_name,
            'num_skipped_rows' => $num_skipped_rows,
            'new_related_models' => $this->new_related_models
        ];
    }

    public function validateModel($model, $row_index)
    {

        $model_rules = (new $this->config['validator'])
            ->rules();
        $messages = (new $this->config['validator'])->messages();

        if($model_rules) {

            $validator = Validator::make($model->toArray(), $model_rules, $messages);

            if ($validator->fails()) {

                $validator->getMessageBag()->add('row_error', ($row_index + 2) ); 
                $this->errors->push($validator->messages());

            } else {

                $model->save();
                $this->new_models_count++;
            }
        }
    }

    public function addErrors($errors)
    {

    }

    public function parseRows($rows)
    {
        foreach($rows as $row_index => $row) {

            $response = $this->parseRow($row, $row_index);
            if($response instanceof $this->config['import_model']) {
                $this->validateModel($response, $row_index);
            } else {
                // related model error
                $this->errors->push($response);
            }
        }
    }

    public function setColumnHeaders($headers)
    {
        $this->column_headers = $headers;
        return $this;
    }

    public function parseRow($row, $row_index)
    {

        $model = resolve('App\ImportModel');
        $model_columns_array = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
        $this->config = config('import'); 
        $relationships = $this->config['relationships'];

        $errors = collect();

        foreach($this->column_headers as $key => $column_header) {

            if(in_array($column_header, $model_columns_array)) {

                $column_type = DB::getSchemaBuilder()->getColumnType($model->getTable(), $column_header);

                if($column_type=="boolean") {

                    $row[$key] = (trim(strtolower($row[$key]))=='yes') ? 1 : 0; 

                } else if ($column_type=="datetime") {

                    if( strtotime($row[$key])) {
                        $row[$key] = date( 'Y-m-d H:i:s', strtotime($row[$key])); 
                    } else {
                        $row[$key] = null;
                    }

                } else if ($column_type=="integer" || $column_type=="float") {
                    $row[$key] = (preg_replace('/[^a-zA-Z_]/', '', $row[$key]));
                    $row[$key] = (trim($row[$key])=='') ? 0 : null; 

                } else if ($column_type=="text" || $column_type=="string") {

                    $row[$key] = utf8_encode($row[$key]);

                }

                $model->{$column_header} = ($row[$key]);
            }

            if(count($relationships)) {

                $belongs_to_relationships = ( array_filter($relationships, function($item) {
                    return (isset($item['relationship']) && $item['relationship'] == 'belongsTo');
                }));

                $has_many_relationships = ( array_filter($relationships, function($item) {
                    return (isset($item['relationship']) && $item['relationship'] == 'hasMany');
                }));

                $related_key = array_search($column_header, array_column($relationships, 'csv_column'));

                if(is_numeric($related_key)) {

                    //$response = $this->parseRelationships($related_key, $relationships, $row, $key, $row_index);
                    $response = $this->parseRelationship($relationships[$related_key], $row, $key, $row_index);

                    if($response instanceof $relationships[$related_key]['model']) {
                        $model->{$relationships[$related_key]['foreign_key']} = $response->{$relationships[$related_key]['reference_primary_key']};
                    } else {
                        $errors->push($response);
                        continue;
                    }

                } 

            }

        }

        if(count($errors)){
            return $errors;
        }

        return $model;

    }


    public function cleanCsvHeadersData($raw_column_headers)
    {

        $cleanup_rules = config('import.csv_headers_cleanup_rules');

        if(isset($cleanup_rules) && is_array($cleanup_rules)) {
            if(in_array('trim', $cleanup_rules)) {
                $this->setColumnHeaders(array_map('trim', $this->column_headers));
            }

            if(in_array('pound_to_word_number', $cleanup_rules)) {
                $this->setColumnHeaders(str_replace("#", "number", $this->column_headers));
            }

            if(in_array('spaces_to_underscores', $cleanup_rules)) {
                $this->setColumnHeaders(str_replace(' ', '_', $this->column_headers));
            }

            if(in_array('remove_special_characters', $cleanup_rules)) {
                $this->setColumnHeaders(preg_replace('/[^a-zA-Z_]/', '', $this->column_headers));
            }

            if(in_array('lowercase', $cleanup_rules)) {
                $this->setColumnHeaders(array_map('strtolower', $this->column_headers));
            }
        }

    }

    public function parseExtraColumns($extra_columns, $row)
    {
        $result = [];
        foreach($extra_columns as $extra_column) {

            $column_matching_key = array_search($extra_column['column'], $this->column_headers);
            $matching_data = $row[$column_matching_key];

            if($column_matching_key && $matching_data) {

                if(is_array($extra_column['maps_to']) && isset($extra_column['explode_on'])) {

                    if (strpos(trim($matching_data), $extra_column['explode_on']) !== false) {

                        $split_as_array = explode($extra_column['explode_on'], $matching_data);

                        if(count($split_as_array)==2){
                            $result = array_combine($extra_column['maps_to'], $split_as_array);
                        }
                        // ['first_name'=>'Kevin','last_name'=>'price']
                    }

                } else if(is_string($extra_column['maps_to'])) {

                    $result[$extra_column['maps_to']] = $matching_data;
                    // ['phone_number' => '217-369-9922']

                }
            }
        }

        return $result;
    }

    public function parseRelationship($relationship, $row, $key, $row_index)
    {

        $errors = collect();
        $related_model = new $relationship['model'];
        $related_model = $related_model
            ->where($relationship['reference_field'], $row[$key])
            ->first();

        if(!$related_model && $relationship['create_if_not_found']) {

            $new_related_model = new $relationship['model'];
            $new_related_model->{$relationship['reference_field']} = $row[$key];


            if (isset($relationship['extra_columns']) && is_array($relationship['extra_columns'])) {
                $result = $this->parseExtraColumns($relationship['extra_columns'], $row);
                if(isset($result)) {

                    // validate that the column matchup exists on the related table
                    $modelColumnsArray = $new_related_model->getConnection()->getSchemaBuilder()->getColumnListing($new_related_model->getTable());
                    $values = array_intersect_key($result, array_flip($modelColumnsArray));

                    if($values) {
                        foreach($values as $value_key => $value) {
                            $new_related_model->{$value_key} = trim($value);
                        }
                    }
                }
            }

            if (isset($relationship['validator']) && !empty($relationship['validator'])) {

                $rules = (new $relationship['validator'])->rules();
                $messages = (new $relationship['validator'])->messages();

                if($rules) {

                    $validator = Validator::make($new_related_model->toArray(), $rules, $messages);

                    if ($validator->fails()) {

                        $validator->getMessageBag()->add('row_error', ($row_index + 2)); 
                        $errors->push($validator->messages());

                    } else {

                        if (isset($relationship['append_data']) && is_array($relationship['append_data'])) {
                            foreach($relationship['append_data'] as $column_name => $column_data) {
                                $new_related_model->{$column_name} = trim($column_data);
                            }
                        }

                        $new_related_model->save();

                        $related_model = $new_related_model;

                        $model_as_array = explode("\\", $relationship['model']);
                        $model_name = end($model_as_array);

                        $parts = preg_split("/((?<=[a-z])(?=[A-Z])|(?=[A-Z][a-z]))/", $model_name);
                        $model_name_clean = implode(" ", $parts);

                        if (isset($this->new_related_models[$model_name_clean])) $this->new_related_models[$model_name_clean]++;else $this->new_related_models[$model_name_clean]=1;

                    }
                }
            }
        }

        if (count($errors)) {
            return $errors;
        }

        return $related_model;
    }

}
