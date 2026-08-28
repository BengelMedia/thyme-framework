<?php

namespace Thyme\Framework\PostType;

use Thyme\Framework\Helpers\Registrar;

/**
 * Collects PostType classes and registers them with WordPress.
 * @extends Registrar<PostType>
 */
class PostTypeRegistrar extends Registrar
{
    public function registerOnInit(int $priority = 10): void
    {
        add_action('init', [$this, 'register'], $priority);
    }
}
