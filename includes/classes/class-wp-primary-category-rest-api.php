<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Primary_Category_REST_API' ) ) {
	class WP_Primary_Category_REST_API {
		public static function init() {
			$option = WP_Primary_Category_Settings::get_option();
			if ( ! empty( $option ) ) {
				foreach ( array_keys( $option ) as $post_type ) {
					self::register_field( $post_type );
				}
			}
		}

		public static function register_field( $post_type ) {
			register_rest_field( $post_type, 'primary_categories', array(
				'get_callback' => array( __CLASS__, 'get_field' ),
				'schema'       => array(
					'description' => esc_html__( 'Primary categories', 'wp-primary-category' ),
					'type'        => 'object',
				),
			) );
		}

		public static function get_field( $request ) {
			$output = apply_filters( 'wp_primary_category_rest_api_output', 'id', $request );
			$data   = get_primary_categories( $request['id'], $output );

			if ( ! empty( $data ) ) {
				$taxonomies = get_taxonomies( array( 'show_in_rest' => true ), 'names' );
				foreach ( $data as $k => $v ) {
					if ( ! in_array( $k, $taxonomies, true ) ) {
						unset( $data[ $k ] );
					}
				}
			}

			return apply_filters( 'wp_primary_category_rest_api_data', $data, $request );
		}
	}
}
