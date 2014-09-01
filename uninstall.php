<?php
/**
 * IconicNavigation Uninstall
 *
 * Deletes_options and meta keys holding icon values
 *
 * @author 		Cadros
 * @version     1.1.6
 */
if( !defined('WP_UNINSTALL_PLUGIN') ) exit();
global $iconic_navigation, $wpdb;
include_once( "classes/cadreu_plugin.php" ); 
include_once( "classes/cadreu_plugin_options.php" );

$options_fields = $iconic_navigation->cadreu_Section_Fields();

// Single site
if ( !is_multisite() ) {

foreach( $options_fields as $options ) {
	foreach ($options as $key => $option ) {
		delete_option( $iconic_navigation->_NAME().$key );

		delete_post_meta_by_key( '_menu_icon' );
		delete_post_meta_by_key( '_menu_font_icon' );
	}
}
} // Multisite
else {
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
		foreach( $options_fields as $options ) {
			foreach ($options as $key => $option ) {
				delete_option( $iconic_navigation->_NAME().$key );

				delete_post_meta_by_key( '_menu_icon' );
				delete_post_meta_by_key( '_menu_font_icon' ); 
			}
		} 
    }
    switch_to_blog( $original_blog_id );
}
?>
