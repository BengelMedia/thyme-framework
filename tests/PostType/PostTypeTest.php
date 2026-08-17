<?php

declare(strict_types=1);

use Thyme\Framework\PostType\PostType;

beforeEach(function () {
    reset_registered_post_types();
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
