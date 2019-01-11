<li <?php post_class(); ?>>
<?php echo tmnf_ribbon() ?>
   
		<?php
        $large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
        $large_image = $large_image[0]; 
        $another_image_1 = get_post_meta($post->ID, 'themnific_image_1_url', true);
        ?>


		<div class="imageformat">
        
            <a rel="prettyPhoto[gallery]"  href="<?php echo $large_image; ?>">  
                     <?php the_post_thumbnail('format-image'); ?>
            </a>
           
        </div>
    
   		<div style="clear: both;"></div> 
        
        <h2 class="singletitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

            <div class="hrline"><span></span></div> 
            
            <p class="meta">
            
                <i class="fa fa-clock-o"></i> <span><?php _e('On','themnific');?></span>  <?php the_time(get_option('date_format')); ?> | 
                <i class="fa fa-file-o"></i> <span> <?php the_category(', ') ?> | 
                <i class="fa fa-pencil-square-o"></i> <span><?php _e('By','themnific');?></span> <?php the_author_posts_link(); ?>
            
            </p>
            
</li>