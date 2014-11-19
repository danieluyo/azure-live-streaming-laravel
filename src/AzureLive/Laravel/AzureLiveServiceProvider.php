<?php
/**
 * AzureLiveServiceProvider
 */

namespace AzureLive\Laravel;

use AzureLive\Laravel\AzureLive;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * AzureLiveServiceProvider
 */
class AzureLiveServiceProvider extends ServiceProvider
{
    const VERSION = '0.0.1';

    // Do the following functions do anything? These are copied from AWS

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('azurelive', function()
        {
            return new AzureLive;
        });
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // $this->package('azurelive/azurelive-sdk-php-laravel', 'azurelive');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('azurelive');
    }

}