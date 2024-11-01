<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Primary_Category_Shortcode' ) ) {
	class WP_Primary_Category_Shortcode {
		public static function init() {
			add_shortcode( 'wp_primary_category', array( __CLASS__, 'shortcode' ) );
		}

		public static function shortcode( $atts ) {
			$atts = shortcode_atts( array(
				'taxonomy' => '',
				'post'     => '',
				'output'   => 'link',
			), $atts, 'wp_primary_category' );

			return the_primary_category( $atts['taxonomy'], $atts['post'], $atts['output'], false );
		}
	}
}
