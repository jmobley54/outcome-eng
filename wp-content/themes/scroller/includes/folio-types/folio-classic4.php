           <?php
				$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
				$large_image = $large_image[0]; 
				$another_image_1 = get_post_meta($post->ID, 'themnific_image_1_url', true);
				$video_input = get_post_meta($post->ID, 'themnific_video_url', true);
				$project_url = get_post_meta($post->ID, 'themnific_project_url', true);
				$project_description = get_post_meta($post->ID, 'themnific_project_description', true);
            ?>
            
            <div class="item_full item_height4">
        
                <div class="imgwrap">
                
                        <span class="cats3"><?php $terms_of_post = get_the_term_list( $post->ID, 'categories', '',' &bull; ', ' ', '' ); echo $terms_of_post; ?></span>
                        
                        <a href="<?php the_permalink(); ?>">
                                
                            <?php the_post_thumbnail('folio4',array('title' => "")); ?>
                        
                        </a>
                        
                </div>	
                
                <div style="clear:both"></div>
    
                <h3><a href="<?php the_permalink(); ?>"><?php echo short_title('...', 8); ?></a></h3>
                
                <p><?php echo themnific_excerpt( get_the_excerpt(), '100'); ?></p>
                
                <a class="hoverstuff-zoom" rel="prettyPhoto[gallery]" href="<?php if($video_input) echo $video_input; else echo $large_image; ?>"><i class="fa fa-arrows-alt"></i></a>
                <a class="hoverstuff-link" href="<?php the_permalink(); ?>"><i class="fa fa-sign-out"></i></a>
        
            </div>