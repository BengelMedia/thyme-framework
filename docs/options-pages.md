---
title: Options Pages
layout: default
nav_order: 4
---

# Options Pages

Register an ACF options page by extending the `OptionsPage` class. Any options page with fields will automatically have its ACF field group registered, located on the `options_page` matching its slug.

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