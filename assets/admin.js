jQuery( document ).ready( function () {
	jQuery( '.cmb2-select-ajax-select' ).each( function ( i, el ) {
		var field = jQuery( el ).data( 'field' );
		var box = jQuery( el ).data( 'box' );
		var url = cmb2MultiselectAjax.searchUrl;
		url += '&field=' + field + '&box=' + box;
		jQuery( el ).select2({
			ajax: {
				url: url,
			}
		})
	} );
} );
