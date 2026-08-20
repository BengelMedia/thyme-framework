<?php

declare(strict_types=1);

use Extended\ACF\Fields\Text;
use Thyme\Framework\PostType\PostType;

beforeEach(function () {
    reset_registered_post_types();
    reset_registered_field_groups();
    reset_acf_field_keys();
});

class BasicEvent extends PostType
{
    public function slug(): string
    {
        return 'event';
    }
}

class CustomEvent extends PostType
{
    public function slug(): string
    {
        return 'custom_event';
    }

    public function singular(): string
    {
        return 'Custom Event';
    }

    public function plural(): string
    {
        return 'Custom Events';
    }

    public function icon(): string
    {
        return 'dashicons-calendar';
    }

    public function supports(): array
    {
        return ['title', 'editor', 'thumbnail', 'excerpt'];
    }

    public function rewriteSlug(): string
    {
        return 'events';
    }
}

class EventWithFields extends PostType
{
    public function slug(): string
    {
        return 'event_with_fields';
    }

    public function fields(): array
    {
        return [
            Text::make('Heading', 'heading')->required(),
            Text::make('Subheading', 'subheading'),
        ];
    }
}

class EventWithEmptyFields extends PostType
{
    public function slug(): string
    {
        return 'empty_event';
    }

    public function fields(): array
    {
        return [];
    }
}

it('returns an empty array of fields by default', function () {
    $event = new BasicEvent;

    expect($event->fields())->toBe([]);
});

it('registers a post type with default values', function () {
    $event = new BasicEvent;
    $event->register();

    expect(registered_post_type('event'))->not->toBeNull()
        ->and(registered_post_type('event')['labels']['name'])->toBe('Events')
        ->and(registered_post_type('event')['labels']['singular_name'])->toBe('Event')
        ->and(registered_post_type('event')['menu_icon'])->toBe('dashicons-admin-post')
        ->and(registered_post_type('event')['supports'])->toBe(['title', 'editor', 'thumbnail'])
        ->and(registered_post_type('event')['rewrite']['slug'])->toBe('events');
});

it('registers a post type with custom values', function () {
    $event = new CustomEvent;
    $event->register();

    expect(registered_post_type('custom_event'))->not->toBeNull()
        ->and(registered_post_type('custom_event')['labels']['name'])->toBe('Custom Events')
        ->and(registered_post_type('custom_event')['labels']['singular_name'])->toBe('Custom Event')
        ->and(registered_post_type('custom_event')['menu_icon'])->toBe('dashicons-calendar')
        ->and(registered_post_type('custom_event')['supports'])->toBe(['title', 'editor', 'thumbnail', 'excerpt'])
        ->and(registered_post_type('custom_event')['rewrite']['slug'])->toBe('events');
});

it('throws on slugs longer than 20 characters', function () {
    $longSlug = new class extends PostType
    {
        public function slug(): string
        {
            return 'this_post_type_slug_is_way_too_long';
        }
    };

    $longSlug->register();
})->throws(InvalidArgumentException::class, 'must be 20 characters or fewer');

it('throws on slugs with uppercase characters', function () {
    $uppercase = new class extends PostType
    {
        public function slug(): string
        {
            return 'Event';
        }
    };

    $uppercase->register();
})->throws(InvalidArgumentException::class, 'must be lowercase');

it('throws on slugs with invalid characters', function () {
    $invalid = new class extends PostType
    {
        public function slug(): string
        {
            return 'my post type';
        }
    };

    $invalid->register();
})->throws(InvalidArgumentException::class, 'may only contain lowercase letters');

it('registers the field group for the post type', function () {
    $event = new EventWithFields;
    $event->register();

    $groups = registered_field_groups();

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['title'])->toBe('Event_with_fields')
        ->and($groups[0]['location'])->toBe([
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'event_with_fields'],
            ],
        ])
        ->and($groups[0]['fields'])->toHaveCount(2)
        ->and($groups[0]['fields'][0]['label'])->toBe('Heading')
        ->and($groups[0]['fields'][0]['name'])->toBe('heading')
        ->and($groups[0]['fields'][1]['label'])->toBe('Subheading')
        ->and($groups[0]['fields'][1]['name'])->toBe('subheading');
});

it('does not register a field group when there are no fields', function () {
    $event = new EventWithEmptyFields;
    $event->register();

    expect(registered_field_groups())->toBeEmpty();
});
