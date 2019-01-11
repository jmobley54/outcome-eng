<div class="tab-post">

	<?php if ( has_post_thumbnail()) : ?>
    
         <a href="<?php the_permalink(); ?>" title="<?php the_title();?>" >
         <?php the_post_thumbnail( 'tabs',array('title' => "")); ?>
         </a>
         
    <?php endif; ?>

        <a class="tab-title" href="<?php the_permalink(); ?>"><?php echo short_title('...', 14);?></a>
        
                <p class="meta">
                
                      <?php the_time(get_option('date_format')); ?> &bull; <?php comments_popup_link( __('Comments (0)', 'themnific'), __('Comments (1)', 'themnific'), __('Comments (%)', 'themnific')); ?>
                      
                </p>
        
</div>