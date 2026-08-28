<?php

namespace Thyme\Framework\Blocks;

use Thyme\Framework\Helpers\Registrar;

/**
 * Collects Block classes and registers them with WordPress.
 *
 * @extends Registrar<Block>
 */
class BlockRegistrar extends Registrar
{
    public function registerOnInit(int $priority = 10): void
    {
        add_action('acf/init', [$this, 'register'], $priority);
    }
}
