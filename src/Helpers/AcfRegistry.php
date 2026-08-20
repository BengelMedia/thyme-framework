<?php

namespace Thyme\Framework\Helpers;

use Extended\ACF\Location;
use Thyme\Framework\Contracts\HasFields;

/**
 * A registration helper for ACF fields
 */
class AcfRegistry
{
    public static function registerFields(
        string $title,
        HasFields $class,
        Location $location
    ) {
        $fields = $class->fields();

        if ($fields === [] || ! function_exists('register_extended_field_group')) {
            return;
        }

        register_extended_field_group([
            'title' => $title,
            'fields' => $fields,
            'location' => [
                $location,
            ],
        ]);

    }
}
