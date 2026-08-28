<?php

namespace Thyme\Framework\Contracts;


/**
 * A registrar is a class that can register other classes that implement the HasRegistration interface.
 * @template T of HasRegistration
 */
interface Registrar
{
    /**
     * Add a class to the registrar.
     * @param class-string<T> $className
     * @return self
     */
    public function add(string $className): self;

    /**
     * Add multiple classes to the registrar
     * @param class-string<T>[] $classNames
     * @return self
     */
    public function addMany(array $classNames): self;

    /**
     * Register all the children
     * @return void
     */
    public function register(): void;

    /**
     * @return class-string<T>[]
     */
    public function all(): array;

    /**
     * Register the registrar on an action
     * @param int $priority the wp_action priority
     * @return void
     */
    public function registerOnInit(int $priority = 10): void;
}