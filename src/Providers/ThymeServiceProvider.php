<?php

namespace Thyme\Framework\Providers;

use Illuminate\Support\ServiceProvider;

class ThymeServiceProvider extends ServiceProvider
{
    protected array $providers = [
        PostTypeServiceProvider::class,
        OptionsPageServiceProvider::class,
        BlockServiceProvider::class,
        DirectivesServiceProvider::class,
        ComponentServiceProvider::class,
    ];

    public function register()
    {
        if (function_exists('do_action')) {
            do_action('registering_thyme');
        }

        collect($this->providers)
            ->each(fn ($provider) => $this->app->register($provider));
    }
}
