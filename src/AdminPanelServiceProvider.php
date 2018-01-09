<?php

namespace AdminPanel;

use Illuminate\Support\ServiceProvider;

/**
 * Class AdminPanelServiceProvider
 * @package MulticahatServiceProvider
 */
class AdminPanelServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
//    protected $defer = true;

    public function boot()
    {
//        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations/');
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
        $this->loadViewsFrom(__DIR__ . '/views/', 'admin');

        $this->publishes([
            __DIR__ . '/assets/admin.css'      => public_path('css/admin.css'),
            __DIR__ . '/assets/admin.js'       => public_path('js/admin/admin.js'),
            __DIR__ . '/assets/adminCharts.js' => public_path('js/admin/adminCharts.js'),
        ], 'public');
        // $this->publishes([
        //     __DIR__ . '/../config/invitedUsers.php' => config_path('invitedUsers.php'),
        // ], 'config');

        // $this->publishes([
        //     __DIR__ . '/../database/migrations/' => database_path('migrations'),
        // ], 'migrations');
    }

    /**
     *
     */
    public function register()
    {
    }
}
