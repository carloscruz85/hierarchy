<?php
/*
Plugin Name: cc85 Hierarchy
Plugin URI: https://github.com/carloscruz85/hierarchy
Description: chatbox made with react
Version: 1.0.0
Author: Carlos Cruz
Author URI: https://github.com/carloscruz85/hierarchy
License: GPLv2 or later
Text Domain: cc85
*/

// adding custom style

add_action( 'init', 'add_global_style_to_header' );
function add_global_style_to_header() {
  wp_register_style( 'global_style_cc85_hierarchy', plugins_url('admin/css/style.css', __FILE__), false, '1.0.0', 'all');
}

  add_action('wp_enqueue_scripts', 'enqueue_global_style');
  function enqueue_global_style(){
    wp_enqueue_style( 'global_style_cc85_hierarchy' );
  }

include('admin/data.php');
include('admin/frontend.php');
include('admin/hierarchy.php');
