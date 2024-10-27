<?php 

class stf_adsense extends WP_Widget {
	public function __construct() {	
    global $adsense_widget_ad_types, $adsense_formats;

		$widget_ops = array(
			'classname' => 'stf-adsense',
			'description' => __( 'Display Adsense Ads.', 'adsense-widget' ) 
		);

		parent::__construct(
			'adsense-widget',
			__( 'Adsense', 'adsense-widget' ),
			$widget_ops
		);

    // Default widget options
    $this->widget_defaults = array(
      'title' => '',
      'adsense_id' => '',
      'slot' => '',  
      'format' => 'display',
      'responsive' => 'on',
      'layout_key' => '',
      'sizes' => '',
      'is_shortcode' => false
    );
      
    $this->ad_formats = $adsense_formats; 
    
  }

  function widget( $args, $instance ) {
    global $adsense_widget;
  
    // extract widget options
		extract( $args );
		$widget_options = wp_parse_args( $instance, $this->widget_defaults );
    extract( $widget_options, EXTR_SKIP );

    $nl = "\n";
    
    $responsive = (bool)('on' == $responsive);
    
    // load plugin settings
    $settings = $adsense_widget->get_settings();
    $user_cap = current_user_can( $settings[ 'hide_ads_for' ] );
    
    $publisher_id = $adsense_id;
    // load publisher id from settings if possible
    if( empty( $publisher_id ) 
      && array_key_exists( 'adsense_id', $settings )
      && $settings[ 'adsense_id' ] != '' ){
      $publisher_id = $settings[ 'adsense_id' ] ;
    }
		
		// ad slot
		if( empty( $slot ) && array_key_exists( 'shortcode_slot', $settings ) ){
			$slot = $settings['shortcode_slot'];
		}
    
    if( empty( $publisher_id ) && current_user_can( 'administrator' ) ) { 
			echo '<div class="adsense-warning">Publisher Id is blank. Please add Publisher Id in <a href="https://wpassist.me/plugins/adsense-widget/">Adsense Widget</a> settings page.</div>';
		} else {

        if( !$is_shortcode ){
          echo $before_widget;
          if ( !empty( $instance[ 'title' ] ) ) {
            echo $before_title . apply_filters( 'widget_title', $instance[ 'title' ] ) . $after_title;
          } /* title check */
        }

        // wrapper
        $ad_class = 'format-' . $format;
        if( $responsive ){ $ad_class .= ' responsive'; }
        if( 'on' == $settings[ 'show_placeholders' ] && $user_cap ){
          $ad_class .= ' placeholder';
        }
				
				// remove sizing for in_article format
				if( 'in_article' === $format ){
					$sizes = '';
				}

        echo "\n<div class=\"adsense-wrapper\">";

        if( 'on' == $settings[ 'show_placeholders' ] && $user_cap ){

          // placeholder
          echo '<div class="adsense ' . $ad_class . ' ' . $sizes . '" >';
          echo '<table><tr><td class="placeholder-text"><small>';
          echo __( "Adsense", 'adsense-widget' );
          echo '</small><br />' . $this->ad_formats[ $format ] . '';
          echo '</td></tr></table>';
          echo '</div>';

        } else { 

					if ( adsense_widget_is_amp() ) {

						echo $nl . "<amp-ad width=\"100vw\" height=320 ";
						echo $nl . "     type=\"adsense\"";
						echo $nl . "     data-ad-client=\"ca-" . $publisher_id . "\"";
						echo $nl . "     data-ad-slot=\"" . $slot . "\"";
						echo $nl . "     data-auto-format=\"rspv\"";
						echo $nl . "     data-full-width>";
						echo $nl . "  <div overflow></div>";
						echo $nl . "</amp-ad>";

					} else {

						// adsbygoogle script
						echo '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';

						echo '<ins class="adsbygoogle adsense ' . $ad_class . ' ' . $sizes . '"';
						echo $nl . ' data-ad-client="ca-' . $publisher_id . '" ';

						// ad slot
						if( !empty( $slot ) && $slot != '' ){
							echo $nl . ' data-ad-slot="' . $slot . '" ';
						}

						// data-ad-format="auto"
						switch( $format ) {
							case 'display':
								echo $nl . ' data-ad-format="auto" ';
								break;
							case 'in_feed':
								echo $nl . ' data-ad-format="fluid"';
								if( '' != $layout_key ){ 
									echo $nl . ' data-ad-layout-key=" ' . $layout_key . ' "';
								}
								break;
							case 'in_article':
								echo $nl . ' data-ad-layout="in-article" ';
								echo $nl . ' data-ad-format="fluid"';
								break;
							case 'matched_content':
								echo $nl . ' data-ad-format="autorelaxed"';
								break;
							case 'link':
								echo $nl . ' data-ad-format="link"';
						}

						if( $responsive && in_array( $format, array( 'display', 'link' ) ) ){
							echo $nl . ' data-full-width-responsive="true" ';
						}

						// Display Ads & Link Ads
						
						echo '></ins>';

						// push script
						echo "\n<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>";
						
					} /* is_amp_endpoint check */

        } /* placeholder check */

        echo "\n</div>";

        if( !$is_shortcode ){
          echo $after_widget; 
        }

    } /* publisher id check */
	} 

