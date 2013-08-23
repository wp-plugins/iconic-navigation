<?php
/**
 * A Plugin Class
 *
 * Works out plugin data: name, file & such. Loads scripts, builds links for plugins list table
 *
 * @class cadrEu_Plugin
 * @version	1.0
 * @since 3.5
 * @author Cadros
 */

if ( ! defined( 'ABSPATH' ) ) exit; // shoo on direct access
if ( ! class_exists( 'cadrEu_Plugin' ) ) {

class cadrEu_Plugin {
public $file;

public function actions() {

	$this->included();
	$this->help = new cadreuHelp(); // help class instance

	add_action( 'init', array( $this, '_NAME' ), 0 );

	add_action('init', array( $this, '_textdomain' ));
	
	add_filter( 'plugin_action_links', array( $this, '_Settings_link' ), 10,2 );
	add_action('wp_enqueue_scripts', array( $this, '_scripts' ) );
	add_action( 'admin_enqueue_scripts', array( $this, '_admin_scripts' ) );
} // end actions


// include files here
function included() {
		include_once( "cadreu_help.php" );
}

/**
* @return plugin name | usage: $this->_NAME()
*/
function _NAME() {
	$nombre = plugin_basename( dirname( $this->file) );
	return $nombre;
}

public function _textdomain() {
		load_plugin_textdomain( 
			$this->_NAME(), 
			false, 
			dirname( plugin_basename( $this->file ) ) . '/languages' 
		);
	}

		// a link to the plugin Settings page at plugins list table
public function _Settings_link( $links, $file  ) {
	if ( $file == plugin_basename( $this->file ) ) {
	$links[]  =  '<a class="cadreu_link" href="' . admin_url( 'options-general.php?page='.$this->_NAME() ) . '">' . __( 'Settings', $this->_NAME() ) . '</a>';

	if(file_exists(plugin_dir_path( $this->file ) .'Help')) {
		$docs_url = plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/Help/'.$this->_NAME().'-docs.php';
	} else {
		$docs_url = 'http://plugins.cardos.eu/demos/'.$this->_NAME().'/docs';
	}
		
	$links[]  =  '<a class="cadreu_link" href="' . $docs_url.'">' . __( 'Docs', $this->_NAME() ) . '</a>';

	$links[]  =  '<a class="cadreu_link" href="http://plugins.cardos.eu/demos/'.$this->_NAME().'/support">' . __( 'Support', $this->_NAME() ) . '</a>';
	}
	return  $links;
}

public function _scripts() {
		wp_enqueue_style( 'cadreu_style', plugins_url().'/'.plugin_basename( dirname( $this->file ) ). '/css/screen.css' );
		global $is_iis7;
		if( $is_iis7 ) {
		wp_enqueue_script('jquery',null,null, '', true);
		wp_enqueue_script( 'ie_seven', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/lte-ie7.js', array( 'jquery' ), '2011-04-28', true );
		wp_enqueue_script( 'respond.min', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/respond.min.js', '', '', false );
		}
}

public function _admin_scripts() {
		wp_enqueue_media('media-models');
		wp_enqueue_media('media-upload');
		wp_enqueue_style( 'cadreu_admin_style', plugins_url().'/'.plugin_basename( dirname( $this->file ) ). '/css/screen.css' );
		wp_enqueue_script( 'respond.min', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/respond.min.js', '', '', false );
		wp_enqueue_script( 'admin_myscripts', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/admin_myscripts.js', '', '', true );
	}
	/*
public static function on_uninstall()
    {
        if ( ! current_user_can( 'activate_plugins' ) )
            return;
        check_admin_referer( 'bulk-plugins' );

        // Important: Check if the file is the one
        // that was registered during the uninstall hook.
        if ( __FILE__ != WP_UNINSTALL_PLUGIN )
            return;
        $options_fields = cadreuPlugin_Options::cadreu_Section_Fields();
	//var_dump($options_fields);
	foreach( $options_fields as $options ) {
		foreach ($options as $key => $option ) {
			//var_dump($this->_NAME().$key);
			delete_option( $this->_NAME().$key );
			
		}
	}
	delete_option( 'iconic-navsmobile_options' );

        # Uncomment the following line to see the function in action
        # exit( var_dump( $_GET ) );
}
*/
		// plugin unistall hook callback
public static function cadreu_uninstall() {
	/*
	$options_fields = cadreuPlugin_Options::cadreu_Section_Fields();
	//var_dump($options_fields);
	foreach( $options_fields as $options ) {
		foreach ($options as $key => $option ) {
			//var_dump($this->_NAME().$key);
			delete_option( $this->_NAME().$key );
			
		}
	} delete_option( 'iconic-navsmobile_options' );
	delete_post_meta_by_key( '_menu_icon' );
delete_post_meta_by_key( '_menu_font_icon' );
*/
	die( var_dump( $_GET ) );
}

} // end class
} // end if class exists