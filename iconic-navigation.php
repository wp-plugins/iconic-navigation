<?php
/*
Plugin Name: Iconic Navigation for Wordpress
Plugin URI: http://cadros.eu/
Description: Upload image icon to a menu item or chose from over 1400 ready font icons. Add custom style. Set a menu display options for smaller screens
Version: 1.1.6
Author: Cadros
Author URI: http://cadros.eu/
Tags: menu, backend, navigation, nav, icon, flat icon, image menu, font icons
Requires at least: 3.5
Tested up to: 3.9.2
Text Domain: iconic_navigation
Domain Path: 
License: GPLv2 or later
*/

if ( ! defined( 'ABSPATH' ) ) exit; // shoo on direct access


add_action( 'plugins_loaded', array( 'IconicNavs', 'Single_instance' ) );

class IconicNavs {
    protected static $call;
    
    public static function Single_instance() {
        is_null( self::$call ) AND self::$call = new self;
        return self::$call;
    }

	public function __construct()  {
    	self::Plugin_classes();
    }

	public function Plugin_classes() {
		$data = str_replace('-', '_', basename(dirname(__FILE__)) ); // get plugin name
    	global $$data; // global obj $plugin_name. valid at plugin funs  

    	$classes = glob(plugin_dir_path( __FILE__ ).'classes/*.php');
    	if( is_array ( $classes ) )  {
        foreach ( $classes as $class ) 
            include_once $class;
		}
		// plugin funs here
        //include_once( "iconavs_icons.php" ); // font icons funs
        include_once( "iconavs_menu_fun.php" ); // nav_menu funs
        
    }
} // end main file class
?>