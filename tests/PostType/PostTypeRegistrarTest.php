<?php

declare(strict_types=1);

use Thyme\Framework\PostType\PostType;
use Thyme\Framework\PostType\PostTypeRegistrar;

beforeEach(function () {
    reset_registered_post_types();
    reset_registered_actions();
});

class RegistrarEvent extends PostType
{
    public function slug(): string
    {
        return 'registrar_event';
    }
}

class RegistrarLocation extends PostType
{
    public function slug(): string
    {
        return 'registrar_location';
    }
}

it('registers a single post type class', function () {
    $registrar = new PostTypeRegistrar;
    $registrar->add(RegistrarEvent::class);

    expect($registrar->all())->toBe([RegistrarEvent::class]);

    $registrar->register();

    expect(registered_post_type('registrar_event'))->not->toBeNull();
});

it('registers multiple post type classes', function () {
    $registrar = new PostTypeRegistrar;
    $registrar->addMany([RegistrarEvent::class, RegistrarLocation::class]);

    $registrar->register();

    expect(registered_post_type('registrar_event'))->not->toBeNull()
        ->and(registered_post_type('registrar_location'))->not->toBeNull();
});

it('throws when adding a non-post-type class', function () {
    $registrar = new PostTypeRegistrar;
    $registrar->add(stdClass::class);
})->throws(InvalidArgumentException::class, 'must extend');

it('hooks registration into the init action', function () {
    $registrar = new PostTypeRegistrar;
    $registrar->add(RegistrarEvent::class);
    $registrar->registerOnInit();

    $actions = registered_actions('init');

    expect($actions)->toHaveCount(1)
        ->and($actions[0]['priority'])->toBe(10)
        ->and($actions[0]['callback'])->toBe([$registrar, 'register']);
});
