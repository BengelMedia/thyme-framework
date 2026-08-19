<?php

namespace Thyme\Framework\Console\Commands;

use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Str;
use Roots\Acorn\Console\Commands\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class MakeBlockCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:block';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new ACF block';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Block';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/stubs/block.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\\Blocks\\' . Str::studly($this->getNameInput());
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

        return base_path(str_replace('\\', '/', $name)) . '.php';
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
     * Create the blade template, style and script files for the block.
     */
    protected function createSupportFiles(): void
    {
        $name = $this->qualifyClass($this->getNameInput());
        $directory = dirname($this->getPath($name));
        $blockName = $this->getBlockName(class_basename($name));
        $title = $this->getTitle(class_basename($name));

        foreach ($this->supportFiles() as $file => $stub) {
            $path = $directory . '/' . str_replace('DummyName', $blockName, $file);

            if ($this->files->exists($path)) {
                continue;
            }

            $contents = str_replace(
                ['DummyName', 'DummyTitle'],
                [$blockName, $title],
                $this->files->get($stub),
            );

            $this->files->put($path, $contents);
        }
    }

    /**
     * The support files to generate alongside the block class.
     *
     * @return array<string, string>
     */
    protected function supportFiles(): array
    {
        return [
            'DummyName.blade.php' => __DIR__ . '/stubs/block.blade.stub',
            'DummyName.ts' => __DIR__ . '/stubs/block.ts.stub',
            'DummyName.css' => __DIR__ . '/stubs/block.css.stub',
        ];
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

        return $this->replaceIcon(
            $this->replaceCategory(
                $this->replaceDescription(
                    $this->replaceTitle(
                        $this->replaceName($stub, $this->getBlockName($className)),
                        $this->getTitle($className),
                    ),
                    $this->getBlockDescription(),
                ),
                $this->getCategory(),
            ),
            $this->option('icon'),
            $className,
        );
    }

    /**
     * Replace the block name placeholder in the stub.
     */
    protected function replaceName(string $stub, string $blockName): string
    {
        return str_replace('DummyName', $blockName, $stub);
    }

    /**
     * Replace the title placeholder in the stub.
     */
    protected function replaceTitle(string $stub, string $title): string
    {
        return str_replace('DummyTitle', $title, $stub);
    }

    /**
     * Replace the description placeholder in the stub.
     */
    protected function replaceDescription(string $stub, ?string $description): string
    {
        return str_replace('DummyDescription', $description ?? '', $stub);
    }

    /**
     * Replace the category placeholder in the stub.
     */
    protected function replaceCategory(string $stub, ?string $category): string
    {
        return str_replace('DummyCategory', $category ?? 'Default', $stub);
    }

    /**
     * Replace the icon placeholder in the stub.
     */
    protected function replaceIcon(string $stub, ?string $icon, string $className): string
    {
        return $this->replaceAssetPath(
            str_replace('DummyIcon', $icon ?? 'MediaAudio', $stub),
            $className,
        );
    }

    /**
     * Replace the asset path placeholder in the stub.
     */
    protected function replaceAssetPath(string $stub, string $className): string
    {
        return str_replace(
            'DummyAssetPath',
            'Blocks/' . $className . '/' . $this->getBlockName($className),
            $stub,
        );
    }

    /**
     * Determine the ACF block name.
     */
    protected function getBlockName(string $className): string
    {
        if ($this->option('name')) {
            return $this->option('name');
        }

        return $this->slugify($className);
    }

    /**
     * Determine the block title.
     */
    protected function getTitle(string $className): string
    {
        if ($this->option('title')) {
            return $this->option('title');
        }

        return $this->titleCase($className);
    }

    /**
     * Determine the block description.
     */
    public function getBlockDescription(): string
    {
        return $this->option('description');
    }

    /**
     * Determine the block category.
     */
    protected function getCategory(): ?string
    {
        return $this->option('category');
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
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the block already exists'],
            ['name', null, InputOption::VALUE_REQUIRED, 'The ACF block name'],
            ['title', null, InputOption::VALUE_REQUIRED, 'The block title'],
            ['description', null, InputOption::VALUE_REQUIRED, 'The block description'],
            ['category', null, InputOption::VALUE_REQUIRED, 'The block category'],
            ['icon', null, InputOption::VALUE_REQUIRED, 'The DashIcons enum case name'],
        ];
    }
}
