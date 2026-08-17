<?php

namespace Thyme\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Thyme\Framework\Console\Commands\MakeOptionsPageCommand;
use Thyme\Framework\Options\OptionsPage;
use Thyme\Framework\Options\OptionsPageRegistrar;

/**
 * Acorn service provider for the options page system.
 *
 * Register this provider in your theme/plugin config/app.php providers array.
 * Then publish/configure which OptionsPage classes should be loaded.
 */
class OptionsPageServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OptionsPageRegistrar::class, function () {
            $registrar = new OptionsPageRegistrar;

            $rootNamespace = $this->app->getNamespace();
            $rootDirectory = get_template_directory();

            collect(glob($rootDirectory.'/app/OptionsPages/*.php', GLOB_BRACE))->each(function ($file) use ($rootNamespace, $registrar) {
                $className = $rootNamespace.'OptionsPages\\'.basename($file, '.php');
                $registrar->add($className);
            });

            return $registrar;
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakeOptionsPageCommand::class,
        ]);

        $this->app->make(OptionsPageRegistrar::class)->registerOnInit();
    }
}
