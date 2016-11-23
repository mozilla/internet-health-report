<?php
/**
 * Copyright (C) 2014-2016 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wm_Export_Controller {

	public static function index() {
		Ai1wm_Template::render( 'export/index' );
	}

	public static function export( $params = array() ) {
		global $wp_filter;

		// Set error handler
		@set_error_handler( 'Ai1wm_Handler::error' );

		// Set params
		if ( empty( $params ) ) {
			$params = ai1wm_urldecode( $_REQUEST );
		}

		// Set priority
		$priority = 5;
		if ( isset( $params['priority'] ) ) {
			$priority = (int) $params['priority'];
		}

		// Set secret key
		$secret_key = null;
		if ( isset( $params['secret_key'] ) ) {
			$secret_key = $params['secret_key'];
		}

		// Verify secret key by using the value in the database, not in cache
		if ( $secret_key !== get_option( AI1WM_SECRET_KEY ) ) {
			Ai1wm_Status::error(
				sprintf( __( 'Unable to authenticate your request with secret_key = "%s"', AI1WM_PLUGIN_NAME ), $secret_key ),
				__( 'Unable to export', AI1WM_PLUGIN_NAME )
			);
			exit;
		}

		// Get hook
		if ( isset( $wp_filter['ai1wm_export'] ) && ( $filters = $wp_filter['ai1wm_export'] ) && ksort( $filters ) ) {
			while ( $hooks = current( $filters ) ) {
				if ( $priority === key( $filters ) ) {
					foreach ( $hooks as $hook ) {
						try {
							$params = call_user_func_array( $hook['function'], array( $params ) );
						} catch ( Exception $e ) {
							Ai1wm_Status::error( $e->getMessage(), __( 'Unable to export', AI1WM_PLUGIN_NAME ) );
							exit;
						}
					}

					// Set completed
					$completed = true;
					if ( isset( $params['completed'] ) ) {
						$completed = (bool) $params['completed'];
					}

					// Log request
					if ( empty( $params['priority'] ) || is_file( ai1wm_export_path( $params ) ) ) {
						Ai1wm_Log::export( $params );
					}

					// Do request
					if ( $completed === false || ( $next = next( $filters ) ) && ( $params['priority'] = key( $filters ) ) ) {

						// Check the status, maybe we need to stop it
						if ( ! is_file( ai1wm_export_path( $params ) ) ) {
							exit;
						}

						return Ai1wm_Http::get( admin_url( 'admin-ajax.php?action=ai1wm_export' ), $params );
					}
				}

				next( $filters );
			}
		}
	}

	public static function buttons() {
		return array(
			apply_filters( 'ai1wm_export_file', Ai1wm_Template::get_content( 'export/button-file' ) ),
			apply_filters( 'ai1wm_export_ftp', Ai1wm_Template::get_content( 'export/button-ftp' ) ),
			apply_filters( 'ai1wm_export_dropbox', Ai1wm_Template::get_content( 'export/button-dropbox' ) ),
			apply_filters( 'ai1wm_export_gdrive', Ai1wm_Template::get_content( 'export/button-gdrive' ) ),
			apply_filters( 'ai1wm_export_s3', Ai1wm_Template::get_content( 'export/button-s3' ) ),
			apply_filters( 'ai1wm_export_onedrive', Ai1wm_Template::get_content( 'export/button-onedrive' ) ),
			apply_filters( 'ai1wm_export_box', Ai1wm_Template::get_content( 'export/button-box' ) ),
		);
	}
}
