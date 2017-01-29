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
        Collection::macro('present', function ($presenterClass) {
            return $this->map(function ($presentee) use ($presenterClass) {
                return new $presenterClass($presentee);
            });
        });
    }
}
