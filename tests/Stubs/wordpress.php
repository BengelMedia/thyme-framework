<?php

declare(strict_types=1);

$GLOBALS['_registered_post_types'] = [];
$GLOBALS['_registered_actions'] = [];

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
