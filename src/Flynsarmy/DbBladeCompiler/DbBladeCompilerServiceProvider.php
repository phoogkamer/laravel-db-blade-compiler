<?php namespace Flynsarmy\DbBladeCompiler;

use Illuminate\Support\ServiceProvider;
use Illuminate\View\Engines\CompilerEngine;

class DbBladeCompilerServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $config_path = __DIR__ . '/../../../config/db-blade-compiler.php';
        $this->publishes([$config_path => config_path('db-blade-compiler.php')], 'config');

        $views_path = __DIR__ . '/../../../config/.gitkeep';
        $this->publishes([$views_path => storage_path('app/db-blade-compiler/views/.gitkeep')]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $config_path = __DIR__ . '/../../../config/db-blade-compiler.php';
        $this->mergeConfigFrom($config_path, 'db-blade-compiler');

        $this->app['dbview'] = $this->app->share(function ($app) {
            return $app->make(DbView::class);
        });

        $this->app->bind(DbBladeCompiler::class, function($app) {
            $cache_path = storage_path('app/db-blade-compiler/views');

            return new DbBladeCompiler($app['files'], $cache_path, $app['config']);
        });

        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('DbView', DbView::class);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array();
    }

}