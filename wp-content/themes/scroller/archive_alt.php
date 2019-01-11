<?php get_header(); ?>
<div class="resmode-No section_template "
style="  <?php if(get_option('themnific_portfolio_image')) { ?>background-image:url(<?php echo esc_url(get_option('themnific_portfolio_image'));?>);<?php } else {}?> ">

	<div class="container">
    
    	<h2><?php single_cat_title(); ?></h2>
		
	</div>

</div>


<div id="portfolio-filter" class="body3">
 
	<div class="container">
    
        <ul>
        
            <li><a class="current" href="<?php echo stripslashes(get_option('themnific_url_portfolio'));?>">
            
            <?php _e('All','themnific');?></a></li>
            
            <?php wp_list_categories('taxonomy=categories&orderby=ID&title_li='); ?> 
            
        </ul>

	</div>

</div>

<div class="hrlineB"></div>


<div class="container">


	<ul id="portfolio-list" class="loop">
		
       	<?php while (have_posts()) : the_post(); ?>
            
            <?php
                      $large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
                      $large_image = $large_image[0]; 
                      $another_image_1 = get_post_meta($post->ID, 'themnific_image_1_url', true);
                      $video_input = get_post_meta($post->ID, 'themnific_video_url', true);
            ?>
            
                <li id="post-<?php the_ID(); ?>" class="threecol_spec rad_big">
                
                    <?php get_template_part('/includes/folio-types/folio-classic4'); ?>
                        
                </li><!-- #post-<?php the_ID(); ?> -->
            
		<?php endwhile; ?> 
    
          </ul>	
        
          <div class="clear"></div>
    
          <div class="pagination"><?php pagination('&laquo;', '&raquo;'); ?></div>
        
	<?php wp_reset_query(); ?>
	
</div>


        
</div>
        
<?php get_footer(); ?>