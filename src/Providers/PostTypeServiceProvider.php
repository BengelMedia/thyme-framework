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
    /**
     * PostType classes that should be registered.
     *
     * @var class-string<\Thyme\Framework\PostType\PostType>[]
     */
    protected array $postTypes = [];

    public function register(): void
    {
        $this->app->singleton(PostTypeRegistrar::class, function () {
            $registrar = new PostTypeRegistrar();

            $registrar->addMany($this->postTypes);

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
