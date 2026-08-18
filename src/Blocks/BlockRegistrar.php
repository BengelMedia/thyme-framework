<?php

namespace Thyme\Framework\Blocks;

use InvalidArgumentException;

/**
 * Collects Block classes and registers them with WordPress.
 */
class BlockRegistrar
{
    /**
     * @var class-string<Block>[]
     */
    protected array $blocks = [];

    /**
     * Add a Block class to the registrar.
     *
     * @param  class-string<Block>  $blockClass
     *
     * @throws InvalidArgumentException
     */
    public function add(string $blockClass): self
    {
        if (! is_subclass_of($blockClass, Block::class)) {
            throw new InvalidArgumentException(
                "{$blockClass} must extend ".Block::class
            );
        }

        $this->blocks[] = $blockClass;

        return $this;
    }

    /**
     * Add multiple Block classes at once.
     *
     * @param  class-string<Block>[]  $blockClasses
     */
    public function addMany(array $blockClasses): self
    {
        foreach ($blockClasses as $blockClass) {
            $this->add($blockClass);
        }

        return $this;
    }

    /**
     * Register every collected block immediately.
     */
    public function register(): void
    {
        foreach ($this->blocks as $blockClass) {
            (new $blockClass)->register();
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
     * Get all registered block classes.
     *
     * @return class-string<Block>[]
     */
    public function all(): array
    {
        return $this->blocks;
    }
}
