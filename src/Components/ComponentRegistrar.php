<?php

namespace Thyme\Framework\Components;

use Thyme\Framework\Helpers\Registrar;

/**
 * @extends Registrar<Component>
 */
class ComponentRegistrar extends Registrar
{
    public function registerOnInit(int $priority = 10): void {}
}
