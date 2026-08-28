---
title: Getting Started
nav_order: 2
---

# Getting Started

Thyme boots up similar to the existing [Acorn bootup sequence](https://roots.io/acorn/docs/installation/#booting-acorn), with one extra thing: the `ThymeServiceProvider` needs to be registered as a provider.

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