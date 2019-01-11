<?php
/*
Template Name: Full Width Image Header
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

<div class="hrlineB"></div>
    
    <div class="container" style="overflow:visible">
    
    	<div class="hrlineB"><span></span></div>
    
    	<div class="entryfull" style="overflow:visible">
            
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
            
            <?php the_content(); ?>
            
            <?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
            
            <?php endwhile; endif; ?>
            
       	</div>
        
    </div>
    
   	<div style="clear: both;"></div>
    
<?php get_footer(); ?>