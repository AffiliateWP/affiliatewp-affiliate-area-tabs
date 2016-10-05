<?php
/**
 * Plugin Name: AffiliateWP - Affiliate Area Tabs
 * Plugin URI: https://affiliatewp.com/
 * Description: Add custom tabs to the Affiliate Area
 * Author: AffiliateWP
 * Author URI: https://affiliatewp.com
 * Version: 1.1.4
 * Text Domain: affiliatewp-affiliate-area-tabs
 * Domain Path: languages
 *
 * AffiliateWP is distributed under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * AffiliateWP is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with AffiliateWP. If not, see <http://www.gnu.org/licenses/>.
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'AffiliateWP_Affiliate_Area_Tabs' ) ) {

	final class AffiliateWP_Affiliate_Area_Tabs {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of AffiliateWP_Affiliate_Area_Tabs exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 * @since 1.0
		 */
		private static $instance;


		/**
		 * The version number of AffiliateWP
		 *
		 * @since 1.0
		 */
		private $version = '1.1.4';

		/**
		 * Main AffiliateWP_Affiliate_Area_Tabs Instance
		 *
		 * Insures that only one instance of AffiliateWP_Affiliate_Area_Tabs exists in memory at any one
		 * time. Also prevents needing to define globals all over the place.
		 *
		 * @since 1.0
		 * @static var array $instance
		 * @return The one true AffiliateWP_Affiliate_Area_Tabs
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof AffiliateWP_Affiliate_Area_Tabs ) ) {

				self::$instance = new AffiliateWP_Affiliate_Area_Tabs;
				self::$instance->setup_constants();
				self::$instance->load_textdomain();
				self::$instance->includes();
				self::$instance->hooks();

			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-area-tabs' ), '1.0' );
		}

		/**
		 * Disable unserializing of the class
		 *
		 * @since 1.0.0
		 * @access protected
		 * @return void
		 */
		public function __wakeup() {
			// Unserializing instances of the class is forbidden
			_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'affiliatewp-affiliate-area-tabs' ), '1.0' );
		}

		/**
		 * Constructor Function
		 *
		 * @since 1.0.0
		 * @access private
		 */
		private function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since 1.0.0
		 * @access public
		 * @static
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Setup plugin constants
		 *
		 * @access private
		 * @since 1.0.0
		 * @return void
		 */
		private function setup_constants() {
			// Plugin version
			if ( ! defined( 'AFFWP_AAT_VERSION' ) ) {
				define( 'AFFWP_AAT_VERSION', $this->version );
			}

			// Plugin Folder Path
			if ( ! defined( 'AFFWP_AAT_PLUGIN_DIR' ) ) {
				define( 'AFFWP_AAT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin Folder URL
			if ( ! defined( 'AFFWP_AAT_PLUGIN_URL' ) ) {
				define( 'AFFWP_AAT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin Root File
			if ( ! defined( 'AFFWP_AAT_PLUGIN_FILE' ) ) {
				define( 'AFFWP_AAT_PLUGIN_FILE', __FILE__ );
			}
		}

		/**
		 * Loads the plugin language files
		 *
		 * @access public
		 * @since 1.0.0
		 * @return void
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'affiliatewp_affiliate_area_tabs_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale',  get_locale(), 'affiliatewp-affiliate-area-tabs' );
			$mofile   = sprintf( '%1$s-%2$s.mo', 'affiliatewp-affiliate-area-tabs', $locale );

			// Setup paths to current locale file
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/affiliatewp-affiliate-area-tabs/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/affiliatewp-affiliate-area-tabs/ folder
				load_textdomain( 'affiliatewp-affiliate-area-tabs', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/affiliatewp-affiliate-area-tabs/languages/ folder
				load_textdomain( 'affiliatewp-affiliate-area-tabs', $mofile_local );
			} else {
				// Load the default language files
				load_plugin_textdomain( 'affiliatewp-affiliate-area-tabs', false, $lang_dir );
			}
		}

		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {

			if ( is_admin() ) {
				require_once AFFWP_AAT_PLUGIN_DIR . 'includes/class-admin.php';
			}
		}

		/**
		 * Setup the default hooks and actions
		 *
		 * @since 1.0.0
		 * @return void
		 */
		private function hooks() {

			// plugin meta
			add_filter( 'plugin_row_meta', array( $this, 'plugin_meta' ), null, 2 );

			// add new tab to affiliate area
			add_action( 'affwp_affiliate_dashboard_tabs', array( $this, 'add_tab' ), 10, 2 );

			// add the tab's content
			add_action( 'affwp_affiliate_dashboard_bottom', array( $this, 'tab_content' ) );

			// redirect if non-affiliate tries to access a tab's page
			add_action( 'template_redirect', array( $this, 'redirect' ) );

			if ( $this->has_1_8() ) {
				$object = $this;

				add_filter( 'affwp_affiliate_area_show_tab', array( $this, 'hide_existing_tabs' ), 10, 2 );

				add_filter( 'affwp_affiliate_area_tabs', array( $this, 'add_tab_slugs' ) );
			}

		}

		/**
		 * Hide existing tabs from the Affiliate Area
		 *
		 * @since 1.1
		 * @return boolean
		 */
		public function hide_existing_tabs( $show, $tab ) {

			$options = affiliate_wp()->settings->get( 'affiliate_area_hide_tabs' );

			if ( ! $options ) {
				return $show;
			}

			if ( array_key_exists( $tab, $options ) && $options[$tab] == true ) {
				$show = false;
			}

			return $show;

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
			return array_merge( $tabs, $this->get_tab_slugs() );
		}

		/**
		 * Determine if the user is on version 1.8 of AffiliateWP
		 *
		 * @since 1.1
		 * @return boolean
		 */
		public function has_1_8() {

			$return = true;

			if ( version_compare( AFFILIATEWP_VERSION, '1.8', '<' ) ) {
				$return = false;
			}

			return $return;
		}

		/**
		 * Prevent non-affiliates from accessing any page that is added as a tab
		 *
		 * @since 1.0.1
		 * @return array $page_ids
		 */
		public function protected_page_ids() {

			if ( ! $this->get_tabs() ) {
				return;
			}

			$page_ids = wp_list_pluck( $this->get_tabs(), 'id' );
			return $page_ids;
		}

		/**
		 * Redirect to affiliate login page if content is accessed
		 *
		 * @since 1.0.1
		 * @return void
		 */
		public function redirect() {

			if ( ! $this->protected_page_ids() ) {
				return;
			}

			$redirect = affiliate_wp()->settings->get( 'affiliates_page' ) ? get_permalink( affiliate_wp()->settings->get( 'affiliates_page' ) ) : site_url();
			$redirect = apply_filters( 'affiliatewp-affiliate-area-tabs', $redirect );

			if ( in_array( get_the_ID(), $this->protected_page_ids() ) && ( ! affwp_is_affiliate() ) ) {
				wp_redirect( $redirect );
				exit;
			}

		}

		/**
		 * Get tabs
		 *
		 * @since 1.0.0
		 */
		public function get_tabs() {

			$tabs = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );

			if ( ! empty( $tabs ) ) {
				$tabs = array_values( $tabs );
			}

			foreach( $tabs as $key => $tab ) {

				if( ! isset( $tab['id'] ) ) {
					$tabs[ $key ]['id'] = 0;
				}

				if( empty( $tab['title'] ) && ! empty( $tab['id'] ) ) {
					$tabs[ $key ]['title'] = get_the_title( $tab['id'] );
				}
			}

			return $tabs;
		}

		/**
		 * Retrieves tabs as a list of slugs.
		 *
		 * @since 1.1.1
		 * @access public
		 *
		 * @return array List of tab slugs.
		 */
		public function get_tab_slugs() {
			$tabs  = affiliate_wp()->settings->get( 'affiliate_area_tabs', array() );
			$slugs = array_map( array( $this, 'make_slug' ), wp_list_pluck( $tabs, 'title' ) );

			return $slugs;
		}

		/**
		 * Make slug
		 *
		 * @since 1.0.0
		 */
		public function make_slug( $title = '' ) {
			return rawurldecode( sanitize_title_with_dashes( $title ) );
		}

		/**
		 * Tab content
		 *
		 * @since 1.0.0
		 */
		public function tab_content( $affiliate_id ) {

			$tabs = $this->get_tabs();

			// Make sure the arrays are unique. If 2 tabs are identical then the content will not be loaded twice.
			$tabs = array_unique( $tabs, SORT_REGULAR );

			$tab_slugs = array_values( $this->get_tab_slugs() );

			if ( $tabs ) : ?>

				<?php foreach ( $tabs as $tab ) :

					$post        = get_post( $tab['id'] );
					$tab_slug    = $this->make_slug( $tab['title'] );
					$current_tab = isset( $_GET['tab'] ) && $_GET['tab'] ? $_GET['tab'] : '';

					/**
					 * Showing a tab which has the [affiliate_area] shortcode inside will cause a nesting fatal error
					 * Instead of erroring out, let's just show a blank tab
					 */
					if ( isset( $post->post_content ) && has_shortcode( $post->post_content, 'affiliate_area' ) ) {
						continue;
					}

					// current tab doesn't match slug
			        if ( $current_tab && $current_tab !== $tab_slug ) {
			            continue;
			        }

					/**
					 * If we're on the Affiliate Area page (without query string)
					 * and the current slug matches the first slug in the array, show the content
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
							<?php echo apply_filters( 'the_content', $post->post_content ); ?>
						</div>

					<?php else : ?>

						<?php

							// current tab doesn't match slug
							if ( $current_tab !== $tab_slug ) {
								continue;
							}

						?>
						<div id="affwp-affiliate-dashboard-tab-<?php echo $tab_slug; ?>" class="affwp-tab-content">
							<?php echo apply_filters( 'the_content', $post->post_content ); ?>
						</div>

					<?php endif; ?>

				<?php endforeach; ?>

			<?php endif; ?>

		<?php
		}

		/**
		 * Add tab
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function add_tab( $affiliate_id, $active_tab ) {

			$tabs = $this->get_tabs();

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
		 * Modify plugin metalinks
		 *
		 * @access      public
		 * @since       1.0.0
		 * @param       array $links The current links array
		 * @param       string $file A specific plugin table entry
		 * @return      array $links The modified links array
		 */
		public function plugin_meta( $links, $file ) {
		    if ( $file == plugin_basename( __FILE__ ) ) {
		        $plugins_link = array(
		            '<a title="' . __( 'Get more add-ons for AffiliateWP', 'affiliatewp-affiliate-area-tabs' ) . '" href="'. admin_url( 'admin.php?page=affiliate-wp-add-ons' ) . '">' . __( 'More add-ons', 'affiliatewp-affiliate-area-tabs' ) . '</a>'
		        );

		        $links = array_merge( $links, $plugins_link );
		    }

		    return $links;
		}
	}

	/**
	 * The main function responsible for returning the one true AffiliateWP_Affiliate_Area_Tabs
	 * Instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * Example: <?php $affiliatewp_affiliate_area_tabs = affiliatewp_affiliate_area_tabs(); ?>
	 *
	 * @since 1.0
	 * @return object The one true AffiliateWP_Affiliate_Area_Tabs Instance
	 */
	function affiliatewp_affiliate_area_tabs() {

		if ( ! class_exists( 'Affiliate_WP' ) ) {

			if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
				require_once 'includes/class-activation.php';
			}

			$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
			$activation = $activation->run();

		} elseif ( version_compare( PHP_VERSION, '5.3', '<' ) ) {

			if ( ! class_exists( 'AffiliateWP_Activation' ) ) {
				require_once 'includes/class-activation.php';
			}

			$activation = new AffiliateWP_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
			$activation = $activation->below_php_version();

		} else {
			return AffiliateWP_Affiliate_Area_Tabs::instance();
		}

	}
	add_action( 'plugins_loaded', 'affiliatewp_affiliate_area_tabs', 100 );

}
