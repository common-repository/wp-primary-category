jQuery( document ).ready( function( $ ) {
	var link = false;

	if ( $.isFunction( wp.template ) ) {
		link = wp.template( 'wp-pc-link' );
	}

	$( WP_PC.taxonomies ).each( function( key, taxonomy) {
		var inputs = $( '#taxonomy-' + taxonomy ).find( '.categorychecklist :checked' );

		if ( inputs.length ) {
			inputs.each( function() {
				if ( ! link ) {
					return false;
				}

				var checkbox = $( this );
				var wrap     = checkbox.closest( '.selectit' );
				var current  = $( '#wp-pc-input-' + taxonomy ).val();

				if ( current === checkbox.val() ) {
					wrap.addClass( 'wp-pc-selected' );
				}

				wrap.append( link( {
					id: checkbox.val(),
					label: WP_PC.link_label,
					taxonomy: taxonomy
				} ) );
			} );
		}

		$( document ).on( 'click', '#taxonomy-' + taxonomy + ' [type="checkbox"]', function() {
			var self = $( this );
			var id   = self.val();
			var wrap = self.closest( '.selectit' );

			if ( ! self.prop( 'checked' ) ) {
				$( '.wp-pc-link-' + taxonomy + '-' + id ).remove();
				if ( wrap.hasClass( 'wp-pc-selected' ) ) {
					wrap.removeClass( 'wp-pc-selected' );
					$( '#wp-pc-input-' + taxonomy ).val( '' );
				}
			} else {
				$( '#taxonomy-' + taxonomy )
					.find( 'input[value="' + self.val() + '"]' )
					.closest( '.selectit' )
					.append( link( {
						id: id,
						label: WP_PC.link_label,
						taxonomy: taxonomy
					} ) );
			}
		} );

		$( '#' + taxonomy + 'checklist' ).on( 'wpListAddEnd', function( data ) {
			if ( ! link ) {
				return false;
			}

			var list  = $( data.target );
			var item  = list.find( '.selectit:first' );
			var input = item.find( '[type="checkbox"]' );

			item.append( link( {
				id: input.val(),
				label: WP_PC.link_label,
				taxonomy: taxonomy
			} ) );
		} );
	} );

	$( document ).on( 'click', '.wp-pc-link', function( e ) {
		e.preventDefault();

		var self        = $( this );
		var taxonomy    = self.data( 'taxonomy' );
		var taxonomy_id = self.data( 'taxonomy-id' );
		var links       = $( '.wp-pc-link-' + taxonomy + '-' + taxonomy_id );
		var wrap        = links.closest( '.selectit' );
		var input       = $( '#wp-pc-input-' + taxonomy );
		var selected    = wrap.hasClass( 'wp-pc-selected' );

		$( '#taxonomy-' + taxonomy )
			.find( '.wp-pc-selected' )
			.removeClass( 'wp-pc-selected' );

		if ( ! selected ) {
			input.val( taxonomy_id );
			wrap.addClass( 'wp-pc-selected' );
		} else {
			input.val( '' );
			wrap.removeClass( 'wp-pc-selected' );
		}
	} );
} );
