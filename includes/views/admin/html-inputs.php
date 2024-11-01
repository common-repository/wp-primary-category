<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wp_nonce_field( 'wp-primary-category-inputs', 'wp_primary_category_nonce' );

if ( ! empty( self::$option ) ) :
	foreach ( self::$option as $taxonomy ) :
		$term_id = get_primary_category( $taxonomy, null, 'id' ); ?>
		<input type="hidden" name="wp_primary_category[<?php echo esc_attr( $taxonomy ); ?>]" id="wp-pc-input-<?php echo esc_attr( $taxonomy ); ?>" value="<?php echo absint( $term_id ); ?>">
	<?php endforeach; ?>
<?php
endif;
