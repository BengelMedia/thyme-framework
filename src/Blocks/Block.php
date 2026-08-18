<?php

namespace Thyme\Framework\Blocks;

use Extended\ACF\Location;
use Thyme\Framework\Icons\DashIcons;

class Block
{
    protected ?string $title = null;

    protected string $name = 'block';

    protected ?string $description = null;

    protected ?string $category = null;

    protected DashIcons $icon = DashIcons::MediaAudio;

    public function getTitle(): string
    {
        return $this->title ?? ucfirst($this->getName());
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }

    public function getCategory(): string
    {
        return $this->category ?? 'Default';
    }

    public function getIcon(): string
    {
        return $this->icon->value;
    }

    public function fields(): array
    {
        return [];
    }

    public function render(): void
    {
        echo \Roots\view(sprintf(
            'resources/views/blocks/%s.blade.php', $this->getName()
        ));
    }

    public function register(): void
    {
        if (! function_exists('acf_register_block_type')) {
            return;
        }

        $registered = acf_register_block_type([
            'name' => sprintf('thyme/%s', $this->getName()),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'category' => $this->getCategory(),
            'icon' => $this->getIcon(),
            'api_version' => 3,
            'supports' => [
                'html' => false,
                'jsx' => true,
            ],
            'attributes' => [
                'style' => [
                    'type' => 'object',
                ],
            ],
            'render_callback' => [$this, 'render'],
        ]);

        register_extended_field_group([
            'title' => $this->getTitle(),
            'fields' => $this->fields(),
            'location' => [
                Location::where('block', $registered['name']),
            ],
        ]);
    }
}
