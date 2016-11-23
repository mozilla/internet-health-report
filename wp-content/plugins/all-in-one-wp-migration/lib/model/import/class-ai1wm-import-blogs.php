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

class Ai1wm_Import_Blogs {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Preparing blogs...', AI1WM_PLUGIN_NAME ) );

		$blogs = array();

		// Check multisite.json file
		if ( true === is_file( ai1wm_multisite_path( $params ) ) ) {

			// Read multisite.json file
			$handle = fopen( ai1wm_multisite_path( $params ), 'r' );
			if ( $handle === false ) {
				throw new Ai1wm_Import_Exception( __( 'Unable to read multisite.json file', AI1WM_PLUGIN_NAME ) );
			}

			// Parse multisite.json file
			$multisite = fread( $handle, filesize( ai1wm_multisite_path( $params ) ) );
			$multisite = json_decode( $multisite, true );

			// Close handle
			fclose( $handle );

			// Validate
			if ( empty( $multisite['Network'] ) ) {
				if ( isset( $multisite['Sites'] ) && ( $sites = $multisite['Sites'] ) ) {
					if ( count( $sites ) === 1 && ( $site = current( $sites ) ) ) {
						$blogs[] = array(
							'Old' => array(
								'BlogID'  => $site['BlogID'],
								'SiteURL' => $site['SiteURL'],
								'HomeURL' => $site['HomeURL'],
							),
							'New' => array(
								'BlogID'  => null,
								'SiteURL' => site_url(),
								'HomeURL' => home_url(),
							),
						);
					} else {
						throw new Ai1wm_Import_Exception(
							__( 'The archive should contain <strong>Single WordPress</strong> site! Please revisit your export settings.', AI1WM_PLUGIN_NAME )
						);
					}
				} else {
					throw new Ai1wm_Import_Exception(
						__( 'At least <strong>one WordPress</strong> site should be presented in the archive.', AI1WM_PLUGIN_NAME )
					);
				}
			} else {
				throw new Ai1wm_Import_Exception(
					__( 'Unable to import <strong>WordPress Network</strong> into WordPress <strong>Single</strong> site.', AI1WM_PLUGIN_NAME )
				);
			}
		}

		// Save blogs.json file
		$handle = fopen( ai1wm_blogs_path( $params ), 'w' );
		fwrite( $handle, json_encode( $blogs ) );
		fclose( $handle );

		// Set progress
		Ai1wm_Status::info( __( 'Done preparing blogs...', AI1WM_PLUGIN_NAME ) );

		return $params;
	}
}
