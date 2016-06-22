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
						'settings'  => __( 'Settings', 'affiliatewp-affiliate-area-tabs' )
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

			if ( empty( $tab['title'] ) && ! isset( $tab['id'] ) ) {
				// remove tab row if there's no page or title entered
				unset( $input['affiliate_area_tabs'][ $key ] );
			} elseif ( empty( $tab['title'] ) && isset( $tab['id'] ) ) {
				// if only a page is selected, use the page's title for the tab's title
				$input['affiliate_area_tabs'][ $key ]['title'] = sanitize_text_field( get_the_title( $tab['id'] ) );
			} else {
				$input['affiliate_area_tabs'][ $key ]['title'] = sanitize_text_field( $tab['title'] );
			}

		}

		// re-enable the first default tab if all other tabs are disabled and there's no custom tabs enabled
		if ( isset( $input['affiliate_area_tabs'][0]['id'] ) && $input['affiliate_area_tabs'][0]['id'] === '0' || empty( $input['affiliate_area_tabs'] ) ) {
			if ( count( $input['affiliate_area_hide_tabs'] ) === 7 ) {
				$input['affiliate_area_hide_tabs']['urls'] = false;
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
	 * Returns an array of pages without the Affiliate Area
	 * @since 1.1.2
	 */
	private function get_pages() {

		$pages             = affwp_get_pages();
		$affiliate_area_id = function_exists( 'affwp_get_affiliate_area_page_id' ) ? affwp_get_affiliate_area_page_id() : affiliate_wp()->settings->get( 'affiliates_page' );

		if ( ! empty( $pages[ $affiliate_area_id ] ) ) {
			unset( $pages[ $affiliate_area_id ] );
		}

		return $pages;
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
			 * @since  1.1.2
			 *
			 * @return {string}  The error string
			 * @type {Object}  affwp_tabs
			 */
			var affwp_tabs = {
				row_last    : $( '#affiliatewp-tabs tbody tr:last' ),
				count_all   : $( '#affiliatewp-tabs tbody tr' ).length,
				count_main  : $( '#affiliatewp-tabs tbody tr' ).not( '#affiliatewp-tabs tbody tr:first-child' ).length,
				cboxes      : $( '.form-table tbody tr input[type="checkbox"]' ),
				notice      : {
					default : 'You must have at least one active Affiliate Area tab.',
					blank   : 'You must select a page from the dropdown, and specify a tab title.'
				},
				error       : function( message ) {
					wp.a11y.speak( message, 'assertive' );
					alert( message );
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
				} else {
					$( this ).closest( 'tr' ).find( 'td input, td select' ).val( '' );
				}
			} );
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

					<?php

					$pages = $this->get_pages();

					if ( $tabs ) :

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
