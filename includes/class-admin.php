<?php

class AffiliateWP_Affiliate_Area_Tabs_Admin {

	public function __construct() {
		add_filter( 'affwp_settings_tabs', array( $this, 'settings_tab' ) );
		
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ), 100 );
		add_action( 'affiliate_area_tabs_tab_row', array( $this, 'render_tab_row' ), 10, 6 );
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
			$tabs = $this->default_tabs();
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

						<?php foreach ( $tabs as $tab_slug => $tab_title ) : $i++; ?>
						
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
				<span class="affiliate-area-tabs-edit"><a href="#"><?php _e( 'Edit tab', 'affiliatewp-affiliate-area-tabs' ); ?></a></span>
			</div>
			
			<div class="aat-repeatable-row-standard-fields" style="display: none;">

				<?php if ( $this->is_default_tab( $tab_slug ) ) : ?>
					<p class="aat-tab-default"><?php _e( 'This is a default AffiliateWP tab.', 'affiliatewp-affiliate-area-tabs' ); ?></p>
				<?php endif; ?>

				<?php 
				/**
				 * Options for custom tabs
				 * 
				 * @since 1.2
				 */
				
				$hidden = $this->is_default_tab( $tab_slug ) ? ' style="display: none;"' : '';
				?>
				<p class="aat-tab-title"<?php echo $hidden; ?>>

					<label for="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][title]"><strong><?php _e( 'Tab Title', 'affiliatewp-affiliate-area-tabs' ); ?></strong></label>
					<span class="description"><?php _e( 'Enter a title for the tab.', 'affiliatewp-affiliate-area-tabs' ); ?></span>
				
					<input id="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][title]" name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][title]" type="text" class="widefat" value="<?php echo esc_attr( $tab_title ); ?>"/>
				</p>

				<p class="aat-tab-content"<?php echo $hidden; ?>>
					<label for="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]"><strong><?php _e( 'Tab Content', 'affiliatewp-affiliate-area-tabs' ); ?></strong></label>
					<span class="description"><?php _e( 'Select which page will be used for the tab\'s content. This page will be blocked for non-affiliates.', 'affiliatewp-affiliate-area-tabs' ); ?></span>
						
					<?php
					$pages = $this->get_pages();
					$tabs  = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );
					?>
					<select id="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]" class="widefat" name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]">
						<?php foreach ( $pages as $id => $title ) :
							$selected = $tabs && isset( $tabs[$key]['id'] ) ? ' ' . selected( $tabs[$key]['id'], $id, false ) : '';
						?>
							<option value="<?php echo $id; ?>"<?php echo $selected; ?>><?php echo $title; ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<?php
				/* Disable tab */

				$disabled_tabs = affiliate_wp()->settings->get( 'affiliate_area_hide_tabs' );
				$checked       = isset( $disabled_tabs[$tab_slug] ) ? true : '';
				?>
				
				<p class="aat-tab-hide">
					<label for="affwp_settings[affiliate_area_hide_tabs][<?php echo $key; ?>]">
						<input type="checkbox" id="affwp_settings[affiliate_area_hide_tabs][<?php echo $key; ?>]" class="affiliate-area-hide-tabs" name="affwp_settings[affiliate_area_hide_tabs][<?php echo $tab_slug; ?>]" value="<?php echo $tab_slug; ?>" <?php checked( $checked, true ); ?> />
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
				if ( ! $this->is_default_tab( $tab_slug ) ) : ?>
				<p><a href="#" class="aat_remove_repeatable"><?php _e( 'Delete tab', 'affiliatewp-affiliate-area-tabs' ); ?></a></p>
				<?php endif; ?>

			</div>
		</div>
		
	<?php 
	}

	/**
	 * Holds an array of the default tabs added by AffiliateWP, previous to 2.1.7.
	 * 
	 * @since 1.2
	 */
	public function default_tabs() {

		$default_tabs = array(
			'urls'      => __( 'Affiliate URLs', 'affiliatewp-affiliate-area-tabs' ),
			'stats'     => __( 'Statistics', 'affiliatewp-affiliate-area-tabs' ),
			'graphs'    => __( 'Graphs', 'affiliatewp-affiliate-area-tabs' ),
			'referrals' => __( 'Referrals', 'affiliatewp-affiliate-area-tabs' ),
			'payouts'   => __( 'Payouts', 'affiliatewp-affiliate-area-tabs' ),
			'visits'    => __( 'Visits', 'affiliatewp-affiliate-area-tabs' ),
			'creatives' => __( 'Creatives', 'affiliatewp-affiliate-area-tabs' ),
			'settings'  => __( 'Settings', 'affiliatewp-affiliate-area-tabs' )
		);

		return $default_tabs;

	}

	/**
	 * Determine if the tab is a default tab or not.
	 * 
	 * @since 1.2
	 */
	public function is_default_tab( $tab_slug ) {

		$return = false;

		if ( array_key_exists( $tab_slug, $this->default_tabs() ) ) {
			$return = true;
		}

		return $return;

	}

}
new AffiliateWP_Affiliate_Area_Tabs_Admin;
