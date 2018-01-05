<?php

namespace Jameron\Import\Http\Controllers;

use DB;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use \Jameron\Import\Http\Requests\ImportRequest;

class ImportController extends Controller
{

    protected $csv_data;
    protected $column_headers;

	public function getImport()
	{
		return view('import::upload');
	}

    public function postImport(ImportRequest $request)
    { 

        $csv = $request->file('csv');
        $csv_rows = array_map('str_getcsv', file($csv));
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

                if($model_rules) {

                    $validator = Validator::make($response->toArray(), $model_rules);

                    if ($validator->fails()) {
                        $errors->push($validator->messages());
                    } else {
                        $response->save();
                    }
                }

            } else {
                 $errors->push($validator->messages());
            }
        }

        return redirect('import')->withErrors($errors);

	}

    public function parseRow($row, $row_index)
    {

        $model = resolve('App\ImportModel');
        $model_columns_array = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
        $import_config = config('import'); 
        $relationships = $import_config['relationships'];
        $errors = [];

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

                    $related_model = $this->parseRelationships($related_key, $relationships, $row, $key, $row_index);
                    if($related_model instanceof $relationships[$related_key]['model']) {
                        $model->{$relationships[$related_key]['foreign_key']} = $related_model->{$relationships[$related_key]['reference_primary_key']};
                    } else {
                        continue;
                    }
                } 
            }
        }

        return $model;

    }

    public function setColumnHeaders($headers)
    {
        $this->column_headers = $headers;
        return $this;
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

        $related_model = new $relationships[$related_key]['model'];
        $related_model = $related_model
            ->where($relationships[$related_key]['reference_field'], $row[$key])
            ->first();
            
            
            /*
             *DB::table($relationships[$related_key]['reference_table'])
             *->where($relationships[$related_key]['reference_field'], $row[$key])
             *->first();
             */

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
                            $new_related_model->{$value_key} = $value;
                        }
                    }
                }
            }

            if (isset($relationships[$related_key]['validator']) && !empty($relationships[$related_key]['validator'])) {
                $rules = (new $relationships[$related_key]['validator'])->rules();

                if($rules) {

                    $messages = [];
                    if(isset($new_related_model->email)) {

                        $messages = [
                            'email' => 'The :attribute address: ' . $new_related_model->email . ' on row ' . ($row_index + 2) . ' is invalid.',
                        ];

                    }
                    $validator = Validator::make($new_related_model->toArray(), $rules, $messages);

                    if ($validator->fails()) {
                        return $errors[] = $validator->messages();
                    } else {
                        if (isset($relationships[$related_key]['append_data']) && is_array($relationships[$related_key]['append_data'])) {
                            foreach($relationships[$related_key]['append_data'] as $column_name => $column_data) {
                                $new_related_model->{$column_name} = $column_data;
                            }
                        }
                        $new_related_model->save();
                        $related_model = $new_related_model;
                    }
                }
            }
        }

        return $related_model;
    }

}
