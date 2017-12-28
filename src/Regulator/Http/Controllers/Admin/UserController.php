<?php

namespace Jameron\Regulator\Http\Controllers\Admin;

use DB;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Jameron\Regulator\Models\Role;
use App\Http\Controllers\Controller;
use Jameron\Enrollments\Models\Company;
use Jameron\Regulator\Http\Requests\UserRequest;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;

class UserController extends Controller
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
                    'column' => 'first_name',
                    'label' => 'First Name'
                ],
                [
                    'column' => 'last_name',
                    'label' => 'Last name'
                ],
                [
                    'column' => 'email',
                    'label' => 'Email',
                    'link'=>[
                        'id_column' => 'id',
                        'resource_route'=>'users'
                    ]
                ],
                [
                    'column' => 'role',
                    'label' => 'Role'
                ],
                [
                    'column' => 'online',
                    'label' => 'Online'
                ],
                [
                    'column' => 'last_login',
                    'label' => 'Last login'
                ],
                [
                    'column' => 'enabled',
                    'label' => 'enabled'
                ]
            ]);
        }

        return $this->columns;
    }

    public function setOnlineStatus($users, $page = null)
    {
        $online_users = DB::table('sessions')
            ->where('last_activity', '>', time() - 60)
            ->join('users', 'users.id', '=', 'sessions.user_id')
            ->pluck('last_activity', 'user_id');

        $user_ids = array_keys($online_users->toArray());

        foreach ($users as $user) {
            if (array_key_exists($user->id, $online_users->toArray())) {
                $user->online = config('regulator.display.users.online_status_identifier');
                $user->last_active = Carbon::createFromTimeStamp($online_users[$user->id], 'America/Los_Angeles')->format('F j, Y g:i:s a');
            } else {
                $user->online = config('regulator.display.users.offline_status_identifier');
            }
        }

        if ($page) {
            $users = $this->sortBy($users, 'online', $order, $page);
        }
        return $users;
    }

    public function index(Request $request)
    {
        $search = ($request->get('search_string')) ? $request->get('search_string') : null;
        $sort_by = ($request->get('sortBy')) ? $request->get('sortBy') : 'email';
        $order = ($request->get('order')) ? $request->get('order') : 'ASC';
        $page = ($request->get('page')) ? $request->get('page') : null;

        if (! $search) {
            switch ($sort_by) {

            case 'email':

                $user = resolve('App\User');
                $users = $user->select('users.*')
                                    ->with('roles')
                                    ->orderBy('users.email', $order)
                                    ->paginate(20);

                if (config('session.driver') == 'database') {
                    $users = $this->setOnlineStatus($users);
                }

                    break;

                case 'role':

                    $user = resolve('App\User');
                    $users = $user->join('regulator_role_user', 'regulator_role_user.user_id', '=', 'users.id')
                                    ->join('regulator_roles', 'regulator_roles.id', '=', 'regulator_role_user.role_id')
                                    ->orderBy('regulator_roles.name', $order)
                                    ->with('roles')
                                    ->select('users.id', 'users.email', 'users.name', 'users.last_login', 'regulator_roles.name as role_name')
                                    ->distinct()
                                    ->paginate(20);

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    break;

                case 'first_name':

                    $user = resolve('App\User');
                    $users = $user->select('users.*')
                                    ->with('roles')
                                    ->orderBy('users.first_name', $order)
                                    ->paginate(20);

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    break;

                case 'last_name':

                    $user = resolve('App\User');
                    $users = $user->select('users.*')
                                    ->with('roles')
                                    ->orderBy('users.last_name', $order)
                                    ->paginate(20);

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    break;

                case 'email':

                    $user = resolve('App\User');
                    $users = $user->select('users.*')
                                    ->with('roles')
                                    ->orderBy('users.email', $order)
                                    ->paginate(20);

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    break;

                case 'id':

                    $user = resolve('App\User');
                    $users = $user->select('users.*')
                                    ->with('roles')
                                    ->orderBy('users.first_name', $order)
                                    ->paginate(20);

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    break;

                case 'online':

                    $user = resolve('App\User');
                    $users = $user->select('users.*')
                        ->with('roles')
                        ->get();

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    $page = $request->get('page');
                    $users = $this->sortBy($users, 'online', $order, $page);

                    break;

                case 'last_login':

                    $user = resolve('App\User');
                    $users = $user->select('users.*')
                        ->with('roles')
                        ->orderBy('users.last_login', $order)
                        ->paginate(20);

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    break;

                case 'enabled':

                    $user = resolve('App\User');
                    $users = $user->select('users.*')
                        ->with('roles')
                        ->orderBy('users.disabled', $order)
                        ->paginate(20);

                    if (config('session.driver') == 'database') {
                        $users = $this->setOnlineStatus($users);
                    }

                    break;
            }
        } elseif ($search) {
            $user = resolve('App\User');
            $users = $user->select('users.*')
                            ->with('roles')
                            ->where(function ($query) use ($search) {
                                $query->where('users.email', 'LIKE', '%'.$search.'%')
                                    ->orWhere('users.last_name', 'LIKE', '%'.$search.'%')
                                    ->orWhere('users.first_name', 'LIKE', '%'.$search.'%');
                            })->paginate(20);

            if (config('session.driver') == 'database') {
                $users = $this->setOnlineStatus($users);
            }
        } else {
            $user = resolve('App\User');
            $users = $user->select('users.*')
                            ->with('roles')
                            ->paginate(20);

            if (config('session.driver') == 'database') {
                $users = $this->setOnlineStatus($users);
            }
        }

        $data = [];
        $data['search_string'] = $search;
        $data['sort_by'] = $sort_by;
        $data['order'] = $order;
        $data['items'] = $users;
        $data['resource_route'] = '/admin/users';
        $data['columns'] = $this->getIndexViewColumns();

        return view('regulator::admin.users.index')
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
        return view('regulator::admin.users.create', compact('companies', 'roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(UserRequest $request)
    {
        $user = resolve('App\User');
        $user->name = $request->get('name');
        $user->email = $request->get('email');

        if ($request->get('company_id')) {
            $user->company_id = $request->get('company_id');
        }

        $user->password = bcrypt($request->get('password'));

        $user->save();

        $request->roles = ($request->get('roles')) ? $request->get('roles') : [];
        $user->roles()->sync($request->roles);

        return redirect('/admin/users')
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
        $user = resolve('App\User');
        $user = $user->where('id', $id)
            ->with('roles')
            ->first();

        $roles = Role::all();

        return view('regulator::admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UserRequest $request, $id)
    {
        $user = resolve('App\User');
        $user = $user->where('id', $id)
            ->firstOrFail();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        if (!empty($request->get('password'))) {
            $user->password = bcrypt($request->get('password'));
        }
        if ($request->get('company_id')) {
            $user->company_id = $request->get('company_id');
        }
        $user->save();

        $request->roles = ($request->get('roles')) ? $request->get('roles') : [];
        $user->roles()->sync($request->roles);

        return redirect('/admin/users')
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
        $user = resolve('App\User');
        $user = $user->find($id);

        if ($user) {
            $user->delete();
            return redirect('admin/users')->with('success_message', 'User was deleted.');
        }
    }

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
