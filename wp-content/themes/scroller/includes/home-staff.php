<div id="staff-wrap">
	<ul class="warpbox">
    
		<?php $loop = new WP_Query( array( 'post_type' => 'staff','posts_per_page' => 50) ); ?>
        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <?php 
			$staff_position = get_post_meta($post->ID, 'themnific_staff_position', true);
			$staff_facebook = get_post_meta($post->ID, 'themnific_staff_facebook', true);
			$staff_twitter = get_post_meta($post->ID, 'themnific_staff_twitter', true);
			$staff_google = get_post_meta($post->ID, 'themnific_staff_google', true);
			$staff_pinterest = get_post_meta($post->ID, 'themnific_staff_pinterest', true);
			$staff_instagram = get_post_meta($post->ID, 'themnific_staff_instagram', true);
			$staff_flickr = get_post_meta($post->ID, 'themnific_staff_flickr', true);
			$staff_dribbble = get_post_meta($post->ID, 'themnific_staff_dribbble', true);
			$staff_behance = get_post_meta($post->ID, 'themnific_staff_behance', true);
			$staff_linkedin = get_post_meta($post->ID, 'themnific_staff_linkedin', true);
			$staff_vk = get_post_meta($post->ID, 'themnific_staff_vk', true);
			$staff_website = get_post_meta($post->ID, 'themnific_staff_website', true);
			$staff_more = get_post_meta($post->ID, 'themnific_staff_more', true);
		?>
        
            <li class="staff">
            
            	<?php if($staff_more) {?>
                	
                    <a href="<?php echo esc_url($staff_more); ?>">
                
        				<?php the_post_thumbnail('staff'); ?>
                
                    </a>
                    
                    <h3><a href="<?php echo esc_url($staff_more); ?>"><?php the_title(  ); ?></a></h3>   
					
				<?php } else { the_post_thumbnail('staff'); ?> <h3><?php the_title(  ); ?></h3>  <?php }?>
                
                
                <?php if($staff_position) {?>
                        <p class="meta"><?php echo ($staff_position); ?></p>
                <?php } else {}?>
                       
                <?php the_content(); ?>
                
                <ul class="staff_social">
                
                	<?php if($staff_facebook) {?><li><a class="rad" href="<?php echo esc_url($staff_facebook); ?>"><i class="fa fa-facebook-official"></i></a></li><?php } else {}?>
                	<?php if($staff_twitter) {?><li><a class="rad" href="<?php echo esc_url($staff_twitter); ?>"><i class="fa fa-twitter"></i></a></li><?php } else {}?>
                	<?php if($staff_google) {?><li><a class="rad" href="<?php echo esc_url($staff_google); ?>"><i class="fa fa-google-plus"></i></a></li><?php } else {}?>
                	<?php if($staff_pinterest) {?><li><a class="rad" href="<?php echo esc_url($staff_pinterest); ?>"><i class="fa fa-pinterest-square"></i></a></li><?php } else {}?>
                	<?php if($staff_linkedin) {?><li><a class="rad" href="<?php echo esc_url($staff_linkedin); ?>"><i class="fa fa-linkedin"></i></a></li><?php } else {}?>

					<?php if($staff_instagram) {?><li><a class="rad" href="<?php echo esc_url($staff_instagram); ?>"><i class="fa fa-instagram"></i></a></li><?php } else {}?>
                    <?php if($staff_flickr) {?><li><a class="rad" href="<?php echo esc_url($staff_flickr); ?>"><i class="fa fa-flickr"></i></a></li><?php } else {}?>
                    <?php if($staff_dribbble) {?><li><a class="rad" href="<?php echo esc_url($staff_dribbble); ?>"><i class="fa fa-dribbble"></i></a></li><?php } else {}?>
                    <?php if($staff_behance) {?><li><a class="rad" href="<?php echo esc_url($staff_behance); ?>"><i class="fa fa-behance"></i></a></li><?php } else {}?>
                    <?php if($staff_vk) {?><li><a class="rad" href="<?php echo esc_url($staff_vk); ?>"><i class="fa fa-vk"></i></a></li><?php } else {}?>
                	<?php if($staff_website) {?><li><a class="rad" href="<?php echo esc_url($staff_website); ?>"><i class="fa fa-sign-out"></i></a></li><?php } else {}?>
                
                </ul>
                
            </li>
        
        <?php endwhile; ?>
    
    </ul>
</div> 
<div style="clear: both;"></div>	