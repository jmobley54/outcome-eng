<li <?php post_class(); ?>>
            
	<h2 class="singletitle"><a href="<?php echo get_post_meta($post->ID, 'tmnf_linkss', true); ?>"><?php echo tmnf_icon() ?> <?php _e('[Link]','themnific');?> <?php the_title(); ?></a></h2>
    
            <div class="hrline"><span></span></div>  
            
            <p class="meta">
            
                <i class="fa fa-clock-o"></i> <span><?php _e('On','themnific');?></span>  <?php the_time(get_option('date_format')); ?> | 
                <i class="fa fa-file-o"></i> <span> <?php the_category(', ') ?> | 
                <i class="fa fa-pencil-square-o"></i> <span><?php _e('By','themnific');?></span> <?php the_author_posts_link(); ?>
            
            </p>

			<div style="clear: both;"></div>

        	<p class="teaser"><?php echo themnific_excerpt( get_the_excerpt(), '350'); ?></p>
            
</li>