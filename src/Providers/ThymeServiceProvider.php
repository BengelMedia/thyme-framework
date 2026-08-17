<?php

namespace Thyme\Framework\Providers;

use Illuminate\Support\ServiceProvider;

class ThymeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(PostTypeServiceProvider::class);
    }
}
