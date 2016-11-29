<?php

class WPML_ST_Reset {
	/**
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * @var WPML_ST_Settings
	 */
	private $settings;

	/**
	 * @param wpdb $wpdb
	 * @param WPML_ST_Settings $settings
	 */
	public function __construct( $wpdb, WPML_ST_Settings $settings = null ) {
		$this->wpdb = $wpdb;

		if ( ! $settings ) {
			$settings = new WPML_ST_Settings();
		}
		$this->settings = $settings;
	}

	public function reset() {
		$this->settings->delete_settings();

		// remove tables at the end to avoid errors in ST due to last actions invoked by hooks
		add_action( 'shutdown', array( $this, 'remove_db_tables' ), PHP_INT_MAX - 1 );
	}

	public function remove_db_tables() {
		$table = $this->wpdb->prefix . 'icl_string_pages';
		$this->wpdb->query( 'DROP TABLE IF EXISTS ' . $table );

		$table = $this->wpdb->prefix . 'icl_string_urls';
		$this->wpdb->query( 'DROP TABLE IF EXISTS ' . $table );
	}
}