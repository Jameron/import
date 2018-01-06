<?php

namespace Jameron\Import\Http\Controllers;

use DB;
use Auth;
use Import;
use Validator;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use \Jameron\Import\Http\Requests\ImportRequest;

class ImportController extends Controller
{

	public function getImport()
	{
		return view('import::upload');
	}

    public function postImport(ImportRequest $request)
    { 

        $csv = $request->file('csv');
        $response = Import::import($csv);

        return view('import::upload')
            ->with($response);

	}

}
