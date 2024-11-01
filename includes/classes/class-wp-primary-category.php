<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WP_Primary_Category' ) ) {
	class WP_Primary_Category {
		public static function init() {
			self::includes();
			self::hooks();
		}

		public static function hooks() {
			add_action( 'admin_init', array( 'WP_Primary_Category_Admin', 'init' ) );
			add_action( 'wp_loaded', array( 'WP_Primary_Category_Settings', 'init' ) );
			add_action( 'init', array( 'WP_Primary_Category_Shortcode', 'init' ) );
			add_action( 'rest_api_init', array( 'WP_Primary_Category_REST_API', 'init' ) );
		}

		public static function includes() {
			require_once WP_PC_PLUGIN_PATH . 'includes/classes/class-wp-primary-category-admin.php';
			require_once WP_PC_PLUGIN_PATH . 'includes/classes/class-wp-primary-category-settings.php';
			require_once WP_PC_PLUGIN_PATH . 'includes/classes/class-wp-primary-category-shortcode.php';
			require_once WP_PC_PLUGIN_PATH . 'includes/classes/class-wp-primary-category-rest-api.php';
			require_once WP_PC_PLUGIN_PATH . 'includes/functions.php';
		}

		public static function get_primary_categories( $post = null, $output = OBJECT ) {
			$post_id = self::_ge_post_id( $post );

			if ( ! $post_id ) {
				return false;
			}

			$post_type  = get_post_type( $post_id );
			$taxonomies = WP_Primary_Category_Settings::get_option( $post_type );

			$data = array();
			if ( $taxonomies ) {
				foreach ( $taxonomies as $taxonomy ) {
					$category = get_primary_category( $taxonomy, $post_id, $output );
					if ( $category ) {
						$data[ $taxonomy ] = $category;
					}
				}
			}

			return $data;
		}

		public static function get_primary_category( $taxonomy, $post = null, $output = OBJECT ) {
			$taxonomy = sanitize_key( $taxonomy );
			$post_id  = self::_ge_post_id( $post );

			if ( ! $post_id || empty( $taxonomy ) ) {
				return false;
			}

			$term_id = get_post_meta( $post_id, '_wp_primary_category_' . $taxonomy, true );

			if ( ! $term_id ) {
				return false;
			}

			$data = self::validate_data( array( $taxonomy => $term_id ), $post_id );

			if ( empty( $data ) ) {
				return false;
			}

			if ( ! $output ) {
				return $term_id;
			}

			$output_term = $output;
			$output      = strtolower( $output );

			if ( 'id' === $output ) {
				$output_term = OBJECT;
			}

			$term = get_term( $term_id, $taxonomy, $output_term );

			if ( 'id' === $output ) {
				if ( $term && ! is_wp_error( $term ) ) {
					return $term->term_id;
				} else {
					return false;
				}
			}

			return $term;
		}

		public static function is_primary_category( $term, $taxonomy = null, $post = null ) {
			$post_id = self::_ge_post_id( $post );
			$term    = get_term( $term, $taxonomy );

			if ( ! is_wp_error( $term ) || $post_id ) {
				$post_type = get_post_type( $post_id );
				$data      = self::validate_data( array( $term->taxonomy => $term->term_id ), $post );
				if ( ! empty( $data ) ) {
					return (int) get_post_meta( $post_id, '_wp_primary_category_' . $term->taxonomy, true ) === $term->term_id;
				}
			}

			return false;
		}

		public static function has_primary_category( $post = null ) {
			$post_id = self::_ge_post_id( $post );

			if ( ! $post_id ) {
				return false;
			}

			$data = get_post_meta( $post_id, '_wp_primary_category', true );

			return ! empty( self::validate_data( $data, $post_id ) );
		}

		public static function validate_data( $data, $post = null ) {
			$tmp     = array();
			$post_id = self::_ge_post_id( $post );

			if ( empty( $data ) || ! absint( $post_id ) ) {
				return $tmp;
			}

			$post_type  = get_post_type( $post_id );
			$taxonomies = WP_Primary_Category_Settings::get_option( $post_type );

			if ( ! empty( $taxonomies ) ) {
				foreach ( $taxonomies as $taxonomy ) {
					if ( array_key_exists( $taxonomy, $data ) && is_numeric( $data[ $taxonomy ] ) && $data[ $taxonomy ] ) {
						$tmp[ $taxonomy ] = absint( $data[ $taxonomy ] );
					}
				}
			}

			return $tmp;
		}

		private static function _ge_post_id( $post ) {
			if ( $post instanceof WP_Post ) {
				$post_id = $post->ID;
			} elseif ( empty( $post ) ) {
				$post_id = get_the_ID();
			} elseif ( is_numeric( $post ) ) {
				$post_id = $post;
			} else {
				$post_id = false;
			}

			return absint( $post_id );
		}
	}
}
