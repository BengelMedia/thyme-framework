<?php

namespace Thyme\Framework\Options;

use InvalidArgumentException;

/**
 * Collects OptionsPage classes and registers them with WordPress.
 */
class OptionsPageRegistrar
{
    /**
     * @var class-string<OptionsPage>[]
     */
    protected array $optionsPages = [];

    /**
     * Add an OptionsPage class to the registrar.
     *
     * @param  class-string<OptionsPage>  $optionsPageClass
     *
     * @throws InvalidArgumentException
     */
    public function add(string $optionsPageClass): self
    {
        if (! is_subclass_of($optionsPageClass, OptionsPage::class)) {
            throw new InvalidArgumentException(
                "{$optionsPageClass} must extend ".OptionsPage::class
            );
        }

        $this->optionsPages[] = $optionsPageClass;

        return $this;
    }

    /**
     * Add multiple OptionsPage classes at once.
     *
     * @param  class-string<OptionsPage>[]  $optionsPageClasses
     */
    public function addMany(array $optionsPageClasses): self
    {
        foreach ($optionsPageClasses as $optionsPageClass) {
            $this->add($optionsPageClass);
        }

        return $this;
    }

    /**
     * Register every collected options page immediately.
     */
    public function register(): void
    {
        foreach ($this->optionsPages as $optionsPageClass) {
            (new $optionsPageClass)->register();
        }
    }

    /**
     * Hook registration into WordPress on the acf/init action.
     */
    public function registerOnInit(int $priority = 10): void
    {
        add_action('acf/init', [$this, 'register'], $priority);
    }

    /**
     * Get all registered options page classes.
     *
     * @return class-string<OptionsPage>[]
     */
    public function all(): array
    {
        return $this->optionsPages;
    }
}
