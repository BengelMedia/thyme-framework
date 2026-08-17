<?php

namespace Thyme\Framework\PostType;

use InvalidArgumentException;

/**
 * Collects PostType classes and registers them with WordPress.
 */
class PostTypeRegistrar
{
    /**
     * @var class-string<PostType>[]
     */
    protected array $postTypes = [];

    /**
     * Add a PostType class to the registrar.
     *
     * @param class-string<PostType> $postTypeClass
     *
     * @throws InvalidArgumentException
     */
    public function add(string $postTypeClass): self
    {
        if (! is_subclass_of($postTypeClass, PostType::class)) {
            throw new InvalidArgumentException(
                "{$postTypeClass} must extend " . PostType::class
            );
        }

        $this->postTypes[] = $postTypeClass;

        return $this;
    }

    /**
     * Add multiple PostType classes at once.
     *
     * @param class-string<PostType>[] $postTypeClasses
     */
    public function addMany(array $postTypeClasses): self
    {
        foreach ($postTypeClasses as $postTypeClass) {
            $this->add($postTypeClass);
        }

        return $this;
    }

    /**
     * Register every collected post type immediately.
     */
    public function register(): void
    {
        foreach ($this->postTypes as $postTypeClass) {
            (new $postTypeClass())->register();
        }
    }

    /**
     * Hook registration into WordPress on the init action.
     */
    public function registerOnInit(int $priority = 10): void
    {
        add_action('init', [$this, 'register'], $priority);
    }

    /**
     * Get all registered post type classes.
     *
     * @return class-string<PostType>[]
     */
    public function all(): array
    {
        return $this->postTypes;
    }
}
