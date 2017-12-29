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
	public function getImport()
	{
		return view('import::upload');
	}

	public function postImport()
	{ 

		$model = resolve('App\ImportModel');
		$modelColumnsArray = $model->getConnection()->getSchemaBuilder()->getColumnListing($model->getTable());
		dd($modelColumnsArray);


		array_shift($modelColumnsArray);

			/*
             *if (array_key_exists('name', $values)) {
             *    $values = array_intersect_key($values, array_flip($modelColumnsArray));
             *} else {
             *    $values = array_combine(array_intersect_key($modelColumnsArray, $values), array_intersect_key($values, $modelColumnsArray));
             *}
			 */

		return view('import::upload');
	}
}
