<?php

namespace Jameron\Regulator\Http\Controllers\Auth;

use Auth;
use Config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Http\Controllers\Auth\LoginController as LaravelLoginController;

class LoginController extends LaravelLoginController
{
    /*
    |--------------------------------------------------------------------------
    | Regulator Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller extends the native Laravel controller that handles
    | authenticating users for the application and
    | redirecting them to your home screen.
    |
    */

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $user_roles = Auth::user()
            ->roles
            ->first();

        if ($user_roles) {
            if (array_key_exists($user_roles->slug, Config::get('regulator.roles'))) {
                $redirectURI = Config::get('regulator.roles')[$user_roles->slug]['loginRedirectURI'];
            } else {
                $redirectURI = strtolower($user_roles->slug);
            }

            return redirect($redirectURI);
        }
    }
}
