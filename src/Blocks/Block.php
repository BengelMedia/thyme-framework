<?php

namespace Thyme\Framework\Blocks;

use Extended\ACF\Location;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Thyme\Framework\Icons\DashIcons;

class Block {
	protected ?string $title = null;

	protected string $name = 'block';

	protected ?string $description = null;

	protected ?string $category = null;

	protected DashIcons $icon = DashIcons::MediaAudio;

	private bool $is_preview = false;

	public function getTitle(): string {
		return $this->title ?? ucfirst( $this->getName() );
	}

	public function getName(): string {
		return $this->name;
	}

	public function getDescription(): string {
		return $this->description ?? '';
	}

	public function getCategory(): string {
		return $this->category ?? 'Default';
	}

	public function getIcon(): string {
		return $this->icon->value;
	}

	public function fields(): array {
		return [];
	}

	public function scripts(): array {
		return [];
	}

	public function styles(): array {
		return [];
	}

	public function render(): void {
		[ , , $_is_preview ] = func_get_args();
		$this->is_preview = $_is_preview;
		$directory        = get_stylesheet_directory() . '/Blocks/' . class_basename( $this );

		$candidates = [
			$this->getName() . '.blade.php',
			Str::ucfirst( $this->getName() ) . '.blade.php',
			class_basename( $this ) . '.blade.php',
		];

		$viewPath = null;

		foreach ( $candidates as $candidate ) {
			$path = $directory . '/' . $candidate;

			if ( file_exists( $path ) ) {
				$viewPath = $path;

				break;
			}
		}

		if ( $viewPath === null ) {
			return;
		}

		echo view( $viewPath, [
			'block' => $this,
		] );
	}

	public function register(): void {
		if ( ! function_exists( 'acf_register_block_type' ) ) {
			return;
		}

		$blockName = sprintf( 'thyme/%s', $this->getName() );
		$assetData = $this->registerAssets( $blockName );

		$blockData = [
			'name'            => $blockName,
			'title'           => $this->getTitle(),
			'description'     => $this->getDescription(),
			'category'        => $this->getCategory(),
			'icon'            => $this->getIcon(),
			'api_version'     => 3,
			'supports'        => [
				'html' => false,
				'jsx'  => true,
			],
			'attributes'      => [
				'style' => [
					'type' => 'object',
				],
			],
			'render_callback' => [ $this, 'render' ],
		];

		$blockData = array_merge( $blockData, $assetData );

		$registeredBlock = acf_register_block_type( $blockData );

		$this->registerFieldGroup( $registeredBlock['name'] );
	}

	/**
	 * Register the ACF field group for this block.
	 */
	private function registerFieldGroup( string $blockName ): void {
		$fields = $this->fields();

		if ( $fields === [] || ! function_exists( 'register_extended_field_group' ) ) {
			return;
		}

		register_extended_field_group( [
			'title'    => $this->getTitle(),
			'fields'   => $fields,
			'location' => [
				Location::where( 'block', $blockName ),
			],
		] );
	}

	/**
	 * Check if the asset starts with a vite tag
	 */
	private function isViteAsset( string $asset ): bool {
		return str_starts_with( $asset, '@vite:/' );
	}

	/**
	 * Get the URL for an asset
	 */
	private function resolveAssetUrl( string $asset ): string {
		if ( $this->isViteAsset( $asset ) ) {
			return Vite::asset( str_replace( '@vite:/', '', $asset ) );
		}

		return $asset;
	}

	/**
	 * Create a slug for the asset
	 */
	private function resolveAssetSlug( string $blockName, string $asset ): string {
		$assetFileName = pathinfo( $asset, PATHINFO_FILENAME );
		$format        = 'thyme/%s/%s';

		return sprintf( $format, $blockName, $assetFileName );
	}

	/**
	 * Register the assets used by the block
	 *
	 * @return array|array[]
	 */
	private function registerAssets(
		string $blockName,
	): array {
		$assetData = [
			'script' => [],
			'style'  => [],
		];

		foreach ( $this->scripts() as $script ) {
			$url  = $this->resolveAssetUrl( $script );
			$slug = $this->resolveAssetSlug( $blockName, $url );
			wp_register_script(
				$slug,
				$url,
				[],
				null,
				true,
			);
			$assetData['script'][] = $slug;
		}

		foreach ( $this->styles() as $style ) {
			$url  = $this->resolveAssetUrl( $style );
			$slug = $this->resolveAssetSlug( $blockName, $url );
			wp_register_style(
				$slug,
				$url,
				[],
				null,
			);
			$assetData['style'][] = $slug;
		}

		return $assetData;
	}


	public function attributes( array $input = [] ): string {
		if ( ! $this->is_preview ) {
			return get_block_wrapper_attributes( $input );
		}

		return implode( ' ', array_map(
			fn( $k, $v ) => sprintf( '%s="%s"', sanitize_key( $k ), esc_attr( is_array( $v ) ? implode( ' ', $v ) : $v ) ),
			array_keys( $input ),
			$input
		) );
	}
}
