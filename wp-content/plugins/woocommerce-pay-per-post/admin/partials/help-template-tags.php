<div class="postbox wc-ppp-help-tab" id="wc-ppp-help-template-tags-tab" style="display:none;">
    <h2 class="hndle"><?php esc_attr_e( 'Available Template Tags', 'wc_pay_per_post' ); ?></h2>
    <div class="inside">
        <p><?php esc_html_e( 'Out of the box this plugin will work with any theme which uses the standard WordPress function <span class="code">the_content()</span> for those themes that do not utilize the_content() you can use the following static functions in your templates.', 'wc_pay_per_post' ); ?></p>

        <code>Woocommerce_Pay_Per_Post_Helper::has_access()</code>
        <p class="description"><?php esc_attr_e( 'This checks if the current user has access to the page. It returns true/false', 'wc_pay_per_post' ); ?></p>
        <code>Woocommerce_Pay_Per_Post_Helper::get_no_access_content()</code>
        <p class="description"><?php esc_attr_e( 'This returns the content specified in the setting page, or the override content if specified on post.', 'wc_pay_per_post' ); ?></p>
        <strong><?php esc_attr_e( 'Example', 'wc_pay_per_post' ); ?></strong>
        <pre><code>&lt;?php if ( Woocommerce_Pay_Per_Post_Helper::has_access() ): ?&gt;
    This post is cool.
&lt;?php else: ?&gt;
    &lt;?php echo Woocommerce_Pay_Per_Post_Helper::get_no_access_content(); ?&gt;
&lt;?php endif; ?&gt;</code></pre>

    </div>
</div>