<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

<div <?php post_class('singlepost'); ?>> 
 
			<?php 
			$video_input = get_post_meta($post->ID, 'tmnf_video', true);
			$audio_input = get_post_meta($post->ID, 'tmnf_audio', true);
			?>

			<?php 	if(has_post_format('video')){
                  			echo ($video_input);
                 	}elseif(has_post_format('audio')){
                       		echo ($audio_input);
                 	}elseif(has_post_format('gallery')){
                       		echo get_template_part( '/includes/post-types/gallery-slider' );
 					} else {
							if ( has_post_thumbnail());
						 		 the_post_thumbnail('format-single', array('class' => 'main-single'));  
								
			}?>
			
            <div style="clear: both;"></div>
            
            <h2 class="singletitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            
            <p class="meta">
            
            	<i class="fa fa-file-o"></i> <?php the_category(', ') ?>  &bull;  
                
                <i class="fa fa-comments-o"></i> <?php comments_popup_link( __('Comments (0)', 'themnific'), __('Comments (1)', 'themnific'), __('Comments (%)', 'themnific')); ?>
                
            </p>
            
            <div class="hrline"><span></span></div>  

            <div class="entry">
            
            	<?php the_content(); ?>
            
            	<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:','themnific') . '</span>', 'after' => '</div>' ) ); ?>
            
            	<div style="clear: both;"></div>
                
                <div class="hrline"><span></span></div>  
            
            
            </div>
            
            <p class="meta fl">
                <?php the_breadcrumb(); ?><br/>
                <i class="fa fa-clock-o"></i> <span><?php _e('On','themnific');?></span> <?php the_time(get_option('date_format')); ?><br/>
                <i class="fa fa-pencil-square-o"></i> <span><?php _e('By','themnific');?></span> <?php the_author_posts_link(); ?><br/>
                <?php the_tags( '<i class="fa fa-tags"></i>  ',', ',  ''); ?>
            
            </p>
            
            <div style="clear: both;"></div>
            <?php comments_template(); ?>
        
            <p>
            <?php previous_post_link('<span class="fl" style="width:45%;">&laquo; %link</span>'); ?>
            <?php next_post_link('<span class="fr" style="width:45%; text-align:right">%link &raquo;</span>'); ?>
            </p>

	<?php endwhile; else: ?>

		<p><?php _e('Sorry, no posts matched your criteria','themnific');?>.</p>

	<?php endif; ?>

    <div style="clear: both;"></div>

</div>