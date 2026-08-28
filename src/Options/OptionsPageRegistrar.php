<?php

namespace Thyme\Framework\Options;

use InvalidArgumentException;
use Thyme\Framework\Helpers\Registrar;

/**
 * Collects OptionsPage classes and registers them with WordPress.
 *
 * @extends Registrar<OptionsPage>
 */
class OptionsPageRegistrar extends Registrar
{
    public function add(string $className): self
    {
        if (! is_a($className, OptionsPage::class, true)) {
            throw new InvalidArgumentException(
                "Class [{$className}] must extend " . OptionsPage::class
            );
        }

        return parent::add($className);
    }

    public function registerOnInit(int $priority = 10): void
    {
        add_action('acf/init', [$this, 'register'], $priority);
    }
}
