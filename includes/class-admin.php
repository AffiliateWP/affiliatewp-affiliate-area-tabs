<?php

class AffiliateWP_Affiliate_Area_Tabs_Admin {

	public function __construct() {
		add_filter( 'affwp_settings_tabs', array( $this, 'settings_tab' ) );
		
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 100 );
		add_action( 'affiliate_area_tabs_tab_row', array( $this, 'render_tab_row' ), 10, 6 );

		add_filter( 'pre_update_option_affwp_settings', array( $this, 'update_settings' ), 10, 2 );
	
	}

	/**
	 * Update settings before they are saved.
	 *
	 * @access public
	 * @since 1.2
	 */
	public function update_settings( $new_value, $old_value ) {

		if ( $new_value['affiliate_area_tabs'] ) {
			foreach ( $new_value['affiliate_area_tabs'] as $key => $tab_array ) {

				// If the tab does not have a slug it's a custom one (core tabs already have slugs).
				if ( empty( $tab_array['slug'] ) ) {
					// Create slug for custom tabs
					$new_value['affiliate_area_tabs'][$key]['slug'] = affiliatewp_affiliate_area_tabs()->make_slug( $tab_array['title'] );
				}

			}
		}

		return $new_value;

	}

	/**
	 * Scripts
	 *
	 * @access public
	 * @since 1.2
	 */
	public function scripts() {

		// Admin CSS file.
		$screen = get_current_screen();

		$js_dir  = AFFWP_AAT_PLUGIN_URL . 'assets/js/';
		
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		
		$admin_deps = array();

		wp_register_style( 'aat-admin', AFFWP_AAT_PLUGIN_URL . 'assets/css/admin.css', array( 'dashicons' ), AFFWP_AAT_VERSION );
		wp_register_script( 'aat-admin-scripts', $js_dir . 'admin-scripts' . $suffix . '.js', $admin_deps, AFFWP_AAT_VERSION, false );

		if (
			$screen->id === 'affiliates_page_affiliate-wp-settings' &&
			isset( $_GET['tab'] ) && $_GET['tab'] === 'affiliate_area_tabs'
		) {
			wp_enqueue_style( 'aat-admin' );
			wp_enqueue_script( 'aat-admin-scripts' );
		}

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
	 * Get a list of all tabs
	 *
	 * @access public
	 * @since 1.1.6
	 * @return array $tabs The tabs to output on the Affiliates -> Settings -> Affiliate Area Tabs page.
	 */
	public function get_tabs() {

		$tabs = array();

		if ( function_exists( 'affwp_get_affiliate_area_tabs' ) ) {
			$tabs = affwp_get_affiliate_area_tabs();
		} else {
			/**
			 * If a previous version of AffiliateWP is being used, output the
			 * hard-coded tabs as before.
			 */
			$tabs = affiliatewp_affiliate_area_tabs()->default_tabs();
		}

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

		add_settings_field(
			'affwp_settings[affiliate_area_tabs_list]',
			__( 'Affiliate Area Tabs', 'affiliatewp-affiliate-area-tabs' ),
			array( $this, 'tabs_list' ),
			'affwp_settings_affiliate_area_tabs',
			'affwp_settings_affiliate_area_tabs'
		);

	}

	/**
	 * Returns an array of pages without the Affiliate Area.
	 * 
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
	 * Render the tabs list
	 * @since 1.0.0
	 */
	public function tabs_list() {
		
			$tabs  = $this->get_tabs();
			$count = count( $tabs );

			$i = 0;
			?>
			
			<form id="affiliatewp-tabs-list-form">
			
				<div class="widefat aat_repeatable_table">

					<div class="aat-repeatables-wrap">
					<?php 
						$current_tabs  = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );
						
						foreach ( $tabs as $tab_slug => $tab_title ) : $i++; ?>
						
						<?php
						$key = $i;
						$args = array();
						$post_id = '';
						$index = $key;
					?>

						<div class="aat_repeatable_row" data-key="<?php echo esc_attr( $key ); ?>">
							<?php do_action( 'affiliate_area_tabs_tab_row', $key, $args, $post_id, $index, $tab_slug, $tab_title ); ?>
						</div>
						
						<?php endforeach; ?>

						<div class="aat-add-repeatable-row">
							<div class="submit" style="float: none; clear:both; padding: 4px 4px 0 0;">
								<button class="button-secondary aat-add-repeatable"><?php _e( 'Add New Tab', 'affiliatewp-affiliate-area-tabs' ); ?></button>
							</div>
						</div>

					</div>
				</div>

			</form>
	<?php
	}
	
	/**
	 * Individual Tab Row
	 *
	 * Used to output a row for each tab.
	 * Can be called directly, or attached to an action.
	 *
	 * @since 1.2
	 *
	 * @param       $key
	 * @param array $args
	 * @param       $post_id
	 */
	public function render_tab_row( $key, $args = array(), $post_id, $index, $tab_slug, $tab_title ) {
	
		$defaults = array(
			'name'   => null,
			'amount' => null
		);

		$args = wp_parse_args( $args, $defaults );

	?>

		<div class="aat-draghandle-anchor">
			<span class="dashicons dashicons-move" title="<?php _e( 'Click and drag to re-order', 'affiliatewp-affiliate-area-tabs' ); ?>"></span>
		</div>

		<div class="aat-repeatable-row-header">

			<div class="aat-repeatable-row-title">
				<?php printf( __( '%s', '' ), '<span class="affiliate-area-tabs-title">' . $tab_title . '</span><span class="aat-tab-number"> (Tab <span class="aat-tab-number-key">' . $key . '</span>)</span>' ); ?>
				<span class="affiliate-area-tabs-edit">
					<span class="dashicons dashicons-arrow-down"></span>
				</span>
			</div>
			
			<div class="aat-repeatable-row-standard-fields" style="display: none;">

				<?php if ( $this->is_default_tab( $tab_slug ) ) : ?>
					<p class="aat-tab-default"><?php _e( 'This is a default AffiliateWP tab.', 'affiliatewp-affiliate-area-tabs' ); ?></p>
				<?php endif; ?>

				<?php

				/**
				 * Hide a field if it's not a custom tab.
				 */
				$hidden = ! $this->is_custom_tab( $tab_slug ) ? ' style="display: none;"' : '';

				/**
				 * Tab title.
				 */
				?>

				<p class="aat-tab-title"<?php echo $hidden; ?>>

					<label for="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][title]"><strong><?php _e( 'Tab Title', 'affiliatewp-affiliate-area-tabs' ); ?></strong></label>
					<span class="description"><?php _e( 'Enter a title for the tab.', 'affiliatewp-affiliate-area-tabs' ); ?></span>
				
					<input id="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][title]" name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][title]" type="text" class="widefat" value="<?php echo esc_attr( $tab_title ); ?>"/>

					<?php
					/**
					 * This makes sure the core tabs have their slug correctly saved as per the default_tabs() method.
					 * Custom tab slugs are generated in update_settings()
					 */
					?>
					<input name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][slug]" type="hidden" value="<?php echo $tab_slug; ?>" />

				</p>

				<?php
				/**
				 * Tab content.
				 */
				?>
				<p class="aat-tab-content"<?php echo $hidden; ?>>
					<label for="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]"><strong><?php _e( 'Tab Content', 'affiliatewp-affiliate-area-tabs' ); ?></strong></label>
					<span class="description"><?php _e( 'Select which page will be used for the tab\'s content. This page will be blocked for non-affiliates.', 'affiliatewp-affiliate-area-tabs' ); ?></span>
						
					<?php
					$pages = $this->get_pages();
					
					$tabs  = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );
					?>
					<select id="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]" class="widefat" name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]">
						<?php foreach ( $pages as $id => $title ) :
							/**
							 * Backwards Compatibility.
							 */
							$selected = $tabs && isset( $tabs[$key]['id'] ) ? ' ' . selected( $tabs[$key]['id'], $id, false ) : '';
						?>
							<option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $title; ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<?php
				$checked = isset( $tabs[$key]['hide'] ) && 'yes' === $tabs[$key]['hide'] ? 'yes' : 'no';
				?>
				<p class="aat-tab-hide">
					<label for="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][hide]">
						<input type="checkbox" id="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][hide]" class="affiliate-area-hide-tabs" name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][hide]" value="yes" <?php checked( $checked, 'yes' ); ?> />
						<?php _e( 'Hide tab in Affiliate Area', 'affiliatewp-affiliate-area-tabs' ); ?>
					</label>
				</p>

				<?php 
				/**
				 * Delete custom tab.
				 * Only custom tabs can be deleted.
				 * 
				 * @since 1.2
				 */
				if ( $this->is_custom_tab( $tab_slug ) ) : ?>
				<p><a href="#" class="aat_remove_repeatable"><?php _e( 'Delete tab', 'affiliatewp-affiliate-area-tabs' ); ?></a></p>
				<?php endif; ?>

			</div>
		</div>
		
	<?php 
	}

	/**
	 * Determine if the tab is a custom tab or not.
	 * A custom tab is one that has been added using the "Add New Tab" button.
	 * 
	 * @since 1.2
	 * @uses get_custom_tab_slugs()
	 * 
	 * @return boolean True if the tab is a custom tab, false otherwise.
	 */
	private function is_custom_tab( $tab_slug = '' ) {
		return in_array( $tab_slug, $this->get_custom_tab_slugs() );
	}

	/**
	 * Get custom tab slugs
	 * 
	 * @since 1.2
	 * 
	 * @return array $custom_tab_slugs Array of custom tab slugs
	 */
	private function get_custom_tab_slugs() {

		$tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );

		$custom_tab_slugs = array();

		if ( $tabs ) {
			foreach( $tabs as $tab_array ) {
				// Custom tabs have a page ID set.
				if ( $tab_array['id'] !== '0' ) {
					$custom_tab_slugs[] = $tab_array['slug'];
				}
			}
		}

		return $custom_tab_slugs;

	}

	/**
	 * Determine if the tab is a default tab or not.
	 * 
	 * @since 1.2
	 */
	public function is_default_tab( $tab_slug ) {

		$return = false;

		if ( array_key_exists( $tab_slug, affiliatewp_affiliate_area_tabs()->default_tabs() ) ) {
			$return = true;
		}

		return $return;

	}

}
new AffiliateWP_Affiliate_Area_Tabs_Admin;
