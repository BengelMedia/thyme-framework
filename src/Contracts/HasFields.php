<?php

namespace Thyme\Framework\Contracts;

use Extended\ACF\Fields\Field;

interface HasFields
{
    /**
     * An array of fields that will be registered for this block.
     *
     * @return Field[]
     */
    public function fields(): array;
}
