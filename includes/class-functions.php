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
	 * Returns an array of pages (minus the the Affiliate Area).
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
	 *		'creatives' => 'Creatives',
	 *		'settings'  => 'Settings'
	 *	)
	 *
	 * @access public
	 * @since 1.1.6
     * @since 1.2 Use affwp_get_affiliate_area_tabs (since Affiliate 2.1.7), 
     * otherwise fallback
     * 
	 * @return array $tabs The array of tabs to show
	 */
	public function get_tabs() {
        
        if ( function_exists( 'affwp_get_affiliate_area_tabs' ) ) {
            $tabs = affwp_get_affiliate_area_tabs();
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
                if ( $tab_array['id'] !== 0 && isset( $tab_array['slug'] ) ) {
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

}