<?php

declare(strict_types=1);

use Extended\ACF\Fields\Text;
use Extended\ACF\Location;
use Thyme\Framework\Contracts\HasFields;
use Thyme\Framework\Helpers\AcfRegistry;

beforeEach(function () {
    reset_registered_field_groups();
    reset_acf_field_keys();
});

class EmptyFields implements HasFields
{
    public function fields(): array
    {
        return [];
    }
}

class SingleField implements HasFields
{
    public function fields(): array
    {
        return [
            Text::make('Heading', 'heading')->required(),
        ];
    }
}

class MultipleFields implements HasFields
{
    public function fields(): array
    {
        return [
            Text::make('Heading', 'heading'),
            Text::make('Subheading', 'subheading'),
        ];
    }
}

it('registers a field group with the given title and location', function () {
    AcfRegistry::registerFields(
        'My Group',
        new SingleField,
        Location::where('post_type', 'event')
    );

    $groups = registered_field_groups();

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['title'])->toBe('My Group')
        ->and($groups[0]['location'])->toBe([
            [
                ['param' => 'post_type', 'operator' => '==', 'value' => 'event'],
            ],
        ])
        ->and($groups[0]['fields'])->toHaveCount(1)
        ->and($groups[0]['fields'][0]['label'])->toBe('Heading')
        ->and($groups[0]['fields'][0]['name'])->toBe('heading');
});

it('registers multiple fields in the same group', function () {
    AcfRegistry::registerFields(
        'My Group',
        new MultipleFields,
        Location::where('post_type', 'event')
    );

    $groups = registered_field_groups();

    expect($groups)->toHaveCount(1)
        ->and($groups[0]['fields'])->toHaveCount(2)
        ->and($groups[0]['fields'][0]['name'])->toBe('heading')
        ->and($groups[0]['fields'][1]['name'])->toBe('subheading');
});

it('does not register a field group when no fields are returned', function () {
    AcfRegistry::registerFields(
        'Empty Group',
        new EmptyFields,
        Location::where('post_type', 'event')
    );

    expect(registered_field_groups())->toBeEmpty();
});
