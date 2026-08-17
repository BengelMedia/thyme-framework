<?php

declare(strict_types=1);

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WYSIWYGEditor;
use Thyme\Framework\Options\OptionsPage;

beforeEach(function () {
    reset_registered_options_pages();
    reset_registered_field_groups();
});

class BasicCookie extends OptionsPage
{
    public function slug(): string
    {
        return 'cookie';
    }
}

class CustomCookie extends OptionsPage
{
    public function slug(): string
    {
        return 'cookie';
    }

    public function pageTitle(): string
    {
        return 'Cookie Bar';
    }

    public function menuTitle(): string
    {
        return 'Cookies';
    }

    public function icon(): string
    {
        return 'dashicons-star-filled';
    }

    public function position(): ?int
    {
        return 21;
    }

    public function capability(): string
    {
        return 'manage_options';
    }

    public function updateButton(): ?string
    {
        return 'Save cookies';
    }
}

class NestedCookie extends OptionsPage
{
    public function slug(): string
    {
        return 'cookie';
    }

    public function parentSlug(): ?string
    {
        return 'options-general.php';
    }
}

class CookieWithFields extends OptionsPage
{
    public function slug(): string
    {
        return 'cookie';
    }

    public function fields(): array
    {
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

class CookieWithCustomFieldGroupTitle extends CookieWithFields
{
    public function fieldGroupTitle(): string
    {
        return 'Custom Cookie Group';
    }
}

it('registers an options page with default values', function () {
    $cookie = new BasicCookie;
    $cookie->register();

    expect(registered_options_page('cookie'))->not->toBeNull()
        ->and(registered_options_page('cookie')['page_title'])->toBe('Cookie')
        ->and(registered_options_page('cookie')['menu_title'])->toBe('Cookie')
        ->and(registered_options_page('cookie')['icon_url'])->toBe('dashicons-admin-generic')
        ->and(registered_options_page('cookie')['capability'])->toBe('edit_themes')
        ->and(registered_options_page('cookie')['redirect'])->toBe(true)
        ->and(registered_options_page('cookie')['autoload'])->toBe(true)
        ->and(registered_options_page('cookie'))->not->toHaveKey('position')
        ->and(registered_options_page('cookie'))->not->toHaveKey('parent_slug');
});

it('registers an options page with custom values', function () {
    $cookie = new CustomCookie;
    $cookie->register();

    expect(registered_options_page('cookie'))->not->toBeNull()
        ->and(registered_options_page('cookie')['page_title'])->toBe('Cookie Bar')
        ->and(registered_options_page('cookie')['menu_title'])->toBe('Cookies')
        ->and(registered_options_page('cookie')['icon_url'])->toBe('dashicons-star-filled')
        ->and(registered_options_page('cookie')['position'])->toBe(21)
        ->and(registered_options_page('cookie')['capability'])->toBe('manage_options')
        ->and(registered_options_page('cookie')['update_button'])->toBe('Save cookies');
});

it('registers as a sub page when a parent slug is set', function () {
    $cookie = new NestedCookie;
    $cookie->register();

    expect(registered_options_sub_page('cookie'))->not->toBeNull()
        ->and(registered_options_sub_page('cookie')['parent_slug'])->toBe('options-general.php')
        ->and(registered_options_page('cookie'))->toBeNull();
});

it('registers the field group for the options page', function () {
    $cookie = new CookieWithFields;
    $cookie->register();

    $groups = registered_field_groups();

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['title'])->toBe('Cookie')
        ->and($groups[0]['location'])->toBe([
            [
                ['param' => 'options_page', 'operator' => '==', 'value' => 'cookie'],
            ],
        ])
        ->and($groups[0]['fields'])->toHaveCount(2)
        ->and($groups[0]['fields'][0]['name'])->toBe('cookie_text')
        ->and($groups[0]['fields'][0]['label'])->toBe('Text')
        ->and($groups[0]['fields'][1]['name'])->toBe('cookie_label')
        ->and($groups[0]['fields'][1]['label'])->toBe('Label');
});

it('does not register a field group when there are no fields', function () {
    $cookie = new BasicCookie;
    $cookie->register();

    expect(registered_field_groups())->toBeEmpty();
});

it('uses the custom field group title when provided', function () {
    $cookie = new CookieWithCustomFieldGroupTitle;
    $cookie->register();

    $groups = registered_field_groups();

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['title'])->toBe('Custom Cookie Group');
});

it('throws on slugs with uppercase characters', function () {
    $uppercase = new class extends OptionsPage
    {
        public function slug(): string
        {
            return 'Cookie';
        }
    };

    $uppercase->register();
})->throws(InvalidArgumentException::class, 'must be lowercase');

it('throws on slugs with invalid characters', function () {
    $invalid = new class extends OptionsPage
    {
        public function slug(): string
        {
            return 'my options page';
        }
    };

    $invalid->register();
})->throws(InvalidArgumentException::class, 'may only contain lowercase letters');
