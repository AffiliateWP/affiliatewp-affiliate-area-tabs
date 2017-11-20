jQuery(document).ready(function ($) {
    
        // Expand/Collapse tabs
        $( document.body ).on( 'click', '.aat-hide-show-tabs', function(e) {
    
            e.preventDefault();
    
            var tabs = $( '.aat-repeatable-row-standard-fields' );
            var el = $(this);
    
            // Change text.
            el.text() == el.data("text-swap") ? el.text( el.data("text-original") ) : el.text( el.data("text-swap") );
    
            // Show/hide tabs.
            if ( el.text() === el.data("text-swap") ) {
                tabs.show();
            } else if (  el.text() === el.data("text-original") ) {
                tabs.hide();
            }
    
        });

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
                    $( this ).val('yes').removeAttr('checked');
                });
    
                // Show the the tab title and content for custom tabs.
                clone.find( '.aat-tab-title, .aat-tab-content').show();
    
                return clone;
            },
    
            add : function() {
    
                $( document.body ).on( 'click', '.aat-add-repeatable', function(e) {
    
                    e.preventDefault();
    
                    var button = $( this ),
                        row    = button.parent().prev( '.aat_repeatable_row' ),
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
    
                    $(this).next( '.aat-repeatable-row-standard-fields' ).toggle();
                    $(this).find( '.affiliate-area-tabs-edit .dashicons' ).toggleClass( 'dashicons-arrow-down dashicons-arrow-up' );
                });
            },
    
            move : function() {
    
                $(".aat_repeatable_table .aat-repeatables-wrap").sortable({
                    handle: '.aat-draghandle-anchor', items: '.aat_repeatable_row', opacity: 0.6, cursor: 'move', axis: 'y', 
                    
                    update: function() {
    
                        var key  = 1;
                        
                        $(this).find( '.aat_repeatable_row' ).each(function() {
    
                            // Update the data-key attribute.
                            $( this ).attr( 'data-key', key );
    
                            // Update the tab number key. Example (Tab 5)
                            $(this).find( '.aat-tab-number-key' ).text( parseInt( key ) );
    
                            // Update any input or select menu's name and ID attribute.
                            $(this).find( 'input, select' ).each(function() {
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
                            $(this).find( 'label' ).val( '' ).each(function() {
                                var labelFor = $( this ).attr( 'for' );
    
                                if ( typeof labelFor != 'undefined' ) {
                                    labelFor = labelFor.replace( /(\d+)/, parseInt( key ) );
                                    $( this ).attr( 'for', labelFor );
                                }
    
                            });
    
                            key++;
    
                        });
                    }
                    
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
    
                    $( 'input, select', row ).val( '' );
                    row.remove();
                    firstFocusable.focus();
    
                    // Re-index after deleting.
                
                    var key  = 1;
    
                    $( '.aat-repeatables-wrap' ).find( '.aat_repeatable_row' ).each(function() {
    
                        // Update the data-key attribute.
                        $( this ).attr( 'data-key', key );
                        
                        // Update the tab number key. Example (Tab 5)
                        $(this).find( '.aat-tab-number-key' ).text( parseInt( key ) );
                        
                        // Update any input or select menu's name and ID attribute.
                        $(this).find( 'input, select' ).each(function() {
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
                        $(this).find( 'label' ).val( '' ).each(function() {
                            var labelFor = $( this ).attr( 'for' );
    
                            if ( typeof labelFor != 'undefined' ) {
                                labelFor = labelFor.replace( /(\d+)/, parseInt( key ) );
                                $( this ).attr( 'for', labelFor );
                            }
    
                        });
    
                        key++;
    
                    });
    
                });
            },
    
        };
    
        AAT_Configuration.init();
    
    });
    
    
    