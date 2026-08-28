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
    public function registerOnInit(int $priority = 10): void
    {
        add_action('acf/init', [$this, 'register'], $priority);
    }
}
