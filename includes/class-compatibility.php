<?php

class AffiliateWP_Affiliate_Area_Tabs_Compatibility {
    
    public function __construct() {
		// Add new tab to affiliate area
        add_action( 'affwp_affiliate_dashboard_tabs', array( $this, 'add_tab' ), 10, 2 );
        
        // this added the custom slugs to the older affwp_affiliate_area_tabs array
        add_filter( 'affwp_affiliate_area_tabs', array( $this, 'add_tab_slugs' ) );
	}


    /**
     * Adds custom tab slugs.
     *
     * @access public
     * @since  1.1.4
     *
     * @param array $tabs Affiliate Area tabs.
     * @return array Filtered Affiliate Area tabs.
     */
    public function add_tab_slugs( $tabs ) {
        return array_merge( $tabs, affiliatewp_affiliate_area_tabs()->functions->get_custom_tab_slugs() );
    }

    /**
     * Add tab
     * For AffiliateWP versions less than 2.1.7
     *
     * @since 1.0.0
     * @return void
     */
    public function add_tab( $affiliate_id, $active_tab ) {
        
        $tabs = affiliatewp_affiliate_area_tabs()->functions->get_all_tabs();

        if ( $tabs ) : ?>

            <?php foreach ( $tabs as $tab ) :

                $tab_slug = rawurldecode( sanitize_title_with_dashes( $tab['title'] ) );
                $post = get_post( $tab['id'] );

                // a tab's content cannot be the content of the page you're currently viewing
                if ( get_the_ID() === $post->ID ) {
                    continue;
                }

                ?>
                <li class="affwp-affiliate-dashboard-tab<?php echo $active_tab == $tab_slug ? ' active' : ''; ?>">
                    <a href="<?php echo esc_url( add_query_arg( 'tab', $tab_slug ) ); ?>"><?php echo $tab['title']; ?></a>
                </li>
            <?php endforeach; ?>

        <?php endif; ?>

    <?php
    }


}
new AffiliateWP_Affiliate_Area_Tabs_Compatibility;