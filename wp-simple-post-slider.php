<?php
/*
Plugin Name: Simple Post Slider Widget
Plugin URI: https://bhubr.eu/projects/wordpress-post-slider/
Description: Display excerpts from recent posts (or other post types) in a widget
Author: Benoît Hubert
Version: 0.9.2
Author URI: https://bhubr.eu
Copyright 2010-2017 Benoit Hubert
*/ 

require_once 'vendor/autoload.php';
require 'class-wp-widget-simple-post-slider.php';
require 'class-wp-simple-post-slider-shortcodes.php';
require 'functions.php';
if(file_exists(__DIR__ . '/wpsps-settings.php')) {
	require __DIR__ . '/wpsps-settings.php';
}
else {
	define('WPSPS_MINIFIED_ASSETS', true);
}

add_filter('mce_external_plugins', "wpnsw_mceplugin_register");
add_filter('mce_buttons', 'wpnsw_mceplugin_add_button', 0);
// add_action( 'wp_print_scripts', 'wpsps_load_assets' );
add_action( 'init', 'wpnsw_localize' );

function wpsps_load_assets() {
	if(is_admin()) {
		return;
	}
	$dir = WPSPS_MINIFIED_ASSETS ? 'assets' : 'src';
	$ext = WPSPS_MINIFIED_ASSETS ? '.min' : '';
	wp_enqueue_style('wpsps',  plugins_url("$dir/wp-simple-post-slider{$ext}.css", __FILE__), [] );
	wp_enqueue_script('wpsps',  plugins_url("$dir/wp-simple-post-slider{$ext}.js", __FILE__), [] );
}

//add_action('init', 'wp_widget_init', 2);
add_action("widgets_init", array('WP_Widget_Simple_Post_Slider', 'register'));
$wpsps_shortcodes = new WP_Simple_Post_Slider_Shortcodes;
?>