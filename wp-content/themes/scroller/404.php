<?php get_header(); ?> 
    
    <div class="container container_block"> 
    
    	<div class="entryfull">

            <h2 class="singletitle"><?php _e('Nothing found here','themnific');?></h2>
            
           	<h4><?php _e('Perhaps You will find something interesting form these lists...','themnific');?></h4>
            
            <div class="hrlineB"></div>
            <div class="errorentry entry">
			<?php get_template_part('/includes/uni-404-content');?>
            </div>
        </div><!-- #homecontent -->
        

</div>
<?php get_footer(); ?>
