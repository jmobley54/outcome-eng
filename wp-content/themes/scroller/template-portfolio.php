<?php
/*
Template Name: Portfolio
*/
?>
<?php get_header(); ?>
<?php 
$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
$large_image = $large_image[0]; 
$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID),false, '' );
?> 

<div class="resmode-No section_template "
style="  <?php if($large_image) { ?>background-image:url(<?php echo $src[0] ?>);<?php } else {}?> ">

	<div class="container">
    
    	<h2><?php the_title(); ?></h2>
		
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

<div class="container container_alt"> 

	<div id="folio-wrap">

          <ul id="portfolio-list" class="loop">
              
              <?php query_posts( array( 'post_type' => 'myportfoliotype', 'paged' => $paged, 'posts_per_page' => 9));
              
              if ( have_posts() ) : while ( have_posts() ) : the_post();?>
                  
                  <?php
                      $large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
                      $large_image = $large_image[0]; 
                      $another_image_1 = get_post_meta($post->ID, 'themnific_image_1_url', true);
                      $video_input = get_post_meta($post->ID, 'themnific_video_url', true);
                  ?>
                  
                  <li id="post-<?php the_ID(); ?>" class="fourcol rad_big">
                  
                      <?php get_template_part('/includes/folio-types/folio-classic'); ?>
                          
                  </li><!-- #post-<?php the_ID(); ?> -->
                  
              <?php endwhile; endif; ?> 
              
          </ul>	
        
          <div class="clear"></div>
    
          <div class="pagination"><?php pagination('&laquo;', '&raquo;'); ?></div>
        
	<?php wp_reset_query(); ?>
	
</div>


        
</div>
        
<?php get_footer(); ?>