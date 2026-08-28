<?php

namespace Thyme\Framework\Helpers;

use Thyme\Framework\Contracts\HasRegistration;
use Thyme\Framework\Contracts\Registrar as RegistrarContract;

/**
 * @template T of HasRegistration
 */
abstract class Registrar implements RegistrarContract
{
    /**
     * @var class-string<T>[]
     */
    protected array $registrations = [];

    public function add(string $className): self
    {
        $this->registrations[] = $className;

        return $this;
    }

    public function addMany(array $classNames): self
    {
        foreach ($classNames as $className) {
            $this->add($className);
        }

        return $this;
    }

    public function register(): void
    {
        foreach ($this->registrations as $registration) {
            /**
             * @var T $class
             */
            $class = new $registration;

            $class->register();
        }
    }

    /**
     * Register the registrar on an action
     *
     * @param  int  $priority  the wp_action priority
     */
    abstract public function registerOnInit(int $priority = 10): void;

    /**
     * @return class-string<T>[]
     */
    public function all(): array
    {
        return $this->registrations;
    }
}
