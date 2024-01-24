<?php

namespace Reach\LocaleLander;

use Reach\LocaleLander\Http\Middleware\HandleLocaleRedirection;
use Statamic\Providers\AddonServiceProvider;
use Statamic\Statamic;

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

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/locale-lander.php' => config_path('statamic/locale-lander.php'),
            ], 'locale-lander');
        }
    }

    protected function bootPublishAfterInstall()
    {
        Statamic::afterInstalled(function ($command) {
            $command->call('vendor:publish', [
                '--tag' => $this->getAddon()->slug(),
                '--force' => true,
            ]);
        });

        return $this;
    }
}
