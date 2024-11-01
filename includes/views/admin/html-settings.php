<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap">
	<h1><?php esc_html_e( 'WP Primary Category', 'wp-primary-category' ); ?></h1>
	<?php settings_errors(); ?>
	<p><?php esc_html_e( 'Select below the taxonomies to able the primary category feature.', 'wp-primary-category' ); ?></p>
	<form action="<?php echo esc_url( admin_url( 'options-general.php?page=wp-primary-category' ) ); ?>" method="POST">
	<?php
	wp_nonce_field( 'wp-primary-category-options', 'wp_primary_category_nonce' );
	if ( ! empty( $data ) ) :
	?>
		<ul>
		<?php foreach ( $data as $name => $value ) : ?>
			<li>
				<h3><?php echo esc_html( $value['post_type']->label ); ?></h3>
				<ul>
				<?php foreach ( $value['taxonomies'] as $taxonomy ) : ?>
					<?php
					if ( in_array( $name, $taxonomy->object_type, true ) ) :
						$checked = in_array( $taxonomy->name, $value['option'], true ) ? $taxonomy->name : '';
					?>
					<li><label><input type="checkbox" name="wp_primary_category[<?php echo esc_attr( $value['post_type']->name ); ?>][]" value="<?php echo esc_attr( $taxonomy->name ); ?>"<?php echo esc_attr( checked( $checked, $taxonomy->name ) ); ?>><?php echo esc_html( $taxonomy->label ); ?></label></li>
					<?php endif; ?>
				<?php endforeach; ?>
				</ul>
			</li>
		<?php endforeach; ?>
		</ul>
		<input type="submit" class="button button-primary" value="<?php esc_attr_e( 'save', 'wp-primary-category' ); ?>">
	<?php endif; ?>
	</form>
</div>
