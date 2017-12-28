<?php 

Route::group(['middleware' => 'web', 'before' => 'auth'], function () {

    // Authentication Routes...
    Route::get('login', 'App\Http\Controllers\Auth\LoginController@showLoginForm')->name('login');
    Route::post('login', 'Jameron\Regulator\Http\Controllers\Auth\LoginController@login');
    Route::post('logout', 'App\Http\Controllers\Auth\LoginController@logout')->name('logout');

    // Registration Routes...
    Route::get('register', 'App\Http\Controllers\Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'App\Http\Controllers\Auth\RegisterController@register');

    // Password Reset Routes...
    Route::get('password/reset', 'App\Http\Controllers\Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/email', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset/{token}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::post('password/reset', 'App\Http\Controllers\Auth\ResetPasswordController@reset');

    Route::get('/', function () {
        return redirect('/login');
    });
});

Route::group(['middleware' => ['web', 'auth', 'role:admin']], function () {
    Route::get(config('regulator.user.resource_route') . '/search', 'Jameron\Regulator\Http\Controllers\Admin\UserController@index');
    Route::resource(config('regulator.user.resource_route'), 'Jameron\Regulator\Http\Controllers\Admin\UserController');
    Route::resource(config('regulator.role.resource_route'), 'Jameron\Regulator\Http\Controllers\Admin\RoleController');
    Route::resource(config('regulator.permission.resource_route'), 'Jameron\Regulator\Http\Controllers\Admin\PermissionController');
});
