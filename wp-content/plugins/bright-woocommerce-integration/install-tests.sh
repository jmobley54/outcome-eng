
#
cat <<EOF
To get this far, you should have ALREADY installed and run the phpunit tests from the Bright plugin.

EOF

if [ ! -d /tmp/wordpress/wp-content/plugins/woocommerce ]; then
    cat <<EOF
Fetching WooCommerce; using the development SVN version from here:

    git clone https://github.com/woothemes/woocommerce.git
EOF
    cd /tmp/wordpress/wp-content/plugins    
    git clone https://github.com/woothemes/woocommerce.git
fi

if [ ! -d /tmp/wordpress/wp-content/plugins/bright ]; then
    cat <<EOF

Bright not installed in /tmp/wordpress/wp-content/plugins/bright

Install it and try again.  Maybe:

(cd .. ; tar cvf - bright) | (cd /tmp/wordpress/wp-content/plugins/ ; tar xvf -)

EOF
    exit 1
else
   cat <<EOF
All good.  Have fun.
EOF

   exit 0
fi
