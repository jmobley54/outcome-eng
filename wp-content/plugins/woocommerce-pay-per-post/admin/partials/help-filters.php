<div class="postbox wc-ppp-help-tab" id="wc-ppp-help-filters-tab" style="display:none;">
    <h2 class="hndle"><?php esc_attr_e( 'Available Filters', 'wc_pay_per_post' ); ?></h2>

    <div class="inside">
        <ul>
            <li><span class="code">wc_pay_per_post_args</span>
                <ul>
                    <li><?php esc_attr_e( 'This filter allows you to override the WP Query arguments for the shortcodes.', 'wc_pay_per_post' ); ?></li>
                    <li><pre>
<code>Array
    (
        [orderby] => post_date
        [order] => DESC
        [nopaging] => 1
        [meta_key] => wc_pay_per_post_product_ids
        [meta_value] => 0
        [meta_compare] => >
        [post_status] => publish
    )</code> </pre>
                    </li>
                    <li><strong><?php esc_attr_e( 'Example', 'wc_pay_per_post' ); ?>:</strong>
                        <ul>
                            <li><pre>
<code>add_filter('wc_pay_per_post_args', 'my_theme_wc_ppp_args');

function my_theme_wc_ppp_args($args){
    $args['orderby'] = 'menu_order';
    return $args;
}
</code></pre>

                            </li>
                        </ul>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</div>