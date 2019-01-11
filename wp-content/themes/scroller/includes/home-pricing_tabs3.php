<div class="pricing-wrap">
	<ul class="pricing_main">
    
		<?php $loop = new WP_Query( array( 'post_type' => 'mypricing_tabstype', 'posts_per_page' => '3') ); ?>
        <?php while ( $loop->have_posts() ) : $loop->the_post(); ?>
        <?php 
			$pricing_main = get_post_meta($post->ID, 'themnific_pricing_main', true);
			$pricing_price = get_post_meta($post->ID, 'themnific_pricing_price', true);
			$pricing_aditional = get_post_meta($post->ID, 'themnific_pricing_aditional', true);
			$pricing_signup_url = get_post_meta($post->ID, 'themnific_pricing_signup_url', true);
			$pricing_signup_label = get_post_meta($post->ID, 'themnific_pricing_signup_label', true);
		?>
        
            <li class="pricing pricing_three <?php echo $pricing_main;?>">
                
                <h2><?php the_title(  ); ?></h2>  
                
                <div class="plan-head">
                
            		<div class="plan-price"><?php echo $pricing_price;?></div>
                    
            		<div class="plan-terms"><?php echo $pricing_aditional;?></div>
                
                </div>
                       
                <?php the_content(); ?>
                
                <div class="plan-bottom">
                
                	<a class="rad" href="<?php echo $pricing_signup_url; ?>"><?php echo $pricing_signup_label; ?></a>
                
                </div>
                
            </li>
        
        <?php endwhile; ?>
    
    </ul>
</div> 
<div style="clear: both;"></div>	