<?php

class WPML_Cache_Directory {

	const MAIN_DIRECTORY_NAME = 'wpml';

	/**
	 * @var WPML_WP_API
	 */
	private $wp_api;

	/**
	 * @var WP_Filesystem_Direct
	 */
	private $filesystem;

	/**
	 * WPML_Cache_Directory constructor.
	 *
	 * @param WPML_WP_API $wp_api
	 */
	public function __construct( WPML_WP_API $wp_api ) {
		$this->wp_api     = $wp_api;
		$this->filesystem = $wp_api->get_wp_filesystem_direct();
	}

	/**
	 * @return string
	 */
	private function get_main_directory_path() {
		$main_directory_path = $this->wp_api->constant( 'WPML_CACHE_PATH_ROOT' )
			? trailingslashit( $this->wp_api->constant( 'WPML_CACHE_PATH_ROOT' ) ) . self::MAIN_DIRECTORY_NAME : null;
		$main_directory_path = is_null( $main_directory_path )
			? trailingslashit( $this->filesystem->wp_content_dir() ) . 'cache/' . self::MAIN_DIRECTORY_NAME : $main_directory_path;

		return trailingslashit( $main_directory_path );
	}

	/**
	 * The function `wp_mkdir_p` will create directories recursively
	 *
	 * @param string $absolute_path
	 *
	 * @return string|bool absolute path or false if we can't have a writable and readable directory
	 */
	private function maybe_create_directory( $absolute_path ) {
		$result = true;

		if( ! $this->filesystem->is_dir( $absolute_path ) ) {
			$result = wp_mkdir_p( $absolute_path );
		}

		if ( ! $this->filesystem->is_writable( $absolute_path )
		     || ! $this->filesystem->is_readable( $absolute_path )
		) {
			$result = $this->filesystem->chmod( $absolute_path, 0755, true );
		}

		return $result ? $absolute_path : false;
	}

	/**
	 * @param string $relative_path
	 *
	 * @return string|bool absolute path or false if we can't have a writable and readable directory
	 */
	public function get( $relative_path = '' ) {
		$absolute_path       = false;
		$main_directory_path = $this->maybe_create_directory( $this->get_main_directory_path() );

		if ( $main_directory_path ) {
			$absolute_path = trailingslashit( $main_directory_path . ltrim( $relative_path, '/\\' ) );
			$absolute_path = $this->maybe_create_directory( $absolute_path );
		}

		return $absolute_path;
	}

	/**
	 * @param string $relative_path
	 */
	public function remove( $relative_path = '' ) {
		$main_directory_path = $this->get_main_directory_path();
		$absolute_path = trailingslashit( $main_directory_path . ltrim( $relative_path, '/\\' ) );
		$this->filesystem->delete( $absolute_path, true );
	}
}