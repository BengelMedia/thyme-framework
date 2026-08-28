---
title: Post Types
layout: default
nav_order: 3
---

# Post Types

Registering post types is easy: extend the `PostType` class and describe the post type.

```php
use Thyme\Framework\PostType\PostType;
use Thyme\Framework\PostType\HasClassicEditor;
use Thyme\Framework\Icons\DashIcons;

class Event extends PostType {
    use HasClassicEditor;
    
    public function slug(): string { return 'event'; }
    public function singular(): string { return 'Event'; }
    public function plural(): string { return 'Events'; }
    public function icon(): string { return DashIcons::Calendar->value; }
}
```