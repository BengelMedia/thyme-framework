<?php

declare(strict_types=1);

$GLOBALS['_registered_post_types'] = [];
$GLOBALS['_registered_actions'] = [];
$GLOBALS['_registered_options_pages'] = [];
$GLOBALS['_registered_options_sub_pages'] = [];
$GLOBALS['_registered_field_groups'] = [];

if (! function_exists('register_post_type')) {
    function register_post_type(string $post_type, array|string $args = []): void
    {
        $GLOBALS['_registered_post_types'][$post_type] = $args;
    }
}

if (! function_exists('add_action')) {
    function add_action(string $tag, callable $callback, int $priority = 10, int $accepted_args = 1): true
    {
        $GLOBALS['_registered_actions'][$tag][] = [
            'callback' => $callback,
            'priority' => $priority,
            'accepted_args' => $accepted_args,
        ];

        return true;
    }
}

if (! function_exists('sanitize_title')) {
    function sanitize_title(string $title): string
    {
        return strtolower(str_replace(' ', '-', $title));
    }
}

if (! function_exists('acf_add_options_page')) {
    function acf_add_options_page(array $args = []): void
    {
        $GLOBALS['_registered_options_pages'][$args['menu_slug']] = $args;
    }
}

if (! function_exists('acf_add_options_sub_page')) {
    function acf_add_options_sub_page(array $args = []): void
    {
        $GLOBALS['_registered_options_sub_pages'][$args['menu_slug']] = $args;
    }
}

if (! function_exists('register_field_group')) {
    function register_field_group(array $args): void
    {
        $GLOBALS['_registered_field_groups'][] = $args;
    }
}

if (! function_exists('get_template_directory')) {
    function get_template_directory(): string
    {
        return __DIR__;
    }
}

function registered_post_type(string $post_type): ?array
{
    return $GLOBALS['_registered_post_types'][$post_type] ?? null;
}

function reset_registered_post_types(): void
{
    $GLOBALS['_registered_post_types'] = [];
}

function registered_actions(string $tag): array
{
    return $GLOBALS['_registered_actions'][$tag] ?? [];
}

function reset_registered_actions(): void
{
    $GLOBALS['_registered_actions'] = [];
}

function registered_options_page(string $menuSlug): ?array
{
    return $GLOBALS['_registered_options_pages'][$menuSlug] ?? null;
}

function registered_options_sub_page(string $menuSlug): ?array
{
    return $GLOBALS['_registered_options_sub_pages'][$menuSlug] ?? null;
}

function reset_registered_options_pages(): void
{
    $GLOBALS['_registered_options_pages'] = [];
    $GLOBALS['_registered_options_sub_pages'] = [];
}

function registered_field_groups(): array
{
    return $GLOBALS['_registered_field_groups'];
}

function reset_registered_field_groups(): void
{
    $GLOBALS['_registered_field_groups'] = [];
}
