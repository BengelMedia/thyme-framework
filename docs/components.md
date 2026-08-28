---
title: Components
layout: default
nav_order: 6
---

# Components

Components are plain PHP classes that render a Blade template. They are auto-discovered from the theme's `Components` directory and registered through the `ComponentRegistrar`. Generate a component class with:

```bash
wp acorn make:component Button
```

The generated class extends `Thyme\Framework\Components\Component`. Pass data into the constructor and call `renderComponent()` to render its Blade view.

```php
<?php

namespace App\Components\Button;

use Thyme\Framework\Components\Component;

class Button extends Component
{
}
```

Render the component anywhere in your theme:

```php
use App\Components\Button\Button;

Button::render(['label' => 'Click Me']);
```

The component renders through `Components/Button/Button.blade.php`. All data passed to the constructor is available in the template, along with the `$component` variable.

```blade
{% raw %}<div class="button-component">
    <button>{{ $label ?? 'Submit' }}</button>
</div>{% endraw %}
```

You can also override `renderComponent()` in the class for custom rendering logic.