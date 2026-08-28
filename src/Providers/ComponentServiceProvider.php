<?php

namespace Thyme\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Thyme\Framework\Components\ComponentRegistrar;
use Thyme\Framework\Console\Commands\MakeComponentCommand;

class ComponentServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ComponentRegistrar::class, function () {
            $registrar = new ComponentRegistrar;

            $rootNamespace = $this->app->getNamespace();
            $rootDirectory = get_stylesheet_directory();

            collect(glob($rootDirectory.'/Components/*/*.php'))->each(function ($file) use ($rootNamespace, $registrar) {
                if (str_ends_with($file, '.blade.php')) {
                    return;
                }

                $className = $rootNamespace.'Components\\'.basename(dirname($file)).'\\'.basename($file, '.php');
                $registrar->add($className);
            });

            return $registrar;
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeComponentCommand::class,
        ]);

        $this->app->make(ComponentRegistrar::class)->registerOnInit();
    }
}
