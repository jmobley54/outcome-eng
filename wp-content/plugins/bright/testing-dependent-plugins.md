# Setup

First off, set up your phpunit wordpress environment with the wp cli scaffolding tool:

http://wp-cli.org/blog/plugin-unit-tests.html

# Loading Bright [and other dependent plugins]

In your tests/bootstrap.php, check that Bright is loaded:

    function _check_for_dependencies() {
        if ( ! is_plugin_active( 'bright/bright.php' ) ) {
            exit( 'Some Plugin must be active to run the tests.' . PHP_EOL );
        }
    }
    tests_add_filter( 'plugins_loaded', '_check_for_dependencies' );

# BWI - installing WooCommerce via git

    cd /tmp/wordpress/wp-content/plugins
	git clone https://github.com/woothemes/woocommerce.git




