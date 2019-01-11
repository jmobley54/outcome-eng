		<div class="mainflex flexslider">
                    
        	<?php if(get_option('themnific_logo')) { ?>
            
                <div id="header_bottom" class="boxshadow">
                    
                    <div class="logo_bottom"> 
                                        
                        <a class="logo" href="<?php echo home_url(); ?>/">
                        
                            <img id="logo" src="<?php echo esc_url(get_option('themnific_logo'));?>" alt="<?php bloginfo('name'); ?>"/>
                                
                        </a>
                    
                    </div>	
                
                </div>
                                    
       		<?php } else {  } ?>
            
                <ul class="slides">
                
					<?php $loop = new WP_Query( array( 'post_type' => 'myslidertype', 'posts_per_page' => '99'  ) ); ?>
                    <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
                    <?php 
		
					$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
					$large_image = $large_image[0]; 
					$another_image_1 = get_post_meta($post->ID, 'themnific_image_1_url', true);
					$size = get_post_meta($post->ID, 'themnific_size', true);
					$slider_url = get_post_meta($post->ID, 'themnific_slider_url', true);
					$slider_content = get_post_meta($post->ID, 'themnific_slider_inside', true);
					$video_input = get_post_meta($post->ID, 'themnific_slider_video', true);
					?>   
                    
                        <li>
                        
                        	<?php 	
							if($video_input) {  echo ($video_input); 
                            } else {?>
                           
                           		<div class="slider_full">
                                
                                	<?php if($slider_url) { ?>
                                
                                    <a href="<?php echo $slider_url; ?>">
                                        
                                            <?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full' ); } ?>
                                    </a>
                                    
                                    <?php } else {?>
                                    
                                    		<?php if ( has_post_thumbnail() ) { the_post_thumbnail( 'full' ); } ?>
                                
                                	<?php }?>  
                                
                                </div>
                                
                                
                                <?php if($slider_content == 'Yes')  {?>
                                        
									<?php if (get_post_meta($post->ID, 'themnific_slider_url', true)) { ?>
                                    
                                        <h1><a href="<?php echo $slider_url; ?>"><?php echo short_title('...', 9); ?></a></h1>
                                        
                                    <?php } else { ?>
                                    
                                        <h1><?php echo short_title('...', 9); ?></h1>
                                        
                                    <?php } ?>
                            	
                                
                                    <div class="stuff"><span class="slidebg boxshadow"></span>
                                    
                                        <div class="flexhead">
                                    
                                            <?php the_content(); ?>
                                        
                                        </div>
                                        
                                    </div>
                                
                                <?php } else ?>
                        
                    	<?php }?>           
                            
                        <div style="clear: both;"></div>
                        </li>                      
                    <?php endwhile; ?>
                    
                </ul>
                
            </div>
    
        <?php wp_reset_query(); ?>