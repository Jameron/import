<?php

namespace Jameron\Import;

use Schema;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;

class ImportServiceProvider extends ServiceProvider
{
    protected $package = 'import';
    protected $routes = '../routes/routes.php';
    protected $views = '../resources/views';
    protected $policies = [];

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(GateContract $gate=null, Router $router)
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        $this->publishes([
            __DIR__.'/../resources/assets/js' => resource_path('assets/import/js'),
            __DIR__.'/../resources/assets/sass' => resource_path('assets/import/sass'),
            __DIR__.'/../config/import.php' => config_path('import.php'),
            __DIR__.'/../resources/views' => resource_path('views/vendor'),
        ]);

        $this->loadViewsFrom(resource_path('views/vendor'), 'import');
		$this->app->make(Factory::class)->load(__DIR__ . '/../database/factories');

        $this->app->bind('App\ImportModel', function ($app) {
            $model = config('import.import_model');
            return new $model;
        });
    }

}
