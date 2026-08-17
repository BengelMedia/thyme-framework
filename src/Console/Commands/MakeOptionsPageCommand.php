<?php

namespace Thyme\Framework\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Roots\Acorn\Console\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeOptionsPageCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:options-page';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new options page class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'OptionsPage';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/options-page.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\OptionsPages';
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $className = class_basename($name);

        return $this
            ->replaceSlug($stub, $this->getSlug($className))
            ->replacePageTitle($stub, $this->getPageTitle($className))
            ->replaceMenuTitle($stub, $this->getMenuTitle($className))
            ->replaceIcon($stub, $this->option('icon'));
    }

    /**
     * Replace the slug placeholder in the stub.
     */
    protected function replaceSlug(string $stub, string $slug): string
    {
        return str_replace('DummySlug', $slug, $stub);
    }

    /**
     * Replace the page title placeholder in the stub.
     */
    protected function replacePageTitle(string $stub, string $pageTitle): string
    {
        return str_replace('DummyPageTitle', $pageTitle, $stub);
    }

    /**
     * Replace the menu title placeholder in the stub.
     */
    protected function replaceMenuTitle(string $stub, string $menuTitle): string
    {
        return str_replace('DummyMenuTitle', $menuTitle, $stub);
    }

    /**
     * Replace the icon placeholder in the stub.
     */
    protected function replaceIcon(string $stub, ?string $icon): string
    {
        return str_replace('DummyIcon', $icon ?? 'dashicons-admin-generic', $stub);
    }

    /**
     * Determine the options page slug.
     */
    protected function getSlug(string $className): string
    {
        if ($this->option('slug')) {
            return $this->option('slug');
        }

        return $this->slugify($className);
    }

    /**
     * Determine the page title.
     */
    protected function getPageTitle(string $className): string
    {
        if ($this->option('title')) {
            return $this->option('title');
        }

        return $this->titleCase($className);
    }

    /**
     * Determine the menu title.
     */
    protected function getMenuTitle(string $className): string
    {
        return $this->option('menu-title') ?? $this->getPageTitle($className);
    }

    /**
     * Convert a class name to a URL-friendly slug.
     */
    protected function slugify(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $value));
    }

    /**
     * Convert a class name to a title-cased label.
     */
    protected function titleCase(string $value): string
    {
        return implode(' ', preg_split('/(?=[A-Z])/', $value, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the options page already exists'],
            ['slug', null, InputOption::VALUE_REQUIRED, 'The ACF options page slug'],
            ['title', null, InputOption::VALUE_REQUIRED, 'The page title'],
            ['menu-title', null, InputOption::VALUE_REQUIRED, 'The admin menu title'],
            ['icon', null, InputOption::VALUE_REQUIRED, 'The dashicon class'],
        ];
    }
}
