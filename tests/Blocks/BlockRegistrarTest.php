<?php

declare(strict_types=1);

use Thyme\Framework\Blocks\Block;
use Thyme\Framework\Blocks\BlockRegistrar;

beforeEach(function () {
    reset_registered_blocks();
    reset_registered_actions();
    reset_acf_field_keys();
});

class RegistrarHero extends Block
{
    protected string $name = 'registrar_hero';
}

class RegistrarQuote extends Block
{
    protected string $name = 'registrar_quote';
}

it('registers a single block class', function () {
    $registrar = new BlockRegistrar;
    $registrar->add(RegistrarHero::class);

    expect($registrar->all())->toBe([RegistrarHero::class]);

    $registrar->register();

    expect(registered_block('thyme/registrar_hero'))->not->toBeNull();
});

it('registers multiple block classes', function () {
    $registrar = new BlockRegistrar;
    $registrar->addMany([RegistrarHero::class, RegistrarQuote::class]);

    $registrar->register();

    expect(registered_block('thyme/registrar_hero'))->not->toBeNull()
        ->and(registered_block('thyme/registrar_quote'))->not->toBeNull();
});

it('throws when adding a non-block class', function () {
    $registrar = new BlockRegistrar;
    $registrar->add(stdClass::class);

    expect($registrar->all())->toHaveLength(0);
});

it('hooks registration into the acf/init action', function () {
    $registrar = new BlockRegistrar;
    $registrar->add(RegistrarHero::class);
    $registrar->registerOnInit();

    $actions = registered_actions('acf/init');

    expect($actions)->toHaveCount(1)
        ->and($actions[0]['priority'])->toBe(10)
        ->and($actions[0]['callback'])->toBe([$registrar, 'register']);
});
