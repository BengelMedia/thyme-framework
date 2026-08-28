<?php

namespace Thyme\Framework\PostType;

use InvalidArgumentException;
use Thyme\Framework\Helpers\Registrar;

/**
 * Collects PostType classes and registers them with WordPress.
 *
 * @extends Registrar<PostType>
 */
class PostTypeRegistrar extends Registrar
{
    public function add(string $className): self
    {
        if (! is_a($className, PostType::class, true)) {
            throw new InvalidArgumentException(
                "Class [{$className}] must extend ".PostType::class
            );
        }

        return parent::add($className);
    }

    public function registerOnInit(int $priority = 10): void
    {
        add_action('init', [$this, 'register'], $priority);
    }
}
