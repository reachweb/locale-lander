<?php

namespace Reach\LocaleLander;

use Reach\LocaleLander\Http\Middleware\HandleLocaleRedirection;
use Reach\LocaleLander\Support\LocaleHelper;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $middlewareGroups = [
        'statamic.web' => [
            HandleLocaleRedirection::class,
        ],
    ];

    public function register()
    {
        $this->app->singleton('localehelper', function ($app) {
            return new LocaleHelper();
        });
    }
}
