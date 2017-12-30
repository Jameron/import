<?php

namespace Jameron\Import\Http\Controllers;

use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jameron\Regulator\Models\Permission;
use App\Http\Controllers\Controller;
use Jameron\Regulator\Http\Requests\PermissionRequest;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class ImportController extends Controller
{

    protected $csv_data;

	public function getImport()
	{
		return view('import::upload');
	}

    public function cleanCsvHeadersData($raw_column_headers)
    {
        $trimmed_column_headers = array_map('trim', $raw_column_headers);
        $pound_symbol_to_word_number_column_headers = str_replace("#", "number", $trimmed_column_headers);
        $spaces_to_underscores_column_headers = str_replace(' ', '_', $pound_symbol_to_word_number_column_headers);
        $letters_only_column_headers = preg_replace('/[^a-zA-Z_]/', '', $spaces_to_underscores_column_headers);
        $lowercased_column_headers = array_map('strtolower', $letters_only_column_headers);

        return $lowercased_column_headers;
    }

	public function postImport(Request $request)
	{ 

		$model = resolve('App\ImportModel');
		$model_columns_array = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());

        $csv = $request->file('csv');
        $csv_array = array_map('str_getcsv', file($csv));

        $num_rows = count($csv_array);
        $raw_column_headers = array_shift($csv_array);
        $column_headers = $this->cleanCsvHeadersData($raw_column_headers);
        $first_row = $csv_array[0];
        
        $relationships = config('import.relationships');

        foreach($column_headers as $key => $column_header) {

            if(in_array($column_header, $model_columns_array)) {
                $model->{$column_header} = $first_row[$key];
            }

            $related_key = array_search($column_header, array_column($relationships, 'csv_column'));

            if(is_numeric($related_key)) {

                $found_matching_related = DB::table($relationships[$related_key]['reference_table'])
                    ->where($relationships[$related_key]['reference_field'], $first_row[$key])
                    ->first();

                if($found_matching_related) {
                    $model->{$relationships[$related_key]['foreign_key']} = $found_matching_related->{$relationships[$related_key]['reference_primary_key']}; 
                } 

            }
        }

        dd($model);

		return view('import::upload');
	}
}
