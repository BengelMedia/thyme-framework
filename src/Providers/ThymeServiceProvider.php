<?php

namespace Thyme\Framework\Providers;

use Illuminate\Support\ServiceProvider;

class ThymeServiceProvider extends ServiceProvider
{
    public function register()
    {
        if (function_exists('do_action')) {
            do_action('registering_thyme');
        }

        $this->app->register(PostTypeServiceProvider::class);
        $this->app->register(OptionsPageServiceProvider::class);
    }
}
