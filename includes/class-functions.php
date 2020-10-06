<?php

class AffiliateWP_Affiliate_Area_Tabs_Functions {

	/**
	 * Determine if the tab is a custom tab or not.
	 * A custom tab is one that has been added using the "Add New Tab" button.
	 * 
	 * @since 1.2
	 * @uses get_custom_tab_slugs()
	 * 
	 * @return boolean True if the tab is a custom tab, false otherwise.
	 */
	public function is_custom_tab( $tab_slug = '' ) {
		return in_array( $tab_slug, $this->get_custom_tab_slugs() );
	}
	
	/**
	 * Determine if a tab is a default tab or not.
	 * A default tab is one of the core AffiliateWP tabs.
	 * 
	 * @since 1.2
	 * @uses default_tabs()
	 * 
	 * @return boolean True if tab is a default tab, false otherwise.
	 */
	public function is_default_tab( $tab_slug ) {
		return array_key_exists( $tab_slug, $this->default_tabs() );
	}

	/**
	 * Holds an array of the default tabs added by AffiliateWP.
	 * 
	 * @since 1.2
	 * 
	 * @return array $default_tabs Array of default tabs.
	 */
	public function default_tabs() {

		$default_tabs = array(
			'urls'      => __( 'Affiliate URLs', 'affiliate-wp' ),
			'stats'     => __( 'Statistics', 'affiliate-wp' ),
			'graphs'    => __( 'Graphs', 'affiliate-wp' ),
			'referrals' => __( 'Referrals', 'affiliate-wp' ),
			'payouts'   => __( 'Payouts', 'affiliate-wp' ),
			'visits'    => __( 'Visits', 'affiliate-wp' ),
			'coupons'   => __( 'Coupons', 'affiliate-wp' ),
			'creatives' => __( 'Creatives', 'affiliate-wp' ),
			'settings'  => __( 'Settings', 'affiliate-wp' ),
		);

		return $default_tabs;

	}

	/**
	 * Returns an array of pages (minus the Affiliate Area).
	 * 
	 * @since 1.1.2
	 */
	public function get_pages() {
		
		$pages             = affwp_get_pages();
		$affiliate_area_id = function_exists( 'affwp_get_affiliate_area_page_id' ) ? affwp_get_affiliate_area_page_id() : affiliate_wp()->settings->get( 'affiliates_page' );

		if ( ! empty( $pages[ $affiliate_area_id ] ) ) {
			unset( $pages[ $affiliate_area_id ] );
		}

		return $pages;
	}

	/**
	 * Make slug
	 *
	 * @since 1.0.0
	 */
	public function make_slug( $title = '' ) {
		
		$slug = rawurldecode( sanitize_title_with_dashes( $title ) );

		return $slug;
	
	}

	/**
	 * Protected page IDs.
	 * These page IDs cannot be accessed by non-affiliates.
	 *
	 * @since 1.0.1
	 * @uses get_tabs()
	 * 
	 * @return array $page_ids
	 */
	public function protected_page_ids() {
		
		if ( ! $this->get_all_tabs() ) {
			return;
		}

		$page_ids = wp_list_pluck( $this->get_all_tabs(), 'id' );
		$page_ids = array_filter( $page_ids );

		return $page_ids;

	}

	/**
	 * Get tabs for the Affiliates -> Settings -> Affiliate Area Tabs page.
	 * 
	 * Example:
	 * 
	 * array(
	 *		'urls'      => 'Affiliate URLs',
	 *		'stats'     => 'Statistics',
	 *		'graphs'    => 'Graphs',
	 *		'referrals' => 'Referrals',
	 *		'payouts'   => 'Payouts',
	 *		'visits'    => 'Visits',
	 *		'coupons'   => 'Coupons',
	 *		'creatives' => 'Creatives',
	 *		'settings'  => 'Settings'
	 *	)
	 *
	 * @access public
	 * @since 1.1.6
	 * @since 1.2 Use affwp_get_affiliate_area_tabs (since Affiliate 2.1.7).
	 * @since 1.2.9 Add coupons to tabs retrieved from affwp_get_affiliate_area_tabs() if it doesn't exists.
	 * otherwise fallback
	 * 
	 * @return array $tabs The array of tabs to show
	 */
	public function get_tabs() {
		
		if ( function_exists( 'affwp_get_affiliate_area_tabs' ) ) {
			$tabs = affwp_get_affiliate_area_tabs();

			if ( affiliatewp_affiliate_area_tabs()->has_2_6() && ! array_key_exists( 'coupons', $tabs ) ) {
				$tabs['coupons'] = __( 'Coupons', 'affiliatewp-affiliate-area-tabs' );
			}

		} else {
			
			// Pre AffiliateWP v2.1.7.

			/**
			 * If a previous version of AffiliateWP (pre 2.1.7) is being used, 
			 * output the tabs from the database. This will include any custom tabs. 
			 */
			$saved_tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );

			if ( $saved_tabs ) {
				$tabs = array();

				foreach ( $saved_tabs as $tab ) {
					if ( isset( $tab['slug'] ) ) {
						$tabs[$tab['slug']] = $tab['title'];
					}
				}

			} else {
				// Tab settings have not been saved yet, use the default tab list.
				$tabs = affiliatewp_affiliate_area_tabs()->functions->default_tabs();
			}

		}

