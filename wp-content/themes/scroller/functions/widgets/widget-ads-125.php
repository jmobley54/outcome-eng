<?php
/*---------------------------------------------------------------------------------*/
/* Ads Widget */
/*---------------------------------------------------------------------------------*/

class AdWidget125_left extends WP_Widget {

	function AdWidget125_left() {
		$widget_ops = array('description' => 'Use this widget to add any type of Ad as a widget.' );
		parent::__construct(false, __('Themnific - Ads 125px 4x', 'themnific'),$widget_ops);      
	}

	function widget($args, $instance) {  
		$title = $instance['title'];
		$image = $instance['image'];
		$image2 = $instance['image2'];
		$href = $instance['href'];
		$href2 = $instance['href2'];
		
		$image3 = $instance['image3'];
		$image4 = $instance['image4'];
		$href3 = $instance['href3'];
		$href4 = $instance['href4'];


		if($title != '')
			echo '<h2 class="widget single">'. esc_html($title).'</h2>';

		?>
		<ul class="ad125">
			<li><a href="<?php echo esc_url($href); ?>"><img class="twinsbox" src="<?php echo esc_url($image); ?>" alt="" /></a></li>
			<li><a href="<?php echo esc_url($href2); ?>"><img class="twinsbox" src="<?php echo esc_url($image2); ?>" alt="" /></a></li>
            
			<li><a href="<?php echo esc_url($href3); ?>"><img class="twinsbox" src="<?php echo esc_url($image3); ?>" alt="" /></a></li>
			<li><a href="<?php echo esc_url($href4); ?>"><img class="twinsbox" src="<?php echo esc_url($image4); ?>" alt="" /></a></li>
		</ul>
		<?php
		
		
		

	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['title'] = strip_tags( $new_instance['title'] );
		
		$instance['image'] = strip_tags( $new_instance['image'] );
		$instance['image2'] = $new_instance['image2'];
		$instance['image3'] = strip_tags( $new_instance['image3'] );
		$instance['image4'] = $new_instance['image4'];
		
		
		$instance['href'] = $new_instance['href'];
		$instance['href2'] = $new_instance['href2'];
		$instance['href3'] = $new_instance['href3'];
		$instance['href4'] = $new_instance['href4'];
		

		return $instance;
	}

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 'title' => __('125px Ads', 'themnific'), 'image' => '', 'image2' => '','image3' => '', 'image4' => '', 'href' => '', 'href2' => '', 'href3' => '', 'href4' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>


		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']); ?>"/>
		</p>





		<p>
			<label for="<?php echo $this->get_field_id( 'image' ); ?>"><?php _e('Image URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'image' ); ?>" name="<?php echo $this->get_field_name( 'image' ); ?>" value="<?php echo esc_attr($instance['image']); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'href' ); ?>"><?php _e('Target URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'href' ); ?>" name="<?php echo $this->get_field_name( 'href' ); ?>" value="<?php echo esc_attr($instance['href']); ?>"/>
		</p>





		<p>
			<label for="<?php echo $this->get_field_id( 'image2' ); ?>"><?php _e('Image 2 URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'image2' ); ?>" name="<?php echo $this->get_field_name( 'image2' ); ?>" value="<?php echo esc_attr($instance['image2']); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'href2' ); ?>"><?php _e('Target 2 URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'href2' ); ?>" name="<?php echo $this->get_field_name( 'href2' ); ?>" value="<?php echo esc_attr($instance['href2']); ?>"/>
		</p>   
        
        
        
        
    
    
		<p>
			<label for="<?php echo $this->get_field_id( 'image3' ); ?>"><?php _e('Image 3 URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'image3' ); ?>" name="<?php echo $this->get_field_name( 'image3' ); ?>" value="<?php echo esc_attr($instance['image3']); ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'href3' ); ?>"><?php _e('Target 3 URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'href3' ); ?>" name="<?php echo $this->get_field_name( 'href3' ); ?>" value="<?php echo esc_attr($instance['href3']); ?>"/>
		</p>  
        
        
        
        
        
        
		<p>
			<label for="<?php echo $this->get_field_id( 'image4' ); ?>"><?php _e('Image 4 URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'image4' ); ?>" name="<?php echo $this->get_field_name( 'image4' ); ?>" value="<?php echo esc_attr($instance['image4']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'href4' ); ?>"><?php _e('Target 4 URL', 'themnific'); ?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'href4' ); ?>" name="<?php echo $this->get_field_name( 'href4' ); ?>" value="<?php echo esc_attr($instance['href4']); ?>" />
		</p>   
        
        
        <?php
	}
} 

register_widget('AdWidget125_left');
?>