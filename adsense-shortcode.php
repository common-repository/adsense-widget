<?php 

/* Adsense shortcode */
add_shortcode( 'adsense', 'adsense_shortcode' ); 
function adsense_shortcode( $atts, $content = null ){
  global $adsense_widget; 

	extract( shortcode_atts( array(
    'userid' => '',
    'pubid' => '',
    'slot' => '',
    'format' => '',
    'responsive' => 'on',
    'layout_key' => '',
    'align' => 'none',
    'sizes' => ''
	), $atts) );
	
	$settings = $adsense_widget->get_settings();

	if( '' == $format && array_key_exists( 'default_ad_format', $settings ) ){ 
    $format = $settings[ 'default_ad_format' ]; 
  }
  
  // if no format specified, fallback to display
  if( '' == $format ){ $format = "display"; }

  // get publisher id from userid or pubid
  $publisher_id = $userid;
  if( '' == $userid && '' != $pubid ){ $publisher_id = $pubid; }

  // get default slot from settings
  if( empty( $slot ) && array_key_exists( 'shortcode_slot', $settings ) ){
    $slot = $settings[ 'shortcode_slot' ];
  }

  // get default slot from settings
  if( empty( $sizes ) && array_key_exists( 'shortcode_sizes', $settings ) ){
    $sizes = $settings[ 'shortcode_sizes' ];
  }
	
	// Open adsense wrapper
	$adcode = "\n<div class=\"adsense-shortcode align".$align."\">";

	ob_start();
    $args = array( 
      'adsense_id' => $publisher_id,
      'slot' => $slot,
      'format' => $format, 
      'responsive' => $responsive,
      'layout_key' => $layout_key,
      'sizes' => $sizes,
      'is_shortcode' => true
    );
    the_widget( 'stf_adsense', $args );
    $widget_code = ob_get_contents();
	ob_end_clean();
	$adcode .= $widget_code;	
	
	// Close the layer
	$adcode .= "\n</div>\n";
  return $adcode;	
  
}