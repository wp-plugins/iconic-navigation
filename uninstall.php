<?php
/**
 * Iconic Navs Uninstall
 *
 * Deletes meta keys holding icon values
 *
 * @author 		Cadros
 * @version     1.0
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();
global $iconic_navs;
include_once( "classes/cadreu_plugin.php" ); 
include_once( "classes/cadreu_plugin_options.php" );

$options_fields = $iconic_navs->cadreu_Section_Fields();
	//var_dump($options_fields);
	foreach( $options_fields as $options ) {
		foreach ($options as $key => $option ) {
			//var_dump($this->_NAME().$key);
			delete_option( $iconic_navs->_NAME().$key );
			
		}
	} //delete_option( 'iconic-navsmobile_options' );

delete_post_meta_by_key( '_menu_icon' );
delete_post_meta_by_key( '_menu_font_icon' );
?>