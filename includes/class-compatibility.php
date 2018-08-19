<?php

class AffiliateWP_Affiliate_Area_Tabs_Compatibility {
    
    public function __construct() {
        // Add new tab to affiliate area.
        add_action( 'affwp_affiliate_dashboard_tabs', array( $this, 'add_tab' ), 10, 2 );
        
        // This added the custom slugs to the older affwp_affiliate_area_tabs array.
        add_filter( 'affwp_affiliate_area_tabs', array( $this, 'add_tab_slugs' ) );

        // Add the tab's content.
        add_action( 'affwp_affiliate_dashboard_bottom', array( $this, 'tab_content' ) );
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

    /**
     * Tab content.
     *
     * @since 1.0.0
     */
    public function tab_content( $affiliate_id ) {

        // Get all tabs.
        $tabs = affiliatewp_affiliate_area_tabs()->functions->get_all_tabs();			
        
        // Make sure the arrays are unique. If 2 tabs are identical then the content will not be loaded twice.
        $tabs = array_unique( $tabs, SORT_REGULAR );

        // Get tab slugs.
        $tab_slugs = affiliatewp_affiliate_area_tabs()->functions->get_custom_tab_slugs();

        if ( $tabs ) : ?>

            <?php foreach ( $tabs as $tab ) :

                $post        = get_post( $tab['id'] );
                $tab_slug    = isset( $tab['slug'] ) ? $tab['slug'] : '';
                $current_tab = isset( $_GET['tab'] ) && $_GET['tab'] ? $_GET['tab'] : '';

                /**
                 * Showing a tab which has the [affiliate_area] shortcode inside will cause a nesting fatal error
                 * Instead of erroring out, let's just show a blank tab.
                 */
                if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'affiliate_area' ) ) {
                    continue;
                }

                // Current tab doesn't match slug
                if ( $current_tab && $current_tab !== $tab_slug ) {
                    continue;
                }

                /**
                 * If we're on the Affiliate Area page (without query string)
                 * and the current slug matches the first slug in the array, show the content.
                 */
                if ( ( ! $current_tab ) && ( $tab_slugs[0] === $tab_slug ) ) :

                    /**
                     * If the active tab does not exist in the tab slugs array,
                     * then one of the other default tabs is active, skip
                     */
                    if ( ! in_array( affwp_get_active_affiliate_area_tab(), $tab_slugs ) ) {
                        continue;
                    }

                    ?>

                    <div id="affwp-affiliate-dashboard-tab-<?php echo $tab_slug; ?>" class="affwp-tab-content">
	                    <?php echo do_shortcode( wpautop( $post->post_content ) ); ?>
                    </div>

                <?php else : ?>

                    <?php

                        // current tab doesn't match slug.
                        if ( $current_tab !== $tab_slug ) {
                            continue;
                        }

                    ?>
                    <div id="affwp-affiliate-dashboard-tab-<?php echo $tab_slug; ?>" class="affwp-tab-content">
	                    <?php echo do_shortcode( wpautop( $post->post_content ) ); ?>
                    </div>

                <?php endif; ?>

            <?php endforeach; ?>

        <?php endif; ?>

    <?php
    }

}
new AffiliateWP_Affiliate_Area_Tabs_Compatibility;