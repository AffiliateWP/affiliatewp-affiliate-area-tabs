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
     * Render the table
     * @since 1.0.0
     */
    public function tabs_table() {

        $tabs  = affiliatewp_affiliate_area_tabs()->get_tabs();
        $count = count( $tabs );

        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {

            // add new tab
            $('#affwp_new_tab').on('click', function(e) {

                e.preventDefault();

                var row = $('#affiliatewp-tabs tbody tr:last');
                console.log( row );

                var count = $('#affiliatewp-tabs tbody tr').length;

                console.log( count );

                // clone the row and its child's data and events
                clone = row.clone( true );

                // empty values
                clone.find( 'td input, td select' ).val( '' );

                clone.find( 'input, select' ).each(function() {
                    var name = $( this ).attr( 'name' );

                    name = name.replace( /\[(\d+)\]/, '[' + parseInt( count ) + ']');

                    $( this ).attr( 'name', name ).attr( 'id', name );
                });

                // insert new clone after existing row
                clone.insertAfter( row );

            });

            // remove tab
            $('.affwp_remove_tab').on('click', function(e) {
                e.preventDefault();

                var count = $('#affiliatewp-tabs tbody tr').length;

                // instead of removing the last row, clear out the values
                if ( count !== 1 ) {
                    $(this).parent().parent().remove();
                } else {
                    $(this).closest('tr').find( 'td input, td select' ).val( '' );
                    console.log( $(this) );
                }

            });

        });
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
                        <th style="width:15%;"><?php _e( 'Tab Page', 'affiliatewp-affiliate-area-tabs' ); ?></th>
                        <th><?php _e( 'Tab Title', 'affiliatewp-affiliate-area-tabs' ); ?></th>
                        <th style="width:5%;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ( $tabs ) : ?>

                        <?php foreach( $tabs as $key => $tab ) :

                            $pages = affwp_get_pages();

                            ?>
                            <tr>
                                <td>
                                    <select name="affwp_settings[affiliate_area_tabs][<?php echo $key; ?>][id]">
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
                                <select name="affwp_settings[affiliate_area_tabs][<?php echo $count; ?>][id]">
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
