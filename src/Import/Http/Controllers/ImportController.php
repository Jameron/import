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
		return view('import::upload');
	}
}
