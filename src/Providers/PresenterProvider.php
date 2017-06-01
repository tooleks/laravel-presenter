<?php

namespace Tooleks\Laravel\Presenter\Providers;

use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;

/**
 * Class PresenterProvider.
 *
 * @package Tooleks\Laravel\Presenter\Providers
 * @author Oleksandr Tolochko <tooleks@gmail.com>
 */
class PresenterProvider extends ServiceProvider
{
    /**
     * Bootstrap package services.
     *
     * @return void
     */
    public function boot()
    {
        $container = $this->app;

        Collection::macro('present', function ($presenterClass) use ($container) {
            return $this->map(function ($wrappedModel) use ($container, $presenterClass) {
                return $container->make($presenterClass)->setWrappedModel($wrappedModel);
            });
        });
    }
}
