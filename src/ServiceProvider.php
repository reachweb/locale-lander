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

    protected $tags = [
        Tags\LocaleBanner::class,
    ];

    public function register()
    {
        $this->app->singleton('localehelper', function ($app) {
            return new LocaleHelper;
        });

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'locale-lander');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/locale-lander'),
        ], 'locale-lander-views');

    }
}
