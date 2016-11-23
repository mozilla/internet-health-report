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

class Ai1wm_Backups_Controller {

	public static function index() {
		$model = new Ai1wm_Backups;

		// Username
		if ( isset( $_POST['ai1wm-username'] ) ) {
			update_option( AI1WM_AUTH_USER, $_POST['ai1wm-username'] );
		}

		// Password
		if ( isset( $_POST['ai1wm-password'] ) ) {
			update_option( AI1WM_AUTH_PASSWORD, $_POST['ai1wm-password'] );
		}

		Ai1wm_Template::render(
			'backups/index',
			array(
				'backups'     => $model->get_files(),
				'free_space'  => $model->get_free_space(),
				'total_space' => $model->get_total_space(),
				'username'    => get_option( AI1WM_AUTH_USER ),
				'password'    => get_option( AI1WM_AUTH_PASSWORD ),
			)
		);
	}

	public static function delete() {
		$response = array( 'errors' => array() );

		// Set archive
		$archive = null;
		if ( isset( $_POST['archive'] ) ) {
			$archive = trim( $_POST['archive'] );
		}

		$model = new Ai1wm_Backups;

		try {
			// Delete file
			$model->delete_file( $archive );
		} catch ( Exception $e ) {
			$response['errors'][] = $e->getMessage();
		}

		echo json_encode( $response );
		exit;
	}
}
