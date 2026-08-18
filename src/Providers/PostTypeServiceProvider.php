<?php

namespace Thyme\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Thyme\Framework\Console\Commands\MakePostTypeCommand;
use Thyme\Framework\PostType\PostTypeRegistrar;

/**
 * Acorn service provider for the post type system.
 *
 * Register this provider in your theme/plugin config/app.php providers array.
 * Then publish/configure which PostType classes should be loaded.
 */
class PostTypeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PostTypeRegistrar::class, function () {
            $registrar = new PostTypeRegistrar;

            $rootNamespace = $this->app->getNamespace();
            $rootDirectory = get_stylesheet_directory();

            collect(glob($rootDirectory.'/app/PostTypes/*.php', GLOB_BRACE))->each(function ($file) use ($rootNamespace, $registrar) {
                $className = $rootNamespace.'PostType\\'.basename($file, '.php');
                $registrar->add($className);
            });

            return $registrar;
        });
    }

    public function boot(): void
    {
        $this->commands([
            MakePostTypeCommand::class,
        ]);

        $this->app->make(PostTypeRegistrar::class)->registerOnInit();
    }
}
