<?php
/*
Plugin Name: MI Booking
Plugin URI: 
Description: WP plugin of multiple booking pages.
Version: 1.0
Author: Ivan Mudrik
Author URI: https://www.upwork.com/users/~01ab4ca7a77d022c2c
Text Domain: mi_booking
License: 
*/
defined( 'ABSPATH' ) or die( '<h3>No access to the file!</h3>' );

define('MI_Booking_PATH', dirname(__FILE__));
$MI_Booking_DIR = (strtolower(substr(strrchr(MI_Booking_PATH,"\\"),1)))?strtolower(substr(strrchr(MI_Booking_PATH,"\\"),1)):strtolower(substr(strrchr(MI_Booking_PATH,"/"),1));
define('MI_Booking_DIR', $MI_Booking_DIR);
define('MI_Booking_URL', plugins_url('/').MI_Booking_DIR.'/');

include_once('lib/class.mi_booking.php');
$mi_booking = new MI_Booking();

add_action( 'plugins_loaded', array($mi_booking, 'textdomain'));
add_action('init', array($mi_booking, 'init'));
add_action('admin_init', array($mi_booking, 'plugin_settings'));
add_action('admin_menu' , array($mi_booking, 'admin_menu'));

load_plugin_textdomain('mi_booking', false, basename( dirname( __FILE__ ) ) . '/languages' );
register_activation_hook(__FILE__, array('MI_Booking', 'install'));
register_uninstall_hook(__FILE__, array('MI_Booking', 'uninstall'));
