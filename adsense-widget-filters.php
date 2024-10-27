<?php 

/* Auto-Ads Filter for Head */
add_action( 'wp_head', 'adsense_widget_auto_ads_filter' );
function adsense_widget_auto_ads_filter(){
  if ( 'on' === get_adsense_widget_option( 'auto_ads_enabled', 'off' ) ) {
    $publisher_id = get_adsense_widget_option( 'adsense_id', '' );
    if ( '' !== $publisher_id ) {
			if( adsense_widget_is_amp() ) {

echo "<script async custom-element=\"amp-ad\" src=\"https://cdn.ampproject.org/v0/amp-ad-0.1.js\"></script>";
echo "<script async custom-element=\"amp-auto-ads\" src=\"https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js\"></script>";

			} else {
?><!-- Adsense Widget Auto Ads -->
<script data-ad-client="ca-<?php echo $publisher_id; ?>" async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- / Adsense Widget Auto Ads --><?php 
			}
    } else {
      echo "<!-- Adsense Widget: Publisher ID Error -->";
    }
  }
}

/* Auto-Ads Filter for AMP */
add_action( 'wp_body_open', 'adsense_widget_amp_body_inserts' );
function adsense_widget_amp_body_inserts() {
	if( adsense_widget_is_amp() ) {
		if ( 'on' === get_adsense_widget_option( 'auto_ads_enabled', 'off' ) ) {
			$publisher_id = get_adsense_widget_option( 'adsense_id', '' );
			if ( '' !== $publisher_id ) {
				echo '<amp-auto-ads type="adsense" data-ad-client="ca-' . $publisher_id . '"></amp-auto-ads>';
			} else {
				echo "<!-- Adsense Widget: Publisher ID Error -->";
			} /* have publisher_id */
		} /* auto-ads enabled */
	} /* is_amp */
}

add_filter( 'the_content', 'adsense_widget_auto_insert_filter', 99, 1 );
function adsense_widget_auto_insert_filter( $content ){
  if( is_singular() ){
    
    global $adsense_widget;
    $adsense_settings = $adsense_widget->get_settings();

    $before_content = "";
    $after_content = "";
    
    if ( 'on' === get_adsense_widget_option( 'insert_before_post', 'off' ) ) {

      $format = get_adsense_widget_option( 'before_post_ad_format', 'display' );
      $sizes = get_adsense_widget_option( 'before_post_ad_sizes' );
			$slot = get_adsense_widget_option( 'before_post_slot_id' );
      
      ob_start();
      $args = array(
        'format' => $format,
        'sizes' => $sizes,
				'slot' => $slot,
        'is_shortcode' => true
      );
      the_widget( 'stf_adsense', $args );
      $before_content = "\n<!-- Adsense Widget Auto Insert -->" . ob_get_contents() . "\n<!-- /Adsense Widget Auto Insert -->\n";
      ob_end_clean();

    }
    
    if ( 'on' === get_adsense_widget_option( 'insert_after_post', 'off' ) ) {

      $format = get_adsense_widget_option( 'after_post_ad_format', 'display' );
      $sizes = get_adsense_widget_option( 'after_post_ad_sizes' );
			$slot = get_adsense_widget_option( 'after_post_slot_id' );
      
      ob_start();
      $args = array(
        'format' => $format,
        'sizes' => $sizes,
				'slot' => $slot,
        'is_shortcode' => true
      );
      the_widget( 'stf_adsense', $args );
      $after_content = "\n<!-- Adsense Widget Auto Insert -->" . ob_get_contents() . "\n<!-- /Adsense Widget Auto Insert -->\n";
      ob_end_clean();

    }

    return $before_content . ' ' . $content . ' ' . $after_content;
  }
  return $content;
}