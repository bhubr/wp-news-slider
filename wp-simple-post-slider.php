<?php
/*
Plugin Name: Simple Post Slider Widget
Plugin URI: https://bhubr.eu/projects/wordpress-post-slider/
Description: Display excerpts from recent posts (or other post types) in a widget
Author: Benoît Hubert
Version: 0.9.1
Author URI: https://bhubr.eu
Copyright 2010-2017 Benoit Hubert
*/ 

require 'class-wp-widget-simple-post-slider.php';
require 'functions.php';

add_filter('mce_external_plugins', "wpnsw_mceplugin_register");
add_filter('mce_buttons', 'wpnsw_mceplugin_add_button', 0);


add_action( 'wp_print_scripts', 'wpnsw_load_scripts' );
add_action( 'get_header', 'wpnsw_load_styles' );
add_action( 'init', 'wpnsw_localize' );

//add_action('init', 'wp_widget_init', 2);
add_action("widgets_init", array('WP_Widget_Simple_Post_Slider', 'register'));

?>