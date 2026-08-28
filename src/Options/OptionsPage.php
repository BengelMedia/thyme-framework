<?php

namespace Thyme\Framework\Options;

use Extended\ACF\Fields\Field;
use Extended\ACF\Location;
use InvalidArgumentException;
use Thyme\Framework\Contracts\HasFields;
use Thyme\Framework\Contracts\HasRegistration;
use Thyme\Framework\Helpers\AcfRegistry;
use Thyme\Framework\Icons\DashIcons;

/**
 * Base class for registering ACF options pages.
 *
 * Extend this class, implement slug(), and optionally override
 * pageTitle()/menuTitle()/icon()/position()/parentSlug()/args() to
 * register an options page.
 *
 * @see https://www.advancedcustomfields.com/resources/acf_add_options_page/
 *
 * @example
 * class Cookie extends OptionsPage
 * {
 *     public function slug(): string { return 'cookie'; }
 *     public function pageTitle(): string { return 'Cookie'; }
 *     public function icon(): string { return 'dashicons-star-filled'; }
 * }
 */
abstract class OptionsPage implements HasFields, HasRegistration
{
    /**
     * The ACF options page slug (menu_slug).
     */
    abstract public function slug(): string;

    /**
     * The page title shown in the browser tab.
     */
    public function pageTitle(): string
    {
        return ucfirst($this->slug());
    }

    /**
     * The title shown in the admin menu.
     */
    public function menuTitle(): string
    {
        return $this->pageTitle();
    }

    /**
     * The icon used in the admin menu.
     */
    public function icon(): string
    {
        return DashIcons::AdminGeneric->value;
    }

    /**
     * The position of the menu item. Lower numbers appear higher.
     */
    public function position(): ?int
    {
        return null;
    }

    /**
     * The slug of the parent menu the page should be nested under.
     * When null the page is added as a top-level menu item.
     */
    public function parentSlug(): ?string
    {
        return null;
    }

    /**
     * The capability required to access the options page.
     */
    public function capability(): string
    {
        return 'edit_themes';
    }

    /**
     * Whether to redirect to the first child page after saving.
     */
    public function redirect(): bool
    {
        return true;
    }

    /**
     * Whether the options are loaded into the database on every request.
     */
    public function autoload(): bool
    {
        return true;
    }

    /**
     * The custom text shown on the update button.
     */
    public function updateButton(): ?string
    {
        return null;
    }

    /**
     * The custom message shown after the options are updated.
     */
    public function updatedMessage(): ?string
    {
        return null;
    }

    /**
     * ACF fields shown on this options page.
     *
     * @return Field[]
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * The title of the ACF field group attached to this options page.
     */
    public function fieldGroupTitle(): string
    {
        return $this->pageTitle();
    }

    /**
     * Full argument list passed to acf_add_options_page() or
     * acf_add_options_sub_page().
     */
    public function args(): array
    {
        $args = [
            'page_title' => $this->pageTitle(),
            'menu_title' => $this->menuTitle(),
            'menu_slug' => $this->slug(),
            'icon_url' => $this->icon(),
            'capability' => $this->capability(),
            'redirect' => $this->redirect(),
            'autoload' => $this->autoload(),
        ];

        if ($this->position() !== null) {
            $args['position'] = $this->position();
        }

        if ($this->parentSlug() !== null) {
            $args['parent_slug'] = $this->parentSlug();
        }

        if ($this->updateButton() !== null) {
            $args['update_button'] = $this->updateButton();
        }

        if ($this->updatedMessage() !== null) {
            $args['updated_message'] = $this->updatedMessage();
        }

        $blockData = apply_filters('thyme-framework/settings-page-args', $args, $this->slug());


        return $args;
    }

    /**
     * Register the options page with WordPress.
     */
    public function register(): void
    {
        if (! function_exists('acf_add_options_page')) {
            return;
        }

        $slug = $this->slug();

        $this->validateSlug($slug);

        if ($this->parentSlug() !== null) {
            acf_add_options_sub_page($this->args());
        } else {
            acf_add_options_page($this->args());
        }

        AcfRegistry::registerFields(
            $this->fieldGroupTitle(),
            $this,
            Location::where('options_page', $this->slug())
        );
    }

    /**
     * Validate that the slug is safe for WordPress.
     */
    protected function validateSlug(string $slug): void
    {
        if ($slug !== strtolower($slug)) {
            throw new InvalidArgumentException(
                "Options page slug '{$slug}' must be lowercase."
            );
        }

        if (preg_match('/[^a-z0-9_-]/', $slug)) {
            throw new InvalidArgumentException(
                "Options page slug '{$slug}' may only contain lowercase letters, numbers, hyphens and underscores."
            );
        }
    }
}
