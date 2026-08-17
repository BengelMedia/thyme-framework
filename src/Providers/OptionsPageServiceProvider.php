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
    /**
     * OptionsPage classes that should be registered.
     *
     * @var class-string<OptionsPage>[]
     */
    protected array $optionsPages = [];

    public function register(): void
    {
        $this->app->singleton(OptionsPageRegistrar::class, function () {
            $registrar = new OptionsPageRegistrar;

            $registrar->addMany($this->optionsPages);

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
