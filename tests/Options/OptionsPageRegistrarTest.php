<?php

declare(strict_types=1);

use Thyme\Framework\Options\OptionsPage;
use Thyme\Framework\Options\OptionsPageRegistrar;

beforeEach(function () {
    reset_registered_options_pages();
    reset_registered_actions();
});

class RegistrarCookie extends OptionsPage
{
    public function slug(): string
    {
        return 'registrar_cookie';
    }
}

class RegistrarConsent extends OptionsPage
{
    public function slug(): string
    {
        return 'registrar_consent';
    }
}

it('registers a single options page class', function () {
    $registrar = new OptionsPageRegistrar;
    $registrar->add(RegistrarCookie::class);

    expect($registrar->all())->toBe([RegistrarCookie::class]);

    $registrar->register();

    expect(registered_options_page('registrar_cookie'))->not->toBeNull();
});

it('registers multiple options page classes', function () {
    $registrar = new OptionsPageRegistrar;
    $registrar->addMany([RegistrarCookie::class, RegistrarConsent::class]);

    $registrar->register();

    expect(registered_options_page('registrar_cookie'))->not->toBeNull()
        ->and(registered_options_page('registrar_consent'))->not->toBeNull();
});

it('throws when adding a non-options-page class', function () {
    $registrar = new OptionsPageRegistrar;
    $registrar->add(stdClass::class);
})->throws(InvalidArgumentException::class, 'must extend');

it('hooks registration into the acf/init action', function () {
    $registrar = new OptionsPageRegistrar;
    $registrar->add(RegistrarCookie::class);
    $registrar->registerOnInit();

    $actions = registered_actions('acf/init');

    expect($actions)->toHaveCount(1)
        ->and($actions[0]['priority'])->toBe(10)
        ->and($actions[0]['callback'])->toBe([$registrar, 'register']);
});
