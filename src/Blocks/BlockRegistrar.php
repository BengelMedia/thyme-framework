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
    public function add(string $className): self
    {
        if (! is_a($className, Block::class, true)) {
            return $this;
        }

        return parent::add($className);
    }

    public function registerOnInit(int $priority = 10): void
    {
        add_action('acf/init', [$this, 'register'], $priority);
    }
}