    function update( $new_instance, $old_instance ) {
      $instance = $old_instance;

      $instance['title'] = isset( $new_instance['title'] ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

      $instance['adsense_id'] = isset( $new_instance['adsense_id'] ) ? wp_strip_all_tags( $new_instance['adsense_id'] ) : '';

      $instance['format'] = isset( $new_instance['format'] ) ?  $new_instance['format'] : '';

      $instance['responsive'] = isset( $new_instance['responsive'] ) ? 'on' : 'off';

      $instance['slot'] = isset( $new_instance['slot'] ) ? wp_strip_all_tags( $new_instance['slot'] ) : '';

      $instance['layout_key'] = isset( $new_instance['layout_key'] ) ? wp_strip_all_tags( $new_instance['layout_key'] ) : '';

      $instance['sizes'] = isset( $new_instance['sizes'] ) ? wp_strip_all_tags( $new_instance['sizes'] ) : '';

      return $instance;
    }

    function form( $instance ) {
      global $adsense_widget;
		
      $widget_options = wp_parse_args( $instance, $this->widget_defaults );
      extract( $widget_options, EXTR_SKIP );
      
      if ( !empty( $instance['title'] ) ) { 
        $title = esc_attr($instance['title']);
      }
        
      if ( !empty( $instance['type'] ) ) { 
        $type = $instance['type'];
      } else {
        $type = null;
      }
      
      if( empty( $adsense_id ) ) {
        $adsense_id = $adsense_widget->get_setting( 'adsense_id' );
      }

		?>
		
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title :', 'adsense-widget'); ?> <small><a href="https://wpassist.me/plugins/adsense-widget/help/#title" target="_blank" rel="external">(?)</a></small> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label><br />

    <p><label for="<?php echo $this->get_field_id('adsense_id'); ?>"><?php _e('Publisher Id :', 'adsense-widget'); ?> <small><a href="https://wpassist.me/plugins/adsense-widget/help/#adsense-id" target="_blank" rel="external">(?)</a></small> <input class="widefat" id="<?php echo $this->get_field_id('adsense_id'); ?>" name="<?php echo $this->get_field_name('adsense_id'); ?>" type="text" value="<?php echo $adsense_id; ?>" /></label><br />

    <p><label for="<?php echo $this->get_field_id('format'); ?>"><?php _e('Ad format:', 'adsense-widget'); ?> <small><a href="https://wpassist.me/plugins/adsense-widget/help/#ad-format" target="_blank" rel="external">(?)</a></small> <select name="<?php echo $this->get_field_name('format'); ?>" id="<?php echo $this->get_field_id('format'); ?>" ><?php 
    
      foreach ( $this->ad_formats as $key=>$label ) {
        echo '<option value="' . $key . '" ' . ( $key == $format ? ' selected="selected"' : '' ) . '>' . $label . '</option>\n';
      }
      
		?></select></label><br /> 
    <small></small></p>

    <p><label for="<?php echo $this->get_field_id('responsive'); ?>"><input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id('responsive'); ?>" name="<?php echo $this->get_field_name('responsive'); ?>" value="on" <?php checked( 'on', $responsive ) ?> /> Responsive</label></p>
		
		<p><label for="<?php echo $this->get_field_id('slot'); ?>"><?php _e('Slot ID (Optional):', 'adsense-widget'); ?> <small><a href="https://wpassist.me/plugins/adsense-widget/help/#slot-id" target="_blank" rel="external">(?)</a></small> <input class="widefat" id="<?php echo $this->get_field_id('slot'); ?>" name="<?php echo $this->get_field_name('slot'); ?>" type="text" value="<?php echo $slot; ?>" /></label><br />
    <small>Slot id for the ad you created.( Eg. 1234567890 )</small><br />
    
    <p><label for="<?php echo $this->get_field_id('sizes'); ?>"><?php _e('Sizing:', 'adsense-widget'); ?> <small><a href="https://wpassist.me/plugins/adsense-widget/sizing-options/" target="_blank" rel="external">(?)</a></small> <input class="widefat" id="<?php echo $this->get_field_id('sizes'); ?>" name="<?php echo $this->get_field_name('sizes'); ?>" type="text" value="<?php echo $sizes; ?>" /></label><br />
    <small>Sizing CSS classes.</small><br />
    
    <p><label for="<?php echo $this->get_field_id('layout_key'); ?>"><?php _e('Layout Key (In Feed Ads):', 'adsense-widget'); ?> <small><a href="https://wpassist.me/plugins/adsense-widget/help/#layout-key" target="_blank" rel="external">(?)</a></small> <input class="widefat" id="<?php echo $this->get_field_id('layout_key'); ?>" name="<?php echo $this->get_field_name('layout_key'); ?>" type="text" value="<?php echo $layout_key; ?>" /></label><br />
		<small>Layout key is required for In-Feed ads.</small><br /> 

    <p><a href="options-general.php?page=adsense-widget" target="_blank">Adsense Widget Settings &gt;</a></p>
		<?php
    }

} // class stf_adsense 


// wrapper function for checking amp endpoint
function adsense_widget_is_amp(){
	return ( function_exists( 'ampforwp_is_amp_endpoint' ) && ampforwp_is_amp_endpoint() ) ||
		( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) ||
		( function_exists( 'is_amp' ) && is_amp() );
}
