<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if(!function_exists('eventon')){
	function eventon(){
		return EventON::instance();
	}
}

if(!function_exists('evo_license')){
	function evo_license(){
		return eventon()->license;
	}
}