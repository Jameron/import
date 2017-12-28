<?php

namespace Jameron\Regulator;

use Schema;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Jameron\Regulator\Models\Permission;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class RegulatorServiceProvider extends ServiceProvider
{
    protected $package = 'regulator';
    protected $routes = '../routes/routes.php';
    protected $views = '../resources/views';
    protected $policies = [];

    /**
     * Register the application's policies.
     *
     * @return void
     */
    public function registerPolicies()
    {
        foreach ($this->policies as $key => $value) {
            Gate::policy($key, $value);
        }
    }

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(GateContract $gate=null, Router $router)
    {
        $router->aliasMiddleware('role', 'Jameron\Regulator\Http\Middleware\RoleMiddleware');
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->publishes([
            __DIR__.'/../config/regulator.php' => config_path('regulator.php'),
            __DIR__.'/../resources/assets/js' => resource_path('assets/regulator/js'),
            __DIR__.'/../resources/assets/sass' => resource_path('assets/regulator/sass'),
            __DIR__.'/../resources/views' => resource_path('views/vendor'),
        ]);

        $this->loadViewsFrom(resource_path('views/vendor'), 'regulator');
        $this->app->make(Factory::class)->load(__DIR__ . '/../database/factories');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->registerPolicies($gate);
        if (Schema::hasTable('regulator_permissions')) {
            foreach ($this->getPermissions() as $permission) {
                $gate->define($permission->slug, function ($user) use ($permission) {
                    return $user->hasRole($permission->roles);
                });
            }
        }

        $this->app->bind('App\User', function ($app) {
            $model = config('regulator.user.model');
            return new $model;
        });
    }

    /*
     * Get the collection of permissions with the related roles.
     */
    protected function getPermissions()
    {
        return Permission::with('roles')->get();
    }
}
