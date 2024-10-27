<?php 
/*
Plugin Name: Adsense Widget
Plugin URI: https://wpassist.me/plugins/adsense-widget/
Description: Add Google Adsense ads to your site using the widget or shortcode. Supports up-to-date ad formats, responsive sizing, AMP ads and placeholders! Visit settings page to enter your Publisher Id. <a href="https://wpassist.me/donate">❤️ Donate</a>
Version: 2.2.3
Author: Metin Saylan
Author URI: https://metinsaylan.com/
Text Domain: adsense-widget
*/

if ( ! defined( 'ABSPATH' ) ) {
	die( 'Invalid request.' );
}

define( 'MS_AW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MS_AW_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

include_once( 'adsense-widget-core.php' );
include_once( 'adsense-widget-filters.php' );
include_once( 'adsense-widget.php' );
include_once( 'adsense-shortcode.php' );

/* Thin wrap for wpa_plugin */
function get_adsense_widget_option( $key, $default = '' ){
  global $adsense_widget;
  return $adsense_widget->get_setting( $key, $default );
}

/* Register widget */
add_action( 'widgets_init', 'register_adsense_widget' );
function register_adsense_widget(){ 
	register_widget( 'stf_adsense' );
}

/* Admin-only content */
if( is_admin() ){
	
	/* Append plugin links */
	add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'plugin_add_settings_link' );
	function plugin_add_settings_link( $links ) {
		$links[] = '<a href="options-general.php?page=adsense-widget">' . __( 'Settings', 'adsense-widget' ) . '</a>';
		$links[] = '<a href="https://wpassist.me/donate/" target="_blank">' . __( 'Donate', 'adsense-widget' ) .  '</a>';
		return $links;
	}

}

/* Plugin stylesheet */
add_action( 'wp_enqueue_scripts', 'adsense_widget_styles' );
function adsense_widget_styles(){
  if ( 'on' === get_adsense_widget_option( 'output_css', 'on' ) ) {
    if( !is_admin() ){
      wp_enqueue_style( 'adsense-widget', MS_AW_PLUGIN_URL . "adsense-widget.min.css", false, "1.0", "all");	
    }
  }
}




