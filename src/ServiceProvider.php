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
}
