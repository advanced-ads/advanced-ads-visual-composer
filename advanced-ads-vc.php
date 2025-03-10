<?php
/**
 * Plugin Name:       Ads for WPBakery Page Builder (formerly Visual Composer)
 * Plugin URI:        https://wpadvancedads.com
 * Description:       Display Advanced Ads as a Visual Composer Element
 * Version:           2.0.0
 * Author:            Advanced Ads
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       ads-for-visual-composer
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * based on Extend WPBakery Page Builder Plugin (formerly Visual Composer)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

class Advanced_Ads_Visual_Composer {
	/**
	 * Advanced_Ads_Visual_Composer constructor.
	 */
	public function __construct() {
		// We safely integrate with VC with this hook.
		add_action( 'init', [ $this, 'check_dependencies' ] );
		add_action( 'init', [ $this, 'add_arguments' ], 30 );
		// load translations.
		add_action( 'plugins_loaded', [ $this, 'ads_for_visual_composer_load_plugin_textdomain' ] );
	}

	/**
	 * Check if Advanced Ads and WP Bakery Visual Composer are installed
	 */
	public function check_dependencies() {
		// Check if WPBakery Page Builder is installed.
		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			// Display notice that Visual Composer is required.
			add_action( 'admin_notices', [ $this, 'show_vc_version_notice' ] );
		}

		// Check if Advanced Ads is installed.
		if ( ! defined( 'ADVADS_VERSION' ) ) {
			// Display notice that Advanced Ads is required.
			add_action( 'admin_notices', [ $this, 'show_advads_version_notice' ] );
		}
	}

	/**
	 * Add WP Bakery options
	 */
	public function add_arguments() {

		if ( ! defined( 'ADVADS_PLUGIN_BASENAME' ) || ! defined( 'WPB_VC_VERSION' ) ) {
			return;
		}

		vc_map( [
				'name'        => __( 'Advanced Ads – Ad', 'ads-for-visual-composer' ),
				'description' => __( 'Displays an Ad', 'ads-for-visual-composer' ),
				'base'        => 'the_ad',
				'icon'        => plugins_url( 'assets/icon.png', __FILE__ ),
				'category'    => 'Ads',
				'group'       => 'Advanced Ads',

				'params' => [
					[
						'type'        => 'dropdown',
						'heading'     => __( 'Select an ad', 'ads-for-visual-composer' ),
						'param_name'  => 'id',
						'description' => __( 'Display an Ad', 'ads-for-visual-composer' ),
						'value'       => $this->get_ads(),
						'std'         => '',
						'admin_label' => true,
					],
				],
			]
		);

		vc_map( [
				'name'        => __( 'Advanced Ads – Group', 'ads-for-visual-composer' ),
				'description' => __( 'Displays an Ad Group', 'ads-for-visual-composer' ),
				'base'        => 'the_ad_group',
				'icon'        => plugins_url( 'assets/icon.png', __FILE__ ),
				'category'    => 'Ads',
				'group'       => 'Advanced Ads',

				"params" => [
					[
						'type'        => 'dropdown',
						'heading'     => __( 'Select a group', 'ads-for-visual-composer' ),
						'param_name'  => 'id',
						'description' => __( 'Displays an Ad Group', 'ads-for-visual-composer' ),
						'value'       => $this->get_groups(),
						'std'         => '',
						'admin_label' => true,
					],
				],
			]
		);

		vc_map( [
				'name'        => __( 'Advanced Ads – Placement', 'ads-for-visual-composer' ),
				'description' => __( 'Displays an Ad Placement', 'ads-for-visual-composer' ),
				'base'        => 'the_ad_placement',
				'icon'        => plugins_url( 'assets/icon.png', __FILE__ ),
				'category'    => 'Ads',
				'group'       => 'Advanced Ads',

				'params' => [
					[
						'type'        => 'dropdown',
						'heading'     => __( 'Select a placement', 'ads-for-visual-composer' ),
						'param_name'  => 'id',
						'description' => __( 'Displays an Ad Placement', 'ads-for-visual-composer' ),
						'value'       => $this->get_placements(),
						'std'         => '',
						'admin_label' => true,
					],
				],
			]
		);
	}

	/**
	 * Warn if WP Bakery Visual Composer plugin is missing
	 */
	public function show_vc_version_notice() {
		$plugin_data = get_plugin_data( __FILE__ );

		echo wp_kses_post(
			sprintf(
				'<div class="error"><p>%s</p></div>',
				sprintf(
				/* translators: %s is the name of this plugin. */
					__( '<strong>%s</strong> requires the <strong><a href="http://bit.ly/vcomposer" target="_blank">WPBakery Page Builder</a></strong> plugin to be installed and activated on your site.', 'ads-for-visual-composer' ),
					esc_html( $plugin_data['Name'] )
				)
			)
		);
	}

	/**
	 * Check if Advanced Ads 2.0 or newer is installed.
	 *
	 * @return bool|int
	 */
	private function is_a2_2() {
		return version_compare( ADVADS_VERSION, '2.0', '>=' );
	}

	/**
	 * Get all ads
	 *
	 * @return array
	 */
	private function get_ads() {
		global $wpdb;
		static $ads;

		if ( null === $ads ) {
			$ads = [ '' => '' ];

			if ( $this->is_a2_2() ) {
				foreach ( wp_advads_get_all_ads() as $id => $ad ) {
					$ads[ $ad->get_title() ] = $id;
				}
			} else {
				foreach ( ( new Advanced_Ads_Model( $wpdb ) )->get_ads() as $ad ) {
					$ads[ $ad->post_title ] = $ad->ID;
				}
			}
		}

		return $ads;
	}

	/**
	 * Get all groups
	 *
	 * @return array
	 */
	private function get_groups() {
		global $wpdb;
		static $groups;

		if ( null === $groups ) {
			$groups = [ '' => '' ];
			if ( $this->is_a2_2() ) {
				foreach ( wp_advads_get_all_groups() as $id => $group ) {
					$groups[ $group->get_title() ] = $id;
				}
			} else {
				foreach ( ( new Advanced_Ads_Model( $wpdb ) )->get_ad_groups() as $group ) {
					$groups[ $group->name ] = $group->term_id;
				}
			}
		}

		return $groups;
	}

	/**
	 * Get all placements
	 *
	 * @return array
	 */
	private function get_placements() {
		static $placements;

		if ( null === $placements ) {
			$placements = [ '' => '' ];
			if ( $this->is_a2_2() ) {
				foreach ( wp_advads_get_placements_by_types( 'default' ) as $id => $placement ) {
					$placements[ $placement->get_title() ] = $placement->get_slug();
				}
			} else {
				foreach ( get_option( 'advads-ads-placements', [] ) as $id => $placement ) {
					if ( 'default' !== $placement['type'] ) {
						continue;
					}
					$placements[ $placement['name'] ] = $id;
				};
			}
		}

		return $placements;
	}

	/**
	 * Warn if Advanced Ads is missing
	 */
	public function show_advads_version_notice() {
		$plugin_data = get_plugin_data( __FILE__ );
		$plugins     = get_plugins();

		if ( isset( $plugins['advanced-ads/advanced-ads.php'] ) ) { // is installed, but not active.
			$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">' . __( 'Activate Now', 'ads-for-visual-composer' ) . '</a>';
		} else {
			$link = '<a class="button button-primary" href="' . wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . 'advanced-ads' ), 'install-plugin_' . 'advanced-ads' ) . '">' . __( 'Install Now', 'ads-for-visual-composer' ) . '</a>';
		}

		echo wp_kses_post(
			sprintf(
				'<div class="error"><p>%1$s&nbsp;%2$s</p></div>',
				sprintf(
				/* translators: %s is the name of this plugin. */
					__( '<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com/#utm_source=advanced-ads&utm_medium=link&utm_campaign=activate-vc" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'ads-for-visual-composer' ),
					$plugin_data['Name']
				),
				$link
			)
		);
	}

	/**
	 * Load translations
	 *
	 * @return void
	 */
	public function ads_for_visual_composer_load_plugin_textdomain() {
		load_plugin_textdomain( 'ads-for-visual-composer', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

}

new Advanced_Ads_Visual_Composer();
