<?php get_header(); ?>
<?php
$large_image =  wp_get_attachment_image_src( get_post_thumbnail_id(get_the_ID()), 'fullsize', false, '' ); 
$large_image = $large_image[0]; 
$video_input = get_post_meta($post->ID, 'themnific_video_embed', true);
$project_url = get_post_meta($post->ID, 'themnific_project_url', true);
$project_description = get_post_meta($post->ID, 'themnific_project_description', true);
$featuredimage = get_post_meta($post->ID, 'themnific_fea_image', true);
$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' => 'image') );
?>

<?php the_post(); ?>
    
<div class="container container_block">
    
    <h2 class="itemtitle"><?php the_title(); ?></h2>
    
    <div class="nav_item">
        
        <?php previous_post_link('%link', '<i title="'.__('Previous Project','themnific').'" class="fa fa-angle-double-left"></i>') ?>
    
    	<a href="<?php echo stripslashes(get_option('themnific_url_portfolio'));?>"><i title="<?php _e('Back To Portfolio','themnific');?>"  class="fa fa-angle-double-up"></i></a>
        
        <?php next_post_link('%link', '<i title="'.__('Next Project','themnific').'" class="fa fa-angle-double-right"></i>') ?>
	
    </div>
    
    <div class="hrlineB"><span></span></div>

    <div id="foliosidebar">
    
    
		<?php if($project_url) : ?>
        
        	<a class="mainbutton bigone" href="<?php echo $project_url; ?>"><?php _e('Visit Project','themnific');?> <i class="fa fa-sign-out"></i></a>
        
        <?php endif; ?>
        
        
       	<?php if($project_description) : ?>
        
            <div class="hrline"><span></span></div>
    
            <p class="meta">
    
                <i class="fa fa-info-circle"></i> <?php echo $project_description; ?>
            
            </p>
            
        <?php endif; ?>    
        
        <div class="hrline"><span></span></div>
        
        <p class="meta"><i class="fa fa-clock-o"></i> <?php the_time(get_option('date_format')); ?></p>
                

        <p class="meta"><i class="fa fa-files-o"></i> <?php $terms_of_post = get_the_term_list( $post->ID, 'categories', '',' &bull; ', ' ', '' ); echo $terms_of_post; ?></p>

        <div class="hrline"><span></span></div>
            
    </div>
    
    
    
    
    <div id="foliocontent">   
            
            <?php 
			
			if($featuredimage == 'No') {} else { 
			
				if($video_input) { echo ($video_input); 
				
				
				} elseif ($attachments) { echo get_template_part( '/includes/folio-types/gallery-slider' );
	
	
				} else {the_post_thumbnail('folio_slider');}
			
			}
			
			?> 
            
            <div class="entry entry_item">
             
				<?php the_content(); ?>
                
                <div class="hrline"><span></span></div>  
                
                <?php comments_template( '', true ); ?>
            
            </div>
  
     </div>
     
</div>
        
<?php get_footer(); ?>