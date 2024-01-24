<?php

namespace Reach\LocaleLander;

use Statamic\Providers\AddonServiceProvider;
use Illuminate\Routing\Router;
use Reach\LocaleLander\Http\Middleware\HandleLocaleRedirection;

class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon()
    {
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('statamic.web', HandleLocaleRedirection::class);
        // $router->prependMiddlewareToGroup('web', HandleLocaleRedirection::class);
    }
}