		return $tabs;

	}

	/**
	 * Get custom tab slugs
	 * 
	 * Example: array( 'custom-tab-one', 'custom-tab-two' );
	 * 
	 * @since 1.2
	 * 
	 * @return array $custom_tab_slugs Array of custom tab slugs
	 */
	public function get_custom_tab_slugs() {
		
		$tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );

		$custom_tab_slugs = array();

		if ( $tabs ) {
			foreach( $tabs as $tab_array ) {
				// Custom tabs have a page ID set.
				if ( ! empty( $tab_array['id'] ) && (int) $tab_array['id'] !== 0 && isset( $tab_array['slug'] ) ) {
					$custom_tab_slugs[] = $tab_array['slug'];
				}
			}
		}

		return $custom_tab_slugs;

	}

	/**
	 * Get all tabs.
	 * 
	 * Gets a multi-dimensional array of all tabs currently saved in the database.
	 * Each tab in the array contains its own array of:
	 * 
	 * id
	 * title
	 * slug
	 * hide (only set if tab is hidden)
	 * 
	 * @since 1.0.0
	 * 
	 * @return array $tabs All tabs stored in the DB 
	 */
	public function get_all_tabs() {
		
		$tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );

		if ( ! empty( $tabs ) ) {
			$tabs = array_values( $tabs );
		}

		foreach ( $tabs as $key => $tab ) {

			if ( ! isset( $tab['id'] ) ) {
				$tabs[ $key ]['id'] = 0;
			}

			if ( empty( $tab['title'] ) && ! empty( $tab['id'] ) ) {
				$tabs[ $key ]['title'] = get_the_title( $tab['id'] );
			}
		}

		return $tabs;

	}

	/**
	 * Get all custom tabs.
	 * 
	 * Gets a multi-dimensional array of all custom tabs currently saved in the database.
	 * Each custom tab in the array contains its own array of:
	 * 
	 * id
	 * title
	 * slug
	 * hide (only set if tab is hidden)
	 * 
	 * @since 1.2.2
	 * 
	 * @return array $custom_tabs All custom tabs stored in the DB 
	 */
	public function get_all_custom_tabs() {

		$tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );

		foreach ( $tabs as $key => $tab ) {

			if ( 0 === $tab['id'] ) {
				unset( $tabs[$key]);
			}
		}

		return $tabs;

	}

	/**
	 * Get a custom tab's content
	 * 
	 * @since 1.2.2
	 * @param string $tab_slug The tab slug to retrieve the content for.
	 * 
	 * @return string $tab_content The tab's post content.
	*/
	public function get_custom_tab_content( $tab_slug = '' ) {

		// Return if no tab has been specified.
		if ( ! $tab_slug ) {
			return;
		}

		$tab_content = '';

		foreach ( $this->get_all_custom_tabs() as $custom_tab ) {

			// Find the custom tab within the array.
			if ( $tab_slug === $custom_tab['slug'] ) {
				$post        = get_post( $custom_tab['id'] );
				$tab_content = $post->post_content;
				break;
			}

		}

		if ( has_shortcode( $tab_content, 'affiliate_area_graphs' ) ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-ui-css' );
		}

		if ( affiliatewp_affiliate_area_tabs()->has_blocks( $tab_content ) ) {
			return apply_filters( 'the_content', do_blocks( $tab_content ) );
		} else {
			return do_shortcode( wpautop( $tab_content ) );
		}
	}

	/**
	 * Holds an array of tabs added by add-ons.
	 * Currently used to store a notice for each tab. In the future it could
	 * hold other information/settings related to these tabs.
	 * 
	 * @since 1.2
	 * 
	 * @return array $tabs The tabs added from other add-ons
	 */
	public function add_on_tabs() {

		$tabs = apply_filters( 'affwp_aat_add_on_tabs', 
			array(
				'direct-links' => array(
					'notice' => sprintf( __( 'This tab has been added from the %s add-on. Only affiliates with direct link tracking enabled will see this tab in the Affiliate Area.', 'affiliatewp-affiliate-area-tabs' ), '<em>Direct Link Tracking</em>' ),
					'title'  => __( 'Direct Links', 'affiliatewp-direct-link-tracking' )
				),
				'order-details' => array(
					'notice' => sprintf( __( 'This tab has been added from the %s add-on. Only affiliates with access to order details will see this tab in the Affiliate Area.', 'affiliatewp-affiliate-area-tabs' ), '<em>Order Details For Affiliates</em>' ),
					'title'  => __( 'Order Details', 'affiliatewp-order-details-for-affiliates' )
				),
				'coupons' => array(
					'notice' => sprintf( __( 'This tab has been added from the %s add-on. Only affiliates with coupons assigned will see this tab in the Affiliate Area.', 'affiliatewp-affiliate-area-tabs' ), '<em>Show Affiliate Coupons</em>' ),
					'title'  => __( 'Coupons', 'affiliatewp-show-affiliate-coupons' )
				),
				'lifetime-customers' => array(
					'notice' => sprintf( __( 'This tab has been added from the %s add-on. Only affiliates with access to their lifetime customers will see this tab in the Affiliate Area.', 'affiliatewp-affiliate-area-tabs' ), '<em>Lifetime Commissions</em>' ),
					'title'  => __( 'Lifetime Customers', 'affiliate-wp-lifetime-commissions' ),
				)
			)
		);

		// Remove the coupons tab as an add-on tab in AffiliateWP 2.6 and newer.
		if ( affiliatewp_affiliate_area_tabs()->has_2_6() && array_key_exists( 'coupons', $tabs ) ) {
			unset( $tabs['coupons'] );
		}

		return $tabs;

	}

}