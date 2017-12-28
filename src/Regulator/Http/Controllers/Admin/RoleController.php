<?php

namespace Jameron\Regulator\Http\Controllers\Admin;

use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jameron\Regulator\Models\Role;
use App\Http\Controllers\Controller;
use Jameron\Regulator\Models\Permission;
use Jameron\Regulator\Http\Requests\RoleRequest;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class RoleController extends Controller
{
    public $columns;

    public function getIndexViewColumns()
    {
        if (Auth::user()->roles()->first()->slug=='admin') {
            $this->columns = collect([
                [
                    'column' => 'id',
                    'label' => 'ID',
                ],
                [
                    'column' => 'name',
                    'label' => 'Name'
                ],
                [
                    'column' => 'slug',
                    'label' => 'Slug'
                ]
            ]);
        }

        return $this->columns;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = ($request->get('search')) ? $request->get('search') : null;
        $sort_by = ($request->get('sortBy')) ? $request->get('sortBy') : 'name';
        $order = ($request->get('order')) ? $request->get('order') : 'ASC';

        $roles = Role::select('regulator_roles.*');

        if ($search) {
            $roles = $roles->where(function ($query) use ($search) {
                $query->where('regulator_roles.name', 'LIKE', '%'.$search.'%');
            });
        }

        if ($sort_by) {
            $roles = $roles
                ->orderBy($sort_by, $order);
        }

        $roles = $roles->paginate(config('admin.paginate.count'));

        $data = [];
        $data['search_string'] = $search;
        $data['sort_by'] = $sort_by;
        $data['order'] = $order;
        $data['items'] = $roles;
        $data['columns'] = $this->getIndexViewColumns();

        return view('regulator::admin.roles.index')
            ->with($data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all();

        return view('regulator::admin.roles.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(RoleRequest $request)
    {
        $role = new Role();
        $role->name = $request->get('name');
        $role->slug = $request->get('slug');
        $role->save();
        
        $role->permissions()->sync($request->get('permissions'));

        return redirect('/admin/roles')
            ->with('success_message', 'Saved');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::where('id', $id)
            ->with('permissions')
            ->first();
        $permissions = Permission::all();

        return view('regulator::admin.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(RoleRequest $request, $id)
    {
        $role = Role::where('id', $id)
            ->firstOrFail();
        $role->name = $request->get('name');
        $role->slug = $request->get('slug');
        $role->save();

        $role->permissions()->sync($request->get('permissions'));

        return redirect('admin/roles')
            ->with('success_message', 'Saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = Role::find($id);

        if ($user) {
            $user->delete();
            return redirect('admin/roles')->with('success_message', 'Role was deleted.');
        }
    }


    // Takes in array of objects
    public function sortBy($array_of_objects, $sort_by=null, $order, $page)
    {
        $collection = new Collection($array_of_objects);
        if ($sort_by) {
            if ($order=='desc') {
                $sorted = $collection->sortBy(function ($role) use ($sort_by) {
                    return $role->{$sort_by};
                })->reverse();
            } elseif ($order=='asc') {
                $sorted = $collection->sortBy(function ($role) use ($sort_by) {
                    return $role->{$sort_by};
                });
            }
        } else {
            $sorted = $collection;
        }

        $num_per_page = 20;
        if (!$page) {
            $page = 1;
        }

        $offset = ($page - 1) * $num_per_page;
        $sorted = $sorted->splice($offset, $num_per_page);

        return  new Paginator($sorted, count($array_of_objects), $num_per_page, $page);
    }
}
