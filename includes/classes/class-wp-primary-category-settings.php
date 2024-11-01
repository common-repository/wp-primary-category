<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Primary_Category_Settings' ) ) {
	class WP_Primary_Category_Settings {
		public static function init() {
			self::hooks();
		}

		private static function hooks() {
			add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
			add_action( 'plugin_action_links_wp-primary-category/plugin.php', array( __CLASS__, 'plugin_action_links' ) );
		}

		public static function admin_menu() {
			add_submenu_page(
				'options-general.php',
				esc_html__( 'WP Primary Category', 'wp-primary-category' ),
				esc_html__( 'Primary Category', 'wp-primary-category' ),
				'manage_options',
				'wp-primary-category',
				array( __CLASS__, 'render' )
			);
		}

		public static function render() {
			self::_handle_posted_data();
			$data = self::_get_data();
			require_once WP_PC_PLUGIN_PATH . 'includes/views/admin/html-settings.php';
		}

		public static function plugin_action_links( $actions ) {
			if ( empty( $actions ) ) {
				$actions = array();
			}

			$actions['wp_primary_category_settings'] = sprintf( '<a href="%s">%s</a>', esc_url( admin_url( 'options-general.php?page=wp-primary-category' ) ), esc_html__( 'Settings', 'wp-primary-category' ) );

			return $actions;
		}

		public static function update_option( $option ) {
			if ( ! current_user_can( 'manage_options' ) ) {
				return false;
			}

			$data = array();

			if ( ! empty( $option ) ) {
				array_walk_recursive( $option, 'sanitize_text_field' );
				$data = self::_validate_option( $option );
			}

			$data = (array) apply_filters( 'wp_primary_category_update_option', $data, $option );

			return update_option( 'wp_primary_category_option', $data, 'yes' );
		}

		public static function get_option( $post_type = null ) {
			$data = get_option( 'wp_primary_category_option', array() );

			if ( ! empty( $data ) ) {
				$data = self::_validate_option( $data );
				if ( ! empty( $post_type ) ) {
					$data = array_key_exists( $post_type, $data ) ? $data[ $post_type ] : false;
				}
			}

			return apply_filters( 'wp_primary_category_option', $data );
		}

		public static function get_not_allowed_post_types() {
			return (array) apply_filters( 'wp_primary_category_not_allowed_post_types', array( 'attachment' => 'attachment' ) );
		}

		public static function get_post_types( $args = array(), $output = 'objects' ) {
			$args       = apply_filters( 'wp_primary_category_post_types_args', array_merge( array( 'show_ui' => true ), $args ) );
			$post_types = array_diff_key( get_post_types( $args, $output ), self::get_not_allowed_post_types() );
			return (array) apply_filters( 'wp_primary_category_post_types', $post_types );
		}

		public static function get_not_allowed_taxonomies() {
			return (array) apply_filters( 'wp_primary_category_not_allowed_taxonomies', array() );
		}

		public static function get_taxonomies( $args = array(), $output = 'objects' ) {
			$args       = apply_filters( 'wp_primary_category_taxonomies_args', array_merge( array( 'hierarchical' => true ), $args ) );
			$taxonomies = array_diff_key( get_taxonomies( $args, $output ), self::get_not_allowed_taxonomies() );
			return (array) apply_filters( 'wp_primary_category_taxonomies', $taxonomies );
		}

		private static function _validate_option( $option ) {
			$data       = array();
			$post_types = self::get_post_types();
			$taxonomies = self::get_taxonomies();

			if ( ! empty( $option ) ) {
				foreach ( $option as $key => $value ) {
					if ( array_key_exists( $key, $post_types ) && ! empty( $value ) ) {
						foreach ( $value as $taxonomy ) {
							if (
								array_key_exists( $taxonomy, $taxonomies )
								&& in_array( $key, $taxonomies[ $taxonomy ]->object_type, true )
							) {
								$data[ $key ][] = sanitize_key( $taxonomy );
							}
						}
					}
				}
			}

			return $data;
		}

		private static function _get_data() {
			$post_types = self::get_post_types();
			$taxonomies = self::get_taxonomies();
			$option     = self::get_option();
			$data       = array();

			foreach ( $taxonomies as $taxonomy ) {
				foreach ( $post_types as $post_type ) {
					if ( in_array( $post_type->name, $taxonomy->object_type, true ) ) {
						if ( ! isset( $data[ $post_type->name ] ) ) {
							$data[ $post_type->name ]['post_type'] = $post_type;
							if ( array_key_exists( $post_type->name, $option ) ) {
								$data[ $post_type->name ]['option'] = $option[ $post_type->name ];
							} else {
								$data[ $post_type->name ]['option'] = array();
							}
						}
						$data[ $post_type->name ]['taxonomies'][] = $taxonomy;
					}
				}
			}

			return $data;
		}

		private static function _handle_posted_data() {
			if (
				isset( $_POST['wp_primary_category_nonce'] )
				&& wp_verify_nonce( sanitize_key( $_POST['wp_primary_category_nonce'] ), 'wp-primary-category-options' )
			) {
				$posted_data = array();
				if ( isset( $_POST['wp_primary_category'] ) && ! empty( $_POST['wp_primary_category'] ) ) {
					// phpcs:ignore -- sanitization in self::_validate_option:108
					$posted_data = wp_unslash( $_POST['wp_primary_category'] );
				}
				if ( self::update_option( $posted_data ) ) {
					$type    = 'updated';
					$message = esc_html__( 'The options has been updated.', 'wp-primary-category' );
				} else {
					$type    = 'error';
					$message = esc_html__( 'The options has not been updated.', 'wp-primary-category' );
				}
				add_settings_error( 'wp-primary-category', 'wp-primary-category', $message, $type );
			}
		}
	}
}
