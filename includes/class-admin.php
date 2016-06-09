<?php

class AffiliateWP_Affiliate_Area_Tabs_Admin {

	public function __construct() {
        add_filter( 'affwp_settings_tabs', array( $this, 'settings_tab' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_filter( 'affwp_settings_affiliate_area_tabs_sanitize', array( $this, 'sanitize_tabs' ) );
	}

    /**
	 * Register the new settings tab
	 *
	 * @access public
	 * @since 1.0.0
	 * @return array
	 */
	public function settings_tab( $tabs ) {
		$tabs['affiliate_area_tabs'] = __( 'Affiliate Area Tabs', 'affiliatewp-affiliate-area-tabs' );
		return $tabs;
	}

    /**
     * Register our settings
     *
     * @access public
     * @since 1.0.0
     * @return array
     */
    public function register_settings( ) {

        add_settings_section(
			'affwp_settings_affiliate_area_tabs',
			__return_null(),
			'__return_false',
			'affwp_settings_affiliate_area_tabs'
		);

		if ( affiliatewp_affiliate_area_tabs()->has_1_8() ) {
			// add_settings_field( $id, $title, $callback, $page, $section, $args );
			add_settings_field(
				'affwp_settings[affiliate_area_hide_tabs]', // (string) (required) String for use in the 'id' attribute of tags.
				__( 'Disable Tabs', 'affiliatewp-affiliate-area-tabs' ), // (string) (required) Title of the field.
				array( $this, 'callback_tabs' ), // (string) (required) Function that fills the field with the desired inputs as part of the larger form. Passed a single argument, the $args array. Name and id of the input should match the $id given to this function. The function should echo its output.
				'affwp_settings_affiliate_area_tabs', // (string) (required) The menu page on which to display this field. Should match $menu_slug from add_theme_page() or from do_settings_sections().
				'affwp_settings_affiliate_area_tabs', // (string) (optional) The section of the settings page in which to show the box (default or a section you added with add_settings_section(), look at the page in the source to see what the existing ones are.)
				array( // (array) (optional) Additional arguments that are passed to the $callback function. The 'label_for' key/value pair can be used to format the field title like so: <label for="value">$title</label>.
					'name'        => 'affiliate_area_hide_tabs',
					'id'          => 'affiliate-area-hide-tabs',
					'description' => __( 'Select tabs to disable. These tabs will no longer appear in the Affiliate Area.', 'affiliatewp-affiliate-area-tabs' ),
					'tabs'        => array(
						'urls'      => __( 'Affiliate URLs', 'affiliatewp-affiliate-area-tabs' ),
						'stats'     => __( 'Statistics', 'affiliatewp-affiliate-area-tabs' ),
						'graphs'    => __( 'Graphs', 'affiliatewp-affiliate-area-tabs' ),
						'referrals' => __( 'Referrals', 'affiliatewp-affiliate-area-tabs' ),
						'visits'    => __( 'Visits', 'affiliatewp-affiliate-area-tabs' ),
						'creatives' => __( 'Creatives', 'affiliatewp-affiliate-area-tabs' ),
						'settings'  => __( 'Settings', 'affiliatewp-affiliate-area-tabs' ),
					)
				)
			);
		}

		add_settings_field(
			'affwp_settings[affiliate_area_tabs]',
			__( 'Affiliate Area Tabs', 'affiliatewp-affiliate-area-tabs' ),
			array( $this, 'tabs_table' ),
			'affwp_settings_affiliate_area_tabs',
			'affwp_settings_affiliate_area_tabs'
		);

    }

    /**
     * Sanitize tabs
     * @since 1.0.0
     */
    public function sanitize_tabs( $input ) {

		$hide_tabs_array = ! empty( $input['affiliate_area_hide_tabs'] ) ? $input['affiliate_area_hide_tabs'] : '';

		if ( $hide_tabs_array ) {
			foreach ( $hide_tabs_array as $key => $tab ) {
				$input['affiliate_area_hide_tabs'][$key] = isset( $input['affiliate_area_hide_tabs'][$key] ) && true == $input['affiliate_area_hide_tabs'][$key] ? true : false;
			}
		}

		// clear out array if no tabs are selected for removal
		if ( ! $hide_tabs_array ) {
			$input['affiliate_area_hide_tabs'] = array();
		}

        foreach ( $input['affiliate_area_tabs'] as $key => $tab ) {

            if ( empty( $tab['title'] ) ) {
                unset( $input['affiliate_area_tabs'][ $key ] );
            } else {
                $input['affiliate_area_tabs'][ $key ]['title'] = sanitize_text_field( $tab['title'] );
            }

        }

        return $input;
    }

	/**
     * Hide existing AffiliateWP tabs
	 *
     * @since 1.1
     */
	public function callback_tabs( $args ) {

		$options = affiliate_wp()->settings->get( 'affiliate_area_hide_tabs' );
		$tabs    = $args['tabs'];

		foreach ( $tabs as $tab => $label ) :

		$checked = isset( $options[$tab] ) ? $options[$tab] : '';
	?>
		<label for="<?php echo $args['id']; ?>-<?php echo $tab; ?>">
			<input type="checkbox" id="<?php echo $args['id']; ?>-<?php echo $tab; ?>" name="affwp_settings[<?php echo $args['name']; ?>][<?php echo $tab; ?>]" value="<?php echo $tab; ?>" <?php checked( $checked, true ); ?> />
			<?php echo $label; ?>
		</label>
		<br />
	<?php endforeach; ?>
	<p class="description"><?php echo $args['description']; ?></p>
		<?php
	}

    /**
     * Render the table
     * @since 1.0.0
     */
    public function tabs_table() {

        $tabs  = affiliatewp_affiliate_area_tabs()->get_tabs();
        $count = count( $tabs );

        ?>
        <script type="text/javascript">
        jQuery( document ).ready( function( $ ) {

            /**
             * Primary affwp_tabs object
             *
             *
             * affwp_tabs.error
             *     Returns a translatable error, via
             *     wp.a11y.speak, shown in an alert,
             *     as well the console.
             *
             * affwp_tabs.val_cb
             *    Checks for checkbox states and disables submit,
             *    if there are also no custom tabs present
             *    with non-empty values.
             *
             * val_custom
             *    Returns true if the values for the first custom
             *    tab row title and select both are not empty.
             *    Defaults to false, as this factory function
             *    in itself does not directly generate an error if false.
             *
             * @since  1.1.2
             *
             * @return {string}  The error string
             * @type {Object}  affwp_tabs
             */
            var affwp_tabs = {
                debug: false,
                enable: function() {
                    $( '#submit' ).prop( 'disabled', false );
                },
                disable: function() {
                    $( '#submit' ).prop( 'disabled', true );
                },
                custom_title: $( '#affiliatewp-tabs tbody tr:nth-child(2) td input' ),
                custom_sel: $( '#affiliatewp-tabs tbody tr:nth-child(2) td select' ),
                row_last: $( '#affiliatewp-tabs tbody tr:last' ),
                count_all: $( '#affiliatewp-tabs tbody tr' ).length,
                count_main: $( '#affiliatewp-tabs tbody tr' ).not( '#affiliatewp-tabs tbody tr:first-child' ).length,
                cboxes: $( '.form-table tbody tr input[type="checkbox"]' ),
                notice: {
                    default: 'You must have at least one active Affiliate Area tab.',
                    blank: 'You must select a page from the dropdown, and specify a tab title.'
                },
                val_custom: function() {
                    var ct, cp;
                    ct = $.trim( affwp_tabs.custom_title.val() );
                    cp = $.trim( affwp_tabs.custom_sel.val() );
                    if ( ct && ( cp === "0" ) ) {
                        return true;
                    } else {
                        return false;
                    }
                },
                error: function( message ) {
                    wp.a11y.speak( message, 'assertive' );
                    alert( message );
                    if ( affwp_tabs.debug ) {
                        console.error( message );
                    }
                },
                val_cb: function() {
                    if ( ( affwp_tabs.cboxes.length == affwp_tabs.cboxes.filter( ":checked" ).length ) ) {
                        return false;
                    } else {
                        return true;
                    }
                },
                is_valid: true,
            }

            function check() {
                affwp_tabs.is_valid = ( affwp_tabs.val_custom() && affwp_tabs.val_cb() ) ? true : false;
                if ( affwp_tabs.custom_title.val() === "" && affwp_tabs.custom_sel.val() === "0" && !affwp_tabs.val_cb() ) {
                    affwp_tabs.disable();
                }
            }

            /**
             * Prevents the enter key from creating a new row
             *
             * @since  0.1
             *
             * @return void
             */
            $( '#affiliatewp-tabs' ).on( 'keyup keypress', function( e ) {
                var keyCode = e.keyCode || e.which;

                if ( keyCode === 13 ) {
                    e.preventDefault();
                    return false;
                }
            } );

            /**
             * Adds a new affiliate area tab
             *
             * @since  0.1
             *
             *
             * @return {mixed}    A new custom affiliate area tab
             */
            $( '#affwp_new_tab' ).on( 'click', function( e ) {

                e.preventDefault();

                // Clone the row and its child's data and events
                clone = affwp_tabs.row_last.clone( true );

                // empty values
                clone.find( 'td input, td select' ).val( '' );

                clone.find( 'input, select' ).each( function() {
                    var name = $( this ).attr( 'name' );

                    name = name.replace( /\[(\d+)\]/, '[' + parseInt( affwp_tabs.count_all ) + ']' );

                    $( this ).attr( 'name', name ).attr( 'id', name );
                } );

                // insert new clone after existing row
                clone.insertAfter( affwp_tabs.row_last );

            } );

            /**
             * A listener bound to checkbox state changes.
             * This is identical to the function
             * affwp_tabs.val_cb(), but accounts for
             * live changes occurring in the dom.
             *
             * Returns an error, and disables the submit button if:
             * 1. All core tabs are disabled, and
             * 2. No custom tabs are present.
             *
             * @since  1.1.2
             *
             */
            affwp_tabs.cboxes.change( function() {

                if ( ( affwp_tabs.cboxes.length == affwp_tabs.cboxes.filter( ":checked" ).length ) && !affwp_tabs.custom_title.val() ) {
                    affwp_tabs.is_valid = false;
                    affwp_tabs.disable();
                    affwp_tabs.error( affwp_tabs.notice.default );
                } else {
                    affwp_tabs.is_valid = true;
                    affwp_tabs.enable();
                }
            } );

            /**
             * Bound to the blur of the first custom tab input title.
             * Enables or disables submit, and fires an error,
             * on the following conditions:
             *
             * If entering a tab title,
             *     Ensure that the value is checked on blur, and
             *     ensure that the value is not empty, nor whitespace.
             * Or:
             *     If there are no core tabs enabled, then
             *     Check for at least once custom tab.
             *     If none are found, return an error,
             *     and disable the submit button.
             *
             * @since  1.1.2
             *
             */
            affwp_tabs.custom_title.blur( function() {
                if ( $( this ).val() === "" && affwp_tabs.custom_sel.val() === "0" && !affwp_tabs.val_cb() ) {
                    affwp_tabs.disable();
                    affwp_tabs.error( affwp_tabs.notice.blank );
                } else if ( $( this ).val() !== "" && affwp_tabs.custom_sel.val() === "0" && !affwp_tabs.val_cb() ) {
                    affwp_tabs.disable();
                    affwp_tabs.error( affwp_tabs.notice.blank );
                } else if ( $( this ).val() === "" && !affwp_tabs.val_cb() && affwp_tabs.custom_sel.val() !== "0" ) {
                    affwp_tabs.disable();
                    affwp_tabs.error( affwp_tabs.notice.blank );
                } else {
                    affwp_tabs.enable();
                }

                if ( affwp_tabs.val_cb() && affwp_tabs.val_custom() ) {
                    affwp_tabs.disable();
                }
            } );

            /**
             * Bound to the blur of the first custom tab page select.
             * Enables or disables submit, and fires an error,
             * on the following conditions:
             *
             * If selecting a tab page,
             *     Check if matches "0", the default value
             *     of an empty select.
             * If a match is found:
             *     Check how many core tabs are disabled, and
             *     check the value of the first custom tab title.
             *
             * Disables submit and returns an error if:
             *     All core tabs disabled,
             *     The first custom tab title is empty, and
             *     the select is empty.
             *
             * @since  1.1.2
             *
             */
            affwp_tabs.custom_sel.blur( function() {
                // A select returns a zero - as a string - if empty.
                if ( affwp_tabs.custom_sel.val() === "0" ) {
                    if ( affwp_tabs.custom_title.val() === "" && !affwp_tabs.val_cb() ) {
                        affwp_tabs.disable();
                        affwp_tabs.error( affwp_tabs.notice.blank );
                    }
                } else if ( affwp_tabs.custom_title.val() === "" && !affwp_tabs.val_cb() ) {
                    affwp_tabs.disable();
                    affwp_tabs.error( affwp_tabs.notice.blank );
                } else {
                    affwp_tabs.enable();
                }
            } );

            /**
             * Removes a custom affiliate area tab
             *
             * @since  1.1.2
             *
             */
            $( '.affwp_remove_tab' ).on( 'click', function( e ) {
                e.preventDefault();

                // Instead of removing the last row, clear out the values
                if ( affwp_tabs.count_main !== 1 ) {
                    $( this ).parent().parent().remove();
                } else if ( !affwp_tabs.val_custom() && !affwp_tabs.val_cb() ) {
                    affwp_tabs.disable();
                    affwp_tabs.error( affwp_tabs.notice.default );
                } else {
                    $( this ).closest( 'tr' ).find( 'td input, td select' ).val( '' );
                }

            } );

            check();

        } );
        </script>
        <style type="text/css">
        #affiliatewp-tabs th { padding-left: 10px; }
        .affwp_remove_tab { margin: 8px 0 0 0; cursor: pointer; width: 10px; height: 10px; display: inline-block; text-indent: -9999px; overflow: hidden; }
        .affwp_remove_tab:active, .affwp_remove_tab:hover { background-position: -10px 0!important }
        </style>
        <form id="affiliatewp-tabs-form">
            <table id="affiliatewp-tabs" class="form-table wp-list-table widefat posts">
                <thead>
                    <tr>
                        <th style="width:50%;"><?php _e( 'Tab Content', 'affiliatewp-affiliate-area-tabs' ); ?></th>
                        <th><?php _e( 'Tab Title', 'affiliatewp-affiliate-area-tabs' ); ?></th>
                        <th style="width:5%;"></th>
                    </tr>
                </thead>
                <tbody>

					<tr>
						<td><p class="description"><?php _e( 'Select which page will be used for the tab\'s content. This page will be blocked for non-affiliates.', 'affiliatewp-affiliate-area-tabs' ); ?></p></td>
						<td><p class="description"><?php _e( 'Enter a title for the tab.', 'affiliatewp-affiliate-area-tabs' ); ?></p></td>
						<td></td>
					</tr>

                    <?php if ( $tabs ) :

						$pages = affwp_get_pages();

						// remove the affiliate area from the pages array so it can never be selected
						if ( $pages ) {
							foreach ( $pages as $key => $page ) {
								if ( $key === affiliate_wp()->settings->get( 'affiliates_page' ) ) {
									unset( $pages[$key] );
								}
							}
						}

						foreach( $tabs as $key => $tab ) :

                            ?>
                            <tr>
                                <td>
                                    <select class="widefat" name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]">
                                        <?php foreach( $pages as $id => $title ) : ?>
                                            <option value="<?php echo $id; ?>"<?php selected( $tab['id'], $id ); ?>><?php echo $title; ?></option>
                                        <?php endforeach; ?>

                                    </select>

                                </td>
                                <td>
                                    <input name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][title]" type="text" class="widefat" value="<?php echo esc_attr( $tab['title'] ); ?>"/>
                                </td>
                                <td>
                                    <a href="#" class="affwp_remove_tab" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                    <?php endif; ?>

                    <?php if ( empty( $tabs ) ) :
                        $count = 0;
                        $pages = affwp_get_pages();

                        ?>
                        <tr>
                            <td>
                                <select class="widefat" name="affwp_settings[affiliate_area_tabs][<?php echo $count; ?>][id]">
                                    <?php foreach( $pages as $id => $title ) : ?>
                                        <option value="<?php echo $id; ?>"><?php echo $title; ?></option>
                                    <?php endforeach; ?>

                                </select>

                            </td>
                            <td>
                                <input name="affwp_settings[affiliate_area_tabs][<?php echo $count; ?>][title]" type="text" class="widefat" value="" />
                            </td>
                            <td>
                                <a href="#" class="affwp_remove_tab" style="background: url(<?php echo admin_url('/images/xit.gif'); ?>) no-repeat;">&times;</a>
                            </td>
                        </tr>
                    <?php endif; ?>

                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="1">
                            <button id="affwp_new_tab" name="affwp_new_tab" class="button"><?php _e( 'Add New Tab', 'affiliatewp-affiliate-area-tabs' ); ?></button>
                        </th>
                        <th colspan="3">

                        </th>
                    </tr>
                </tfoot>
            </table>
        </form>
<?php
    }

}
new AffiliateWP_Affiliate_Area_Tabs_Admin;
