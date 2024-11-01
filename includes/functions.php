<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'get_primary_categories' ) ) {
	function get_primary_categories( $post = null, $output = OBJECT ) {
		return WP_Primary_Category::get_primary_categories( $post, $output );
	}
}

if ( ! function_exists( 'get_primary_category' ) ) {
	function get_primary_category( $taxonomy, $post = null, $output = OBJECT ) {
		return WP_Primary_Category::get_primary_category( $taxonomy, $post, $output );
	}
}

if ( ! function_exists( 'the_primary_category' ) ) {
	function the_primary_category( $taxonomy, $post = null, $output = 'link', $echo = true ) {
		$primary = get_primary_category( $taxonomy, $post );
		$html    = '';

		if ( ! is_wp_error( $primary ) ) {
			if ( 'link' === $output ) {
				$html = sprintf( '<a href="%s" title="%s">%s</a>', get_term_link( $primary ), $primary->name, $primary->name );
			} else {
				$html = $primary->name;
			}
		}

		$html = apply_filters( 'wp_primary_category_html', $html, $taxonomy, $post, $output );

		if ( $echo ) {
			echo wp_kses_post( $html );
		} else {
			return wp_kses_post( $html );
		}
	}
}

if ( ! function_exists( 'is_primary_category' ) ) {
	function is_primary_category( $term, $taxonomy = null, $post = null ) {
		return WP_Primary_Category::is_primary_category( $term, $taxonomy, $post );
	}
}

if ( ! function_exists( 'has_primary_category' ) ) {
	function has_primary_category( $post = null ) {
		return WP_Primary_Category::has_primary_category( $post );
	}
}
