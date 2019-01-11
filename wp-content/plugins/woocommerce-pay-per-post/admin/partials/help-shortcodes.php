<div id="wc-ppp-help-shortcode-tab" class="wc-ppp-help-tab" style="display:none;">
	<?php if ( wcppp_freemius()->is_not_paying() && ! wcppp_freemius()->is_trial()  ) : ?>
    <div class="postbox">
        <h2 class="hndle" id="shortcodes"><?php esc_attr_e( 'Available Shortcodes', 'wc_pay_per_post' ); ?></h2>
        <div class="inside">
            <h4><?php esc_attr_e( 'Default Shortcode', 'wc_pay_per_post' ); ?></h4>
            <input type="text" class="code" value="[woocommerce-payperpost]" style="width:100%;">
            <h4><?php esc_attr_e( 'Attributes', 'wc_pay_per_post' ); ?></h4>
            <ul>
                <li><span class="code"><strong>template</strong></span></li>
                <li><input type="text" class="code" value="[woocommerce-payperpost template='purchased|remaining|all']" style="width:100%;">
                    <ul style="margin-left:10px;">
                        <li><span class="code">purchased</span>
                            <ul>
                                <li><p class="description"><?php esc_attr_e( 'This is the default template. It displays all of the purchased posts to the current logged in user.', 'wc_pay_per_post' ); ?></p></li>
                            </ul>
                        </li>
                        <li><span class="code">remaining</span>
                            <ul>
                                <li><p class="description"><?php esc_attr_e( 'It displays all of the available protected posts which the current logged in user has yet to purchase.', 'wc_pay_per_post' ); ?></p></li>
                            </ul>
                        </li>
                        <li><span class="code">all</span>
                            <ul>
                                <li><p class="description"><?php esc_attr_e( 'This will output a list of all protected posts available for purchase.', 'wc_pay_per_post' ); ?></p></li>
                            </ul>
                        </li>

                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="postbox" style="opacity: .5">
        <h2 class="hndle" id="shortcodes" style="background-color:#087891; color:white;"><?php esc_attr_e( 'PREMIUM ONLY SHORTCODES', 'wc_pay_per_post' ); ?></h2>
		<?php else: ?>
        <div class="postbox">
            <h2 class="hndle" id="shortcodes"><?php esc_attr_e( 'Available Shortcodes', 'wc_pay_per_post' ); ?></h2>
			<?php endif; ?>
            <div class="inside">
                <img src="<?php echo plugin_dir_url( __DIR__ ) . 'img/videos/shortcodes.gif'; ?>" width="100%">

                <h4><?php esc_attr_e( 'Default Shortcode', 'wc_pay_per_post' ); ?></h4>
                <input type="text" class="code" value="[woocommerce-payperpost template='purchased' orderby='post_date' order='DESC']" style="width:100%;">
                <h4><?php esc_attr_e( 'Options', 'wc_pay_per_post' ); ?></h4>
                <p><?php esc_attr_e( 'The base shortcode comes with a variety of options.', 'wc_pay_per_post' ); ?></p>
                <ul>
                    <li><span class="code"><strong>template</strong></span></li>
                    <li><input type="text" class="code" value="[woocommerce-payperpost template='purchased|remaining|all']" style="width:100%;">
                        <ul style="margin-left:10px;">
                            <li><span class="code">purchased</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'This is the default template. It displays all of the purchased posts to the current logged in user.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                            <li><span class="code">remaining</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'It displays all of the available protected posts which the current logged in user has yet to purchase.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                            <li><span class="code">all</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'This will output a list of all protected posts available for purchase.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>

                        </ul>
                    </li>
                    <li><span class="code"><strong>orderby</strong></span></li>
                    <li><input type="text" class="code" value="[woocommerce-payperpost template='purchased' orderby='post_date|ID|title|menu_order']" style="width:100%;">

                        <ul style="margin-left:10px;">
                            <li><span class="code">post_date</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'This is the default orderby. It orders posts by the post_date.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                            <li><span class="code">ID</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'This orders posts by the post ID.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                            <li><span class="code">title</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'This orders posts by the post Title.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                            <li><span class="code">menu_order</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'This orders posts by the menu order.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li><span class="code"><strong>order</strong></span></li>
                    <li><input type="text" class="code" value="[woocommerce-payperpost template='purchased' orderby='post_date' order='DESC']" style="width:100%;">

                        <ul style="margin-left:10px;">
                            <li><span class="code">DESC</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'This is the default order. It orders posts in descending order.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                            <li><span class="code">ASC</span>
                                <ul>
                                    <li><p class="description"><?php esc_attr_e( 'It orders posts in ascending order.', 'wc_pay_per_post' ); ?></p></li>
                                </ul>
                            </li>
                        </ul>
                    </li>

                    <li><span class="code"><strong>category_slug</strong></span></li>
                    <li><input type="text" class="code" value="[woocommerce-payperpost template='purchased' category_slug='beverages-cold-vault' order='DESC']" style="width:100%;">
                        <br>
						<?php esc_attr_e( 'You can filter just by specific categories by inputting the category slug into the shortcode.  If you want multiple categories you can separate them by commas. ', 'wc_pay_per_post' ); ?><a href="https://codex.wordpress.org/Class_Reference/WP_Query#Category_Parameters" target="_blank"><?php esc_attr_e( 'Category Parameters', 'wc_pay_per_post' ); ?></a>
                    </li>

                    <li><span class="code"><strong>tag</strong></span></li>
                    <li><input type="text" class="code" value="[woocommerce-payperpost template='purchased' tag='cover-story']" style="width:100%;">
                        <br>
						<?php esc_attr_e( 'You can filter just by specific tags by inputting the tag slug into the shortcode.  If you want multiple tags you can separate them by commas.', 'wc_pay_per_post' ); ?> <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Tag_Parameters" target="_blank"><?php esc_attr_e( 'Tag Parameters', 'wc_pay_per_post' ); ?></a>
                    </li>
                </ul>

            </div>

        </div>
    </div>