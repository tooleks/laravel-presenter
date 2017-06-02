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
        $this->registerCollectionPresentMacros();
    }

    /**
     * Register collection present macros method.
     *
     * @return void
     */
    protected function registerCollectionPresentMacros()
    {
        $app = $this->app; // The application container instance.

        Collection::macro('present', function ($presenterClass) use ($app) {
            return $this->map(function ($wrappedModel) use ($app, $presenterClass) {
                return $app->make($presenterClass)->setWrappedModel($wrappedModel);
            });
        });
    }
}
