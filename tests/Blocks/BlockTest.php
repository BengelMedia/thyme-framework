<?php

declare(strict_types=1);

use Extended\ACF\Fields\Text;
use Thyme\Framework\Blocks\Block;
use Thyme\Framework\Icons\DashIcons;

beforeEach(function () {
    reset_registered_blocks();
    reset_registered_field_groups();
    reset_acf_field_keys();
});

class BasicBlock extends Block {}

class CustomBlock extends Block
{
    protected ?string $title = 'Hero Section';

    protected string $name = 'hero';

    protected ?string $description = 'A hero block';

    protected ?string $category = 'Layout';

    protected DashIcons $icon = DashIcons::Layout;
}

class HeroWithFields extends Block
{
    protected string $name = 'hero';

    public function fields(): array
    {
        return [
            Text::make('Heading', 'heading')->required(),
        ];
    }
}

it('returns default block metadata', function () {
    $block = new BasicBlock;

    expect($block->getTitle())->toBe('Block')
        ->and($block->getName())->toBe('block')
        ->and($block->getDescription())->toBe('')
        ->and($block->getCategory())->toBe('Default')
        ->and($block->getIcon())->toBe(DashIcons::MediaAudio->value);
});

it('returns custom block metadata', function () {
    $block = new CustomBlock;

    expect($block->getTitle())->toBe('Hero Section')
        ->and($block->getName())->toBe('hero')
        ->and($block->getDescription())->toBe('A hero block')
        ->and($block->getCategory())->toBe('Layout')
        ->and($block->getIcon())->toBe(DashIcons::Layout->value);
});

it('registers a block with default values', function () {
    $block = new BasicBlock;
    $block->register();

    expect(registered_block('thyme/block'))->not->toBeNull()
        ->and(registered_block('thyme/block')['title'])->toBe('Block')
        ->and(registered_block('thyme/block')['description'])->toBe('')
        ->and(registered_block('thyme/block')['category'])->toBe('Default')
        ->and(registered_block('thyme/block')['icon'])->toBe(DashIcons::MediaAudio->value)
        ->and(registered_block('thyme/block')['api_version'])->toBe(3)
        ->and(registered_block('thyme/block')['supports'])->toBe([
            'html' => false,
            'jsx' => true,
        ])
        ->and(registered_block('thyme/block')['render_callback'])->toBe([$block, 'render']);
});

it('registers a block with custom values', function () {
    $block = new CustomBlock;
    $block->register();

    expect(registered_block('thyme/hero'))->not->toBeNull()
        ->and(registered_block('thyme/hero')['title'])->toBe('Hero Section')
        ->and(registered_block('thyme/hero')['description'])->toBe('A hero block')
        ->and(registered_block('thyme/hero')['category'])->toBe('Layout')
        ->and(registered_block('thyme/hero')['icon'])->toBe(DashIcons::Layout->value);
});

it('registers the field group for the block', function () {
    $block = new HeroWithFields;
    $block->register();

    $groups = registered_field_groups();

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['title'])->toBe('Hero')
        ->and($groups[0]['location'])->toBe([
            [
                ['param' => 'block', 'operator' => '==', 'value' => 'thyme/hero'],
            ],
        ])
        ->and($groups[0]['fields'])->toHaveCount(1)
        ->and($groups[0]['fields'][0]['name'])->toBe('heading')
        ->and($groups[0]['fields'][0]['label'])->toBe('Heading');
});

it('does not register a field group when there are no fields', function () {
    $block = new BasicBlock;
    $block->register();

    expect(registered_field_groups())->toBeEmpty();
});
