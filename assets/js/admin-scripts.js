jQuery(document).ready(function ($) {

	/**
	 * Affiliate Area Tabs Configuration
	 */
	var AAT_Configuration = {
		init : function() {
			this.add();
			this.edit();
			this.move();
			this.remove();
		},

		clone_repeatable : function(row) {

			// Retrieve the highest current key
			var key = highest = 1;
			
			row.parent().find( '.aat_repeatable_row' ).each(function() {
				var current = $(this).data( 'key' );
				if( parseInt( current ) > highest ) {
					highest = current;
				}
			});
			
			key = highest += 1;

			clone = row.clone();

			// Manually update any select box values.
			clone.find( 'select' ).each(function() {
				$( this ).val( row.find( 'select[name="' + $( this ).attr( 'name' ) + '"]' ).val() );
			});

			// Update the data-key.
			clone.attr( 'data-key', key );

			// Update any input or select menu's name and ID attribute.
			clone.find( 'input, select' ).val( '' ).each(function() {
				var name = $( this ).attr( 'name' );
				var id   = $( this ).attr( 'id' );

				if ( name ) {
					name = name.replace( /\[(\d+)\]/, '[' + parseInt( key ) + ']');
					$( this ).attr( 'name', name );
				}

				$( this ).attr( 'data-key', key );

				if ( typeof id != 'undefined' ) {
					id = id.replace( /(\d+)/, parseInt( key ) );
					$( this ).attr( 'id', id );
				}

			});
			
			// Update the label "for" attribute.
			clone.find( 'label' ).val( '' ).each(function() {
				var labelFor = $( this ).attr( 'for' );

				if ( typeof labelFor != 'undefined' ) {
					labelFor = labelFor.replace( /(\d+)/, parseInt( key ) );
					$( this ).attr( 'for', labelFor );
				}

			});

			// Change the tab's title when the last one is cloned.
			clone.find( '.affiliate-area-tabs-title' ).each(function() {
				$( this ).html( 'New Custom Tab' );
			});

			// Remove the "(Default AffiliateWP tab)" text if a custom tab is inserted after a default tab.
			clone.find( '.aat-tab-default' ).remove();

			// Increase the tab number key.
			clone.find( '.aat-tab-number-key' ).each(function() {
				$( this ).text( parseInt( key ) );
			});

			// Uncheck "Hide tab in Affiliate Area" option if last one was selected.
			clone.find( '.affiliate-area-hide-tabs' ).each( function() {
				$( this ).val( parseInt( key ) ).removeAttr('checked');
			});

			// Show the the tab title and content for custom tabs.
			clone.find( '.aat-tab-title, .aat-tab-content').show();

			return clone;
		},

		add : function() {

			$( document.body ).on( 'click', '.submit .aat-add-repeatable', function(e) {

				e.preventDefault();

				var button = $( this ),
					row    = button.parent().parent().prev( '.aat_repeatable_row' ),
					clone  = AAT_Configuration.clone_repeatable(row);

				clone.insertAfter( row );
				clone.find( '.aat-repeatable-row-standard-fields' ).show();
				clone.find('input, select').filter(':visible').eq(0).focus();

			});
		},

		edit : function() {
			// Open settings for each tab.
			$( document.body ).on( 'click', '.aat-repeatable-row-title', function(e) {
				e.preventDefault();
				$(this).next( '.aat-repeatable-row-standard-fields' ).slideToggle();
			});
		},

		move : function() {

			$(".aat_repeatable_table .aat-repeatables-wrap").sortable({
				handle: '.aat-draghandle-anchor', items: '.aat_repeatable_row', opacity: 0.6, cursor: 'move', axis: 'y', 
				
				// update: function() {
				// 	var count  = 0;
				// 	$(this).find( '.aat_repeatable_row' ).each(function() {
				// 		$(this).find( 'input.edd_repeatable_index' ).each(function() {
				// 			$( this ).val( count );
				// 		});
				// 		count++;
				// 	});
				// }
				
			});

		},

		remove : function() {
			$( document.body ).on( 'click', '.aat_remove_repeatable', function(e) {
				e.preventDefault();

				// Confirm that the user wants to delete the tab.
				var hasConfirmed = confirm( 'Are you sure you want to delete this tab?' );

				if ( ! hasConfirmed ) {
					return;
				}

				var row   = $(this).parents( '.aat_repeatable_row' ),
					count = row.parent().find( '.aat_repeatable_row' ).length,
				//	type  = $(this).data('type'),
				//	repeatable = 'div.edd_repeatable_' + type + 's',
					focusElement,
					focusable,
					firstFocusable;

					// Set focus on next element if removing the first row. Otherwise set focus on previous element.
					if ( $(this).is( '.ui-sortable .aat_repeatable_row:first-child .aat_remove_repeatable' ) ) {
						focusElement  = row.next( '.aat_repeatable_row' );
					} else {
						focusElement  = row.prev( '.aat_repeatable_row' );
					}

					focusable  = focusElement.find( 'select, input, textarea, button' ).filter( ':visible' );
					firstFocusable = focusable.eq(0);

				// if ( type === 'price' ) {
				// 	var price_row_id = row.data('key');
				// 	/** remove from price condition */
				// 	$( '.edd_repeatable_condition_field option[value="' + price_row_id + '"]' ).remove();
				// }
					
				$( 'input, select', row ).val( '' );
				row.fadeOut( 'fast' ).remove();
				firstFocusable.focus();
					
				// if ( count > 1 ) {
				// 	$( 'input, select', row ).val( '' );
				// 	row.fadeOut( 'fast' ).remove();
				// 	firstFocusable.focus();
				// } else {
				// 	switch( type ) {
				// 		case 'price' :
				// 			alert( edd_vars.one_price_min );
				// 			break;
				// 		case 'file' :
				// 			$( 'input, select', row ).val( '' );
				// 			break;
				// 		default:
				// 			alert( edd_vars.one_field_min );
				// 			break;
				// 	}
				// }

				// Re-index after deleting.

				// $(repeatable).each( function( rowIndex ) {
				// 	$(this).find( 'input, select' ).each(function() {
				// 		var name = $( this ).attr( 'name' );
				// 		name = name.replace( /\[(\d+)\]/, '[' + rowIndex+ ']');
				// 		$( this ).attr( 'name', name ).attr( 'id', name );
				// 	});
				// });
				
			});
		},

	};

	AAT_Configuration.init();

});


