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
class Event extends \Thyme\Framework\PostType\PostType {
    public function slug(): string { return 'event'; }
    public function singular(): string { return 'Event'; }
    public function plural(): string { return 'Events'; }
    public function icon(): string { return 'dashicons-calendar'; }
}
```