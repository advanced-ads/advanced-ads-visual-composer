<?php
/* 
 * Plugin Name:       Ads for WPBakery Page Builder (formerly Visual Composer)
 * Plugin URI:        https://wpadvancedads.com
 * Description:       Display Advanced Ads as a Visual Composer Element
 * Version:           1.0.3
 * Author:            Thomas Maier, Hans-Lukas Herse
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       ads-for-visual-composer
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * 
 * based on Extend Visual Composer Plugin Example by WPBakery
*/

if (!defined('ABSPATH')) {
    die('-1');
}

class Advanced_Ads_Visual_Composer
{
    function __construct()
    {
        // We safely integrate with VC with this hook
        add_action('init', array($this, 'check_dependencies'));
        // add_action('vc_before_init', array($this, 'add_arguments'));
        add_action('init', array($this, 'add_arguments'));
		// load translations		
		add_action('plugins_loaded', array($this, 'ads_for_visual_composer_load_plugin_textdomain'));
    }
   
    
    public function check_dependencies()
    {
        // Check if Visual Composer is installed
        if (!defined('WPB_VC_VERSION')) {
            // Display notice that Visual Compser is required
            add_action('admin_notices', array($this, 'show_vc_version_notice'));
        }

        // Check if Advanced Ads is installed
        if (!defined('ADVADS_VERSION')) {
            // Display notice that Advanced Ads is required
            add_action('admin_notices', array($this, 'show_advads_version_notice'));
        }
    }
    
    public function add_arguments(){
	
	if (!defined('ADVADS_BASE') || !defined('WPB_VC_VERSION')) {
	    return;
	}
	
        vc_map( array(
		"name" => __("Advanced Ads – Ad", 'ads-for-visual-composer'),
		"description" => __("Displays an Ad", 'ads-for-visual-composer'),
		"base" => "the_ad",
		"icon" => plugins_url('assets/icon.png', __FILE__),
		"category" => 'Ads',
		"group" => 'Advanced Ads',

		"params" => array(
		    array(
			"type" => "textfield",
			"heading" => __("Ad Id", 'ads-for-visual-composer'),
			"param_name" => "id",
			"description" => __("Enter the ID of the ad.", 'ads-for-visual-composer'),
		    )
		)
	    )
	);
	vc_map( array(
		"name" => __("Advanced Ads – Group", 'ads-for-visual-composer'),
		"description" => __("Displays an Ad Group", 'ads-for-visual-composer'),
		"base" => "the_ad_group",
		"icon" => plugins_url('assets/icon.png', __FILE__),
		"category" => 'Ads',
		"group" => 'Advanced Ads',

		"params" => array(
		    array(
			"type" => "textfield",
			"holder" => "div",
			"heading" => __("Ad Group Id", 'ads-for-visual-composer'),
			"param_name" => "id",
			"description" => __("Enter the ad group ID.", 'ads-for-visual-composer')                )
		)
	    )
	);
	vc_map( array(
		"name" => __("Advanced Ads – Placement", 'ads-for-visual-composer'),
		"description" => __("Displays an Ad Placement", 'ads-for-visual-composer'),
		"base" => "the_ad_placement",
		"icon" => plugins_url('assets/icon.png', __FILE__),
		"category" => 'Ads',
		"group" => 'Advanced Ads',

		"params" => array(
		    array(
			"type" => "textfield",
			"holder" => "div",
			"heading" => __("Placement slug", 'ads-for-visual-composer'),
			"param_name" => "id",
			"description" => __("Enter the slug from a Manual Placement.", 'ads-for-visual-composer')                
		    )
		)
	    )
	);
    }
    
    public function show_vc_version_notice() {
        $plugin_data = get_plugin_data(__FILE__);
        echo '
        <div class="error">
          <p>'.sprintf(__('<strong>%s</strong> requires the <strong><a href="http://bit.ly/vcomposer" target="_blank">Visual Composer</a></strong> plugin to be installed and activated on your site.', 'ads-for-visual-composer'), $plugin_data['Name']).'</p>
        </div>';
    }

    public function show_advads_version_notice() {
        $plugin_data = get_plugin_data(__FILE__);
	$plugins = get_plugins();
	if( isset( $plugins['advanced-ads/advanced-ads.php'] ) ){ // is installed, but not active
	    $link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">'. __('Activate Now', 'ads-for-visual-composer') .'</a>';
	} else {
	    $link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads') . '">'. __('Install Now', 'ads-for-visual-composer') .'</a>';
	}
        echo '
        <div class="error">
          <p>'.sprintf(__('<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'ads-for-visual-composer'), $plugin_data['Name']) .
	     '&nbsp;' . $link . '</p></div>';
    }

	function ads_for_visual_composer_load_plugin_textdomain() {
    	load_plugin_textdomain( 'ads-for-visual-composer', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

}
new Advanced_Ads_Visual_Composer();