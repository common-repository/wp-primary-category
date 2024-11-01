<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Primary_Category_Admin' ) ) {
	class WP_Primary_Category_Admin {
		private static $option;

		public static function init() {
			self::hooks();
		}

		public static function hooks() {
			add_action( 'admin_enqueue_scripts', array( __CLASS__, 'assets' ) );
			add_action( 'admin_footer', array( __CLASS__, 'templates' ) );
			add_action( 'edit_form_top', array( __CLASS__, 'inputs' ) );
			add_action( 'save_post', array( __CLASS__, 'save' ) );
		}

		public static function assets() {
			if ( ! empty( self::_get_option() ) ) {
				wp_enqueue_style( 'wp_primary_category', WP_PC_PLUGIN_URL . 'assets/css/admin.css' );
				wp_enqueue_script( 'wp_primary_category', WP_PC_PLUGIN_URL . 'assets/js/admin.js' );
				wp_localize_script( 'wp_primary_category', 'WP_PC', array(
					'link_label' => esc_html__( 'Primary', 'wp-primary-category' ),
					'taxonomies' => (array) self::$option,
				) );
			}
		}

		public static function templates() {
			require_once WP_PC_PLUGIN_PATH . 'includes/views/admin/html-templates.php';
		}

		public static function inputs() {
			self::_get_option();
			require_once WP_PC_PLUGIN_PATH . 'includes/views/admin/html-inputs.php';
		}

		public static function save( $post_id ) {
			if (
				isset( $_POST['wp_primary_category'], $_POST['wp_primary_category_nonce'] )
				&& wp_verify_nonce( sanitize_key( $_POST['wp_primary_category_nonce'] ), 'wp-primary-category-inputs' )
			) {
				self::_delete_all_primary_categories( $post_id );

				// phpcs:ignore -- sanitization in WP_Primary_Category::validate_data:104
				$data = wp_unslash( $_POST['wp_primary_category'] );
				$data = WP_Primary_Category::validate_data( $data, $post_id );

				if ( ! empty( $data ) ) {
					foreach ( $data as $taxonomy => $term_id ) {
						update_post_meta( $post_id, '_wp_primary_category_' . $taxonomy, $term_id );
					}

					update_post_meta( $post_id, '_wp_primary_category', $data );
				}
			}
		}

		private static function _get_option() {
			global $typenow;

			if ( ! empty( $typenow ) && is_null( self::$option ) ) {
				self::$option = WP_Primary_Category_Settings::get_option( $typenow );
			}

			return self::$option;
		}

		private static function _delete_all_primary_categories( $post_id ) {
			if ( ! is_numeric( $post_id ) || ! $post_id ) {
				return false;
			}

			self::_get_option();

			if ( ! empty( self::$option ) ) {
				foreach ( self::$option as $taxonomy ) {
					delete_post_meta( $post_id, '_wp_primary_category_' . $taxonomy );
				}
			}

			delete_post_meta( $post_id, '_wp_primary_category' );
		}
	}
}
