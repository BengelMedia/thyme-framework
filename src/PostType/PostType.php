<?php

namespace Thyme\Framework\PostType;

use InvalidArgumentException;
use Thyme\Framework\Icons\DashIcons;
use Thyme\Framework\Models\Post;

/**
 * Base class for registering WordPress custom post types.
 *
 * Extend this class, implement slug(), and optionally override
 * singular()/plural()/labels()/args() to register a custom post type.
 *
 * @example
 * class Event extends PostType
 * {
 *     public function slug(): string { return 'event'; }
 *     public function singular(): string { return 'Event'; }
 *     public function plural(): string { return 'Events'; }
 *     public function icon(): string { return 'dashicons-calendar'; }
 * }
 */
abstract class PostType
{
    /**
     * The WordPress post type slug. Must be unique and <= 20 characters.
     */
    abstract public function slug(): string;

    /**
     * The singular human-readable label.
     */
    public function singular(): string
    {
        return ucfirst($this->slug());
    }

    /**
     * The plural human-readable label.
     */
    public function plural(): string
    {
        return $this->singular().'s';
    }

    /**
     * The icon used in the admin menu.
     */
    public function icon(): string
    {
        return DashIcons::AdminPost->value;
    }

    /**
     * Features supported by this post type.
     */
    public function supports(): array
    {
        return ['title', 'editor', 'thumbnail'];
    }

    /**
     * The URL rewrite slug. Defaults to the plural label.
     */
    public function rewriteSlug(): string
    {
        return sanitize_title($this->plural());
    }

    /**
     * WordPress labels for the post type.
     */
    public function labels(): array
    {
        $singular = $this->singular();
        $plural = $this->plural();

        return [
            'name' => $plural,
            'singular_name' => $singular,
            'add_new' => 'Add New',
            'add_new_item' => "Add New {$singular}",
            'edit_item' => "Edit {$singular}",
            'new_item' => "New {$singular}",
            'view_item' => "View {$singular}",
            'view_items' => "View {$plural}",
            'search_items' => "Search {$plural}",
            'not_found' => "No {$plural} found.",
            'not_found_in_trash' => "No {$plural} found in trash.",
            'parent_item_colon' => "Parent {$singular}:",
            'all_items' => "All {$plural}",
            'archives' => "{$singular} Archives",
            'attributes' => "{$singular} Attributes",
            'insert_into_item' => "Insert into {$singular}",
            'uploaded_to_this_item' => "Uploaded to this {$singular}",
            'featured_image' => 'Featured Image',
            'set_featured_image' => 'Set featured image',
            'remove_featured_image' => 'Remove featured image',
            'use_featured_image' => 'Use as featured image',
            'menu_name' => $plural,
            'filter_items_list' => "Filter {$plural} list",
            'items_list_navigation' => "{$plural} list navigation",
            'items_list' => "{$plural} list",
        ];
    }

    /**
     * Full argument list passed to register_post_type().
     */
    public function args(): array
    {
        return [
            'labels' => $this->labels(),
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'query_var' => true,
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => null,
            'menu_icon' => $this->icon(),
            'supports' => $this->supports(),
            'rewrite' => ['slug' => $this->rewriteSlug()],
        ];
    }

    /**
     * Optional Eloquent model class used for this post type.
     *
     * @return class-string<Post>|null
     */
    public function model(): ?string
    {
        return null;
    }

    /**
     * Start a query for posts of this type.
     */
    public function query()
    {
        $model = $this->model() ?? Post::class;

        return $model::query()->ofType($this->slug());
    }

    /**
     * Register the post type with WordPress.
     */
    public function register(): void
    {
        if (! function_exists('register_post_type')) {
            return;
        }

        $slug = $this->slug();

        $this->validateSlug($slug);

        register_post_type($slug, $this->args());
    }

    /**
     * Validate that the slug is safe for WordPress.
     */
    protected function validateSlug(string $slug): void
    {
        if (strlen($slug) > 20) {
            throw new InvalidArgumentException(
                "Post type slug '{$slug}' must be 20 characters or fewer."
            );
        }

        if ($slug !== strtolower($slug)) {
            throw new InvalidArgumentException(
                "Post type slug '{$slug}' must be lowercase."
            );
        }

        if (preg_match('/[^a-z0-9_-]/', $slug)) {
            throw new InvalidArgumentException(
                "Post type slug '{$slug}' may only contain lowercase letters, numbers, hyphens and underscores."
            );
        }
    }
}
