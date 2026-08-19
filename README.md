# Thyme Framework
Thyme is a framework for wordpress block themes.
It's built around the principles Roots/Acorn already sets up for the users, with some extra goodies like Post Type registration right from your theme.

To boot it up it's similar to the existing [Acorn bootup sequence](https://roots.io/acorn/docs/installation/#booting-acorn) with one extra thing
```php
<?php

use Roots\Acorn\Application;
use Thyme\Framework\Providers\ThymeServiceProvider;
if (! class_exists(\Roots\Acorn\Application::class)) {
    wp_die(
        __('You need to install Acorn to use this site.', 'domain'),
        '',
        [
            'link_url' => 'https://roots.io/acorn/docs/installation/',
            'link_text' => __('Acorn Docs: Installation', 'domain'),
        ]
    );
}

add_action('after_setup_theme', function () {
    Application::configure()
        ->withProviders([
            App\Providers\ThemeServiceProvider::class,
            ThymeServiceProvider::class
        ])
        ->boot();
}, 0);
```


# Features:
## Easy post type registration
```php
use Thyme\Framework\PostType\PostType;
use Thyme\Framework\Icons\DashIcons;

class Event extends PostType {
    public function slug(): string { return 'event'; }
    public function singular(): string { return 'Event'; }
    public function plural(): string { return 'Events'; }
    public function icon(): string { return DashIcons::Calendar->value; }
}
```

## Easy options page registration
```php
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;

use Thyme\Framework\Icons\DashIcons;
use Thyme\Framework\Options\OptionsPage;
class Cookie extends OptionsPage  {
    public function slug(): string { return 'cookie'; }
    public function pageTitle(): string { return 'Cookie'; }
    public function icon(): string { return DashIcons::StarFilled->value; }
    public function position(): ?int { return 21; }

    public function fields(): array {
        return [
            WYSIWYGEditor::make('Text', 'cookie_text')
                ->helperText('Add the cookie disclaimer text.')
                ->required(),
            Text::make('Label', 'cookie_label')
                ->helperText('Add the button label.')
                ->required(),
        ];
    }
}
```
Any option page with fields will automatically have its ACF field group registered, located on the `options_page` matching its slug.

## Easy block registration

Blocks are automatically discovered from the theme's `app/Blocks` directory and registered on the `acf/init` action. Generate a block class with:

```bash
wp acorn make:block Hero --title="Hero" --description="A hero banner" --category="design" --icon=CoverImage
```

The generated class extends `Thyme\\Framework\\Blocks\\Block`. Override its metadata, `fields()`, `scripts()`, `styles()`, and `render()` methods as needed. Blocks are registered with the `thyme/` namespace, use ACF API version 3, and disable HTML editing by default.

```php
<?php

namespace App\Blocks;

use Extended\ACF\Fields\Text;
use Thyme\Framework\Blocks\Block;
use Thyme\Framework\Icons\DashIcons;

class Hero extends Block
{
    protected ?string $title = 'Hero';

    protected string $name = 'hero';

    protected ?string $description = 'A hero banner with a heading.';

    protected ?string $category = 'design';

    protected DashIcons $icon = DashIcons::CoverImage;

    public function fields(): array
    {
        return [
            Text::make('Heading', 'heading')->required(),
        ];
    }

    public function scripts(): array
    {
        return ['@vite:/resources/js/blocks/hero.js'];
    }

    public function styles(): array
    {
        return ['@vite:/resources/css/blocks/hero.css'];
    }
}
```

The block renders through `resources/views/blocks/hero.blade.php`. Asset paths prefixed with `@vite:/` are resolved through Vite; regular URLs are also supported. When `fields()` returns fields, Thyme automatically registers an ACF field group for the block.
