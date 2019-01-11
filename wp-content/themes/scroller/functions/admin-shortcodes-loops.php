<?php

/*-----------------------------------------------------------------------------------*/
/* 1. blog_latest  */
/*-----------------------------------------------------------------------------------*/

function tmnf_blog_latest($atts, $content = null) {
	extract(shortcode_atts(array(
		"query" => '',
		"posts_number" => '',
	), $atts));
	global $wp_query,$paged,$post;
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
		$query .= 'showposts='.$posts_number;
	$wp_query->query($query);
	ob_start();
	?>
	<ul class="loop">
	<?php while ($wp_query->have_posts()) : $wp_query->the_post();?>
    
    	<li id="post-<?php the_ID(); ?>" class="sixcol rad_big">
			<?php get_template_part('/includes/post-types/blog-classic'); ?>
       	</li>
            
	<?php endwhile; ?>
	</ul>
    <?php wp_reset_query(); ?>
	<?php $wp_query = null; $wp_query = $temp;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode("blog_latest", "tmnf_blog_latest");

/*-----------------------------------------------------------------------------------*/
/* 2. [blog_featured category=design posts_number="6"]  */
/*-----------------------------------------------------------------------------------*/

function myLoop($atts, $content = null) {
        extract(shortcode_atts(array(
                "pagination" => 'true',
                "query" => '',
                "category" => '',
                "posts_number" => '',
        ), $atts));
        global $wp_query,$paged,$post;
        $temp = $wp_query;
        $wp_query= null;
        $wp_query = new WP_Query();
        if(!empty($category)){
                $query .= 'meta_key=post_views_count&orderby=date&category_name='.$category.'&showposts='.$posts_number;
        }
        if(!empty($query)){
                $query .= $query;
        }
        $wp_query->query($query);
        ob_start();
        ?>
        <ul class="loop">
        <?php while ($wp_query->have_posts()) : $wp_query->the_post(); ?>      
            <li id="post-<?php the_ID(); ?>" class="sixcol rad_big">
                <?php get_template_part('/includes/post-types/blog-classic'); ?>
            </li>
        <?php endwhile; ?>
        </ul>
        <?php $wp_query = null; $wp_query = $temp;
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
}
add_shortcode("blog_featured", "myLoop");

/*-----------------------------------------------------------------------------------*/
/* 3. portfolio_featured  */
/*-----------------------------------------------------------------------------------*/

function tmnf_portfolio_featured($atts, $content = null) {
	extract(shortcode_atts(array(
		"query" => '',
		"category" => '',
		"posts_number" => '',
	), $atts));
	global $wp_query,$paged,$post;
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	if(!empty($category)){
		$query .= 'post_type=myportfoliotype&categories='.$category.'&showposts='.$posts_number;
	}
	if(!empty($query)){
		$query .= $query;
	}
	$wp_query->query($query);
	ob_start();
	?>
	<ul class="loop">
	<?php while ($wp_query->have_posts()) : $wp_query->the_post();?>
    
    	<li class="fourcol rad_big">
			<?php get_template_part('/includes/folio-types/folio-classic'); ?>
       	</li>
            
	<?php endwhile; ?>
	</ul>
    <?php wp_reset_query(); ?>
    <div style="clear: both;"></div>
	<?php $wp_query = null; $wp_query = $temp;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode("portfolio_featured", "tmnf_portfolio_featured");



/*-----------------------------------------------------------------------------------*/
/* 4. portfolio_latest  */
/*-----------------------------------------------------------------------------------*/

function tmnf_portfolio_latest($atts, $content = null) {
	extract(shortcode_atts(array(
		"query" => '',
		"posts_number" => '',
	), $atts));
	global $wp_query,$paged,$post;
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
		$query .= 'post_type=myportfoliotype&showposts='.$posts_number;
	$wp_query->query($query);
	ob_start();
	?>
	<ul class="loop">
	<?php while ($wp_query->have_posts()) : $wp_query->the_post();?>
    
    	<li class="fourcol rad_big">
			<?php get_template_part('/includes/folio-types/folio-classic'); ?>
       	</li>
            
	<?php endwhile; ?>
	</ul>
    <?php wp_reset_query(); ?>
    <div style="clear: both;"></div>
	<?php $wp_query = null; $wp_query = $temp;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode("portfolio_latest", "tmnf_portfolio_latest");

/*-----------------------------------------------------------------------------------*/
/* 5. carousel_featured  */
/*-----------------------------------------------------------------------------------*/

function tmnf_carousel_featured($atts, $content = null) {
	extract(shortcode_atts(array(
		"query" => '',
		"category" => '',
		"posts_number" => '',
	), $atts));
	global $wp_query,$paged,$post;
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	if(!empty($category)){
		$query .= 'post_type=myportfoliotype&categories='.$category.'&showposts='.$posts_number;
	}
	if(!empty($query)){
		$query .= $query;
	}
	$wp_query->query($query);
	ob_start();
	wp_enqueue_script('jquery.flexslider.start.carousel', get_template_directory_uri() .'/js/jquery.flexslider.start.carousel.js','','', true);
	?>
    
    <div class="widgetflexslider flexslider">
    <ul class="slides">
	<?php while ($wp_query->have_posts()) : $wp_query->the_post();?>
    
    	<li>
			<?php get_template_part('/includes/folio-types/folio-carousel'); ?>
       	</li>
            
	<?php endwhile; ?>
	</ul>
    </div>
    <?php wp_reset_query(); ?>
    <div style="clear: both;"></div>
	<?php $wp_query = null; $wp_query = $temp;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode("carousel_featured", "tmnf_carousel_featured");


/*-----------------------------------------------------------------------------------*/
/* 7. slider_featured  */
/*-----------------------------------------------------------------------------------*/

function tmnf_slider_featured($atts, $content = null) {
	extract(shortcode_atts(array(
		"query" => '',
		"category" => '',
		"posts_number" => '',
	), $atts));
	global $wp_query,$paged,$post;
	$temp = $wp_query;
	$wp_query= null;
	$wp_query = new WP_Query();
	if(!empty($category)){
		$query .= 'post_type=myportfoliotype&categories='.$category.'&showposts='.$posts_number;
	}
	if(!empty($query)){
		$query .= $query;
	}
	$wp_query->query($query);
	ob_start();
	wp_enqueue_script('jquery.flexslider.start.featured', get_template_directory_uri() .'/js/jquery.flexslider.start.featured.js','','', true);
	?>
    
    <div class="featuredflex flexslider">
    <ul class="slides">
	<?php while ($wp_query->have_posts()) : $wp_query->the_post();?>
    
    	<li>
			<?php get_template_part('/includes/folio-types/folio-slider'); ?>
       	</li>
            
	<?php endwhile; ?>
	</ul>
    </div>
    <?php wp_reset_query(); ?>
    <div style="clear: both;"></div>
	<?php $wp_query = null; $wp_query = $temp;
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode("slider_featured", "tmnf_slider_featured");

/*-----------------------------------------------------------------------------------*/
/* THE END */
/*-----------------------------------------------------------------------------------*/
?>