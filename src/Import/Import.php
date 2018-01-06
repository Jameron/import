<?php 

namespace Jameron\Import;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Jameron\Import\Repositories\ImportRepository;

class Import
{

    /**
     * Errors captured during validation or saving.
     *
     * @var \Illuminate\Support\MessageBag
     */
    protected $errors;

    /**
     * The config/vendor/jameron/regulator.php configuration values.
     *
     * @var array
     */
    protected $csv_data;
    protected $column_headers;

    /**
     * Constructor for Import.
     *
     * @param  Snap\Taxonomy\Repositories\TaxonomyRepository  $repo
     * @param  \Illuminate\Support\MessageBag  $errors
     * @param  array  $config
     * @return void
     */
    public function __construct()
    {


    }

    /**
     * Inserts a Role model object
     *
     * @param  string|array $values
     * @return Snap\Taxonomy\Models\Role
     */
    public function import($csv_file)
    {
        $csv_rows = array_map('str_getcsv', file($csv_file));
        $num_rows = count($csv_rows);
        $raw_column_headers = array_shift($csv_rows);
        $import_config = config('import'); 

        $this->setColumnHeaders($raw_column_headers);
        $this->cleanCsvHeadersData($this->column_headers);

        $errors = collect();

        foreach($csv_rows as $row_index => $row) {

            $response = $this->parseRow($row, $row_index);

            if($response instanceof $import_config['import_model']) {

                $model_rules = (new $import_config['validator'])
                    ->rules();
                $messages = (new $import_config['validator'])->messages();

                if($model_rules) {

                    $validator = Validator::make($response->toArray(), $model_rules, $messages);

                    if ($validator->fails()) {
                        $validator->getMessageBag()->add('row_error', '<strong>Errors on row:</strong> ' . ($row_index + 2) ); 
                        $errors->push($validator->messages());
                    } else {
                        $response->save();
                    }
                }

            } else {
                 $errors->push($response);
            }
        }

        $error_bags = $errors->flatten();

        return [
            'error_bags' => $error_bags
        ];
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
        $import_config = config('import'); 
        $relationships = $import_config['relationships'];
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
                } else if ($column_type=="integer") {
                    $row[$key] = (trim($row[$key])=='') ? 0 : null; 
                }
                $model->{$column_header} = $row[$key];
            }

            if(count($relationships)) {

                $related_key = array_search($column_header, array_column($relationships, 'csv_column'));

                if(is_numeric($related_key)) {

                    $response = $this->parseRelationships($related_key, $relationships, $row, $key, $row_index);
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

    public function parseExtraColumns($relationships, $related_key, $row)
    {
        $result = [];
        foreach($relationships[$related_key]['extra_columns'] as $extra_column) {

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

    public function parseRelationships($related_key, $relationships, $row, $key, $row_index)
    {

        $errors = collect();
        $related_model = new $relationships[$related_key]['model'];
        $related_model = $related_model
            ->where($relationships[$related_key]['reference_field'], $row[$key])
            ->first();

        if(!$related_model && $relationships[$related_key]['create_if_not_found']) {

            $new_related_model = new $relationships[$related_key]['model'];
            $new_related_model->{$relationships[$related_key]['reference_field']} = $row[$key];

            if (isset($relationships[$related_key]['extra_columns']) && is_array($relationships[$related_key]['extra_columns'])) {
                $result = $this->parseExtraColumns($relationships, $related_key, $row);
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

            if (isset($relationships[$related_key]['validator']) && !empty($relationships[$related_key]['validator'])) {

                $rules = (new $relationships[$related_key]['validator'])->rules();
                $messages = (new $relationships[$related_key]['validator'])->messages();

                if($rules) {

                    $validator = Validator::make($new_related_model->toArray(), $rules, $messages);

                    if ($validator->fails()) {

                        $validator->getMessageBag()->add('row_error', '<strong>Errors on row:</strong> ' . ($row_index + 2)); 
                        $errors->push($validator->messages());

                    } else {

                        if (isset($relationships[$related_key]['append_data']) && is_array($relationships[$related_key]['append_data'])) {
                            foreach($relationships[$related_key]['append_data'] as $column_name => $column_data) {
                                $new_related_model->{$column_name} = trim($column_data);
                            }
                        }
                        $new_related_model->save();
                        $related_model = $new_related_model;

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
}
