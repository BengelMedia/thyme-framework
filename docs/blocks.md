---
title: Blocks
nav_order: 5
---

# Blocks

Blocks are automatically discovered from the theme's `app/Blocks` directory and registered on the `acf/init` action. Generate a block class with:

```bash
wp acorn make:block Hero --title="Hero" --description="A hero banner" --category="design" --icon=CoverImage
```

The generated class extends `Thyme\Framework\Blocks\Block`. Override its metadata, `fields()`, `scripts()`, `styles()`, and `render()` methods as needed. Blocks are registered with the `thyme/` namespace, use ACF API version 3, and disable HTML editing by default.

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