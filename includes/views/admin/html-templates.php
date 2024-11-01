<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script type="text/html" id="tmpl-wp-pc-link">
	<a href="#" class="wp-pc-link wp-pc-link-{{ data.taxonomy }}-{{ data.id }}" data-taxonomy="{{ data.taxonomy }}" data-taxonomy-id="{{ data.id }}">{{ data.label }}</a>
</script>
