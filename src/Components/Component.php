<?php

namespace Thyme\Framework\Components;

use Thyme\Framework\Contracts\HasRegistration;

class Component implements HasRegistration
{
    protected array $data = [];

    public function __construct(
        array $data = []
    ) {
        $this->data = $data;
    }

    public static function render(array $data = []): void
    {
        (new self($data))->renderComponent();
    }

    public function renderComponent(): void
    {
        $directory = get_stylesheet_directory().'/Components/'.class_basename($this);

        $name = class_basename($this);

        $candidates = [
            $name.'.blade.php',
            ucfirst($name).'.blade.php',
            strtolower($name).'.blade.php',
        ];

        $viewPath = null;

        foreach ($candidates as $candidate) {
            $path = $directory.'/'.$candidate;

            if (file_exists($path)) {
                $viewPath = $path;

                break;
            }
        }

        if ($viewPath === null) {
            return;
        }

        echo view($viewPath, [
            'component' => $this,
            ...$this->data,
        ]);

    }

    public function register(): void {}
}
