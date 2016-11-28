<?php

function wpml_1022038_custom_menu() {
	$languages = apply_filters( 'wpml_active_languages' );

	if( ! empty( $languages ) ) {

		$output = '<div class="landing-page-menu"><ul>';

		foreach( $languages as $l ){
			$output .= '<li><a href="' . $l['url'] . '">' . $l['translated_name'] . '</a></li>';
		}

		$output .= '</ul></div>';

		echo $output;
	}
}