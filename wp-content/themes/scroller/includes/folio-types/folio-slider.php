           <?php
				$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
				$large_image = $large_image[0]; 
				$another_image_1 = get_post_meta($post->ID, 'themnific_image_1_url', true);
				$video_input = get_post_meta($post->ID, 'themnific_video_url', true);
				$project_url = get_post_meta($post->ID, 'themnific_project_url', true);
				$project_description = get_post_meta($post->ID, 'themnific_project_description', true);
            ?>
            
            <div class="item_slider body3">
                        
                <a href="<?php the_permalink(); ?>">
                        
                    <?php the_post_thumbnail('block_2',array('title' => "")); ?>
                
                </a>
                
                <div class="slider_inn">
                
                    <h2><a href="<?php the_permalink(); ?>"><?php echo short_title('...', 8); ?></a></h2>
        
                    <p class="meta"><?php $terms_of_post = get_the_term_list( $post->ID, 'categories', '',' &bull; ', ' ', '' ); echo $terms_of_post; ?></p>
                    
                    <p><?php echo themnific_excerpt( get_the_excerpt(), '170'); ?></p>
                  
                    <a class="hoverstuff-link" href="<?php the_permalink(); ?>"><i class="fa fa-sign-out"></i></a>
        		
                </div>
                
            </div>