<?php

namespace Thyme\Framework\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Roots\Acorn\Console\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakePostTypeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:post-type';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new custom post type class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'PostType';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/post-type.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\PostTypes';
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
            ->replaceSingular($stub, $this->getSingular($className))
            ->replacePlural($stub, $this->getPlural($className))
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
     * Replace the singular label placeholder in the stub.
     */
    protected function replaceSingular(string $stub, string $singular): string
    {
        return str_replace('DummySingular', $singular, $stub);
    }

    /**
     * Replace the plural label placeholder in the stub.
     */
    protected function replacePlural(string $stub, string $plural): string
    {
        return str_replace('DummyPlural', $plural, $stub);
    }

    /**
     * Replace the icon placeholder in the stub.
     */
    protected function replaceIcon(string $stub, ?string $icon): string
    {
        return str_replace('DummyIcon', $icon ?? 'dashicons-admin-post', $stub);
    }

    /**
     * Determine the post type slug.
     */
    protected function getSlug(string $className): string
    {
        if ($this->option('slug')) {
            return $this->option('slug');
        }

        return $this->slugify($className);
    }

    /**
     * Determine the singular label.
     */
    protected function getSingular(string $className): string
    {
        if ($this->option('singular')) {
            return $this->option('singular');
        }

        return $this->titleCase($className);
    }

    /**
     * Determine the plural label.
     */
    protected function getPlural(string $className): string
    {
        if ($this->option('plural')) {
            return $this->option('plural');
        }

        return $this->getSingular($className) . 's';
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
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the post type already exists'],
            ['slug', null, InputOption::VALUE_REQUIRED, 'The WordPress post type slug'],
            ['singular', null, InputOption::VALUE_REQUIRED, 'The singular label'],
            ['plural', null, InputOption::VALUE_REQUIRED, 'The plural label'],
            ['icon', null, InputOption::VALUE_REQUIRED, 'The dashicon class'],
        ];
    }
}
