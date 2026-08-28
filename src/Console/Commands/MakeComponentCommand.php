<?php

namespace Thyme\Framework\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Roots\Acorn\Console\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeComponentCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:component';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Thyme component';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Component';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/component.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\\Components\\'.Str::studly($this->getNameInput());
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return base_path(str_replace('\\', '/', $name)).'.php';
    }

    /**
     * Execute the console command.
     *
     * @return bool|int|null
     *
     * @throws FileNotFoundException
     */
    public function handle()
    {
        $status = parent::handle();

        if ($status !== false) {
            $this->createSupportFiles();
        }

        return $status;
    }

    /**
     * Create the Blade template for the component.
     */
    protected function createSupportFiles(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $directory = dirname($this->getPath($name));
        $className = class_basename($name);

        foreach ($this->supportFiles() as $file => $stub) {
            $path = $directory.'/'.str_replace('DummyName', $className, $file);

            if ($this->files->exists($path)) {
                continue;
            }

            $contents = str_replace(
                ['DummyName', 'DummyClass'],
                [$this->kebabName($className), $className],
                $this->files->get($stub),
            );

            $this->files->put($path, $contents);
        }
    }

    /**
     * The support files to generate alongside the component class.
     *
     * @return array<string, string>
     */
    protected function supportFiles(): array
    {
        return [
            'DummyName.blade.php' => __DIR__.'/stubs/component.blade.stub',
        ];
    }

    /**
     * Convert a class name to a kebab-cased CSS name.
     */
    protected function kebabName(string $value): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $value));
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the component already exists'],
        ];
    }
}
