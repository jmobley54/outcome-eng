<li <?php post_class(); ?>>
<?php echo tmnf_ribbon() ?>

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
                    if ( has_post_thumbnail()); ?>
                    
                        <a href="<?php the_permalink(); ?>">  
  
                             <?php the_post_thumbnail('format-single', array('class' => 'main-single')); ?>
      
                        </a>  
                        
    <?php }?>
            
			<div style="clear: both;"></div>
            
            <h2 class="singletitle"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
            
            <p class="meta">
            
            	<i class="fa fa-file-o"></i> <?php the_category(', ') ?>  &bull;  
                
                <i class="fa fa-comments-o"></i> <?php comments_popup_link( __('Comments (0)', 'themnific'), __('Comments (1)', 'themnific'), __('Comments (%)', 'themnific')); ?>
                
            </p>
            
            <div class="hrline"><span></span></div>  

    		<div class="entry">    
            
				<?php global $more; $more = 0; ?>
                
                <?php the_content('Continue Reading'); ?> 
                  
           	</div>
            
            <div class="hrline"><span></span></div>  
            
            <p class="meta fl">
                
                <i class="fa fa-clock-o"></i> <span><?php _e('On','themnific');?></span> <?php the_time(get_option('date_format')); ?><br/>
                <i class="fa fa-pencil-square-o"></i> <span><?php _e('By','themnific');?></span> <?php the_author_posts_link(); ?>
            
            </p>
                
            <a class="mainbutton fr" href="<?php the_permalink(); ?>"><?php _e('Read More','themnific');?> &#187;</a>
                  
</li>