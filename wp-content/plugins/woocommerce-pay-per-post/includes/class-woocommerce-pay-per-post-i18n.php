<?php

class Woocommerce_Pay_Per_Post_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
				'wc_pay_per_post',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
