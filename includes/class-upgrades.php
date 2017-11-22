<?php

class Affiliate_Area_Tabs_Upgrades {

	/**
	 * Signals whether the upgrade was successful.
	 *
	 * @access public
	 * @var    bool
	 */
	private $upgraded = false;

	/**
	 * Affiliate Area Tabs version.
	 *
	 * @access private
	 * @since  1.2
	 * @var    string
	 */
	private $version;

	/**
	 * Sets up the Upgrades class instance.
	 *
	 * @access public
	 */
	public function __construct() {

		$this->version = get_option( 'affwp_aat_version' );

		add_action( 'admin_init', array( $this, 'init' ), -9999 );

	}

	/**
	 * Initializes upgrade routines for the current version of Affiliate Area Tabs.
	 *
	 * @access public
	 */
	public function init() {

		if ( empty( $this->version ) ) {
			$this->version = '1.1.6'; // last version that didn't have the version option set
		}

		if ( version_compare( $this->version, '1.2', '<' ) ) {
			$this->v12_upgrade();
		}

		// If upgrades have occurred
		if ( $this->upgraded ) {
			update_option( 'affwp_aat_version_upgraded_from', $this->version );
			update_option( 'affwp_aat_version', AFFWP_AAT_VERSION );
		}

	}

	/**
	 * Performs database upgrades for version 1.2.
	 *
	 * @access private
	 * @since 1.2
	 */
	private function v12_upgrade() {
		
		// Remove filter from main class during upgrade routine.
		remove_filter( 'affwp_affiliate_area_tabs', array( affiliatewp_affiliate_area_tabs(), 'affiliate_area_tabs' ) );

		// Get the current Affiliate Area Tabs.
		$affiliate_area_tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs' );

		if ( $affiliate_area_tabs ) {

			foreach ( $affiliate_area_tabs as $key => $tab_array ) {

				$slug = affiliatewp_affiliate_area_tabs()->functions->make_slug( $tab_array['title'] );

				/**
				 * If AffiliateWP 2.1.7 or newer, check slugs against the affwp_get_affiliate_area_tabs() function.
				 */
				if ( affiliatewp_affiliate_area_tabs()->has_2_1_7() ) {
					// Check that the slug doesn't already exist
					$i = 1;
					while ( array_key_exists( $slug, affwp_get_affiliate_area_tabs() ) ) {
						// If slug exists, append "-1" to make the slug unique.
						$slug = $slug . '-' . $i++;
					}
				}

				// Set the slug for any custom tab.
				$affiliate_area_tabs[$key]['slug'] = $slug;
			}
			
		}

		// Get the current AffiliateWP settings
		$options = get_option( 'affwp_settings' );

		// Get the default AffiliateWP tabs. We need to merge these with any custom tabs.
		$default_tabs = affiliatewp_affiliate_area_tabs()->functions->default_tabs();

		// Create our new array in the needed format.
		$new_tabs = array();

		$i = 1;

		foreach ( $default_tabs as $slug => $title ) {
			$new_tabs[$i]['id']    = 0;
			$new_tabs[$i]['title'] = $title;
			$new_tabs[$i]['slug']  = $slug;
			$i++;
		}

		/**
		 * Prior to v1.2, tabs that were hidden were stored in the affiliate_area_hide_tabs array.
		 * This will add a "hide" key to the existing "affiliate_area_tabs" array and remove the now uneeded affiliate_area_hide_tabs array.
		 */
		$hide_tabs = affiliate_wp()->settings->get( 'affiliate_area_hide_tabs' );

		// Some tabs are currently hidden.
		if ( $hide_tabs ) {
		
			// Loop through our affiliate area tabs and match them to any tab hidden in the old array.
			foreach ( $new_tabs as $key => $tab_array ) {

				if ( isset( $tab_array['slug'] ) && array_key_exists( $tab_array['slug'], $hide_tabs ) ) {
					// We have a match. Set the "hide" key to "yes"
					$new_tabs[$key]['hide'] = 'yes';
				}

			}

		}
		
		// Remove the old hidden tabs array (prior to 1.2).
		unset( $options['affiliate_area_hide_tabs'] );

		if ( is_array( $new_tabs ) && is_array( $affiliate_area_tabs ) ) {
			// Merge
			$reindexed = array_merge( $new_tabs, $affiliate_area_tabs );

			// Reindex array so it starts from 1
			$options['affiliate_area_tabs'] = array_combine( range(1, count( $reindexed ) ), array_values( $reindexed ) );
		}
		
		// Update options array to include our new tabs
		update_option( 'affwp_settings', $options );

		// Upgraded!
		$this->upgraded = true;
		
    }
    
}
new Affiliate_Area_Tabs_Upgrades;