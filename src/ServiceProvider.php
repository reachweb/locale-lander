<?php

namespace Reach\LocaleLander;

use Reach\LocaleLander\Http\Middleware\HandleLocaleRedirection;
use Statamic\Providers\AddonServiceProvider;

class ServiceProvider extends AddonServiceProvider
{
    protected $middlewareGroups = [
        'statamic.web' => [
            HandleLocaleRedirection::class,
        ],
    ];

    public function bootAddon()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/locale-lander.php', 'statamic.locale-lander');

        $this->publishes([
            __DIR__.'/../config/locale-lander.php' => config_path('statamic/locale-lander.php'),
        ], 'locale-lander');
    }
}
