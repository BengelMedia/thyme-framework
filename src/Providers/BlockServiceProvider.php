<?php

namespace Thyme\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Thyme\Framework\Blocks\BlockRegistrar;
use Thyme\Framework\Console\Commands\MakeBlockCommand;

class BlockServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BlockRegistrar::class, function () {
            $registrar = new BlockRegistrar;

            $rootNamespace = $this->app->getNamespace();
            $rootDirectory = get_stylesheet_directory();

            collect(glob($rootDirectory.'/app/Blocks/*.php', GLOB_BRACE))->each(function ($file) use ($rootNamespace, $registrar) {
                $className = $rootNamespace.'Blocks\\'.basename($file, '.php');
                $registrar->add($className);
            });

            return $registrar;
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeBlockCommand::class,
        ]);

        $this->app->make(BlockRegistrar::class)->registerOnInit();
    }
}
