<?php
/**
 * A Plugin Class
 *
 * Works out plugin data: name, file & such. Loads scripts, builds links for plugins list table
 *
 * @class cadrEu_Plugin
 * @version	1.1.6
 * @since 1.0
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
	//if(is_admin())
	//var_dump($nombre);
	//return 'iconic-navigation';
	return $nombre;
}

public function _textdomain() {
		load_plugin_textdomain( 
			$this->_NAME(), 
			false, 
			dirname( plugin_basename( $this->file ) ) . '/languages' 
		);
	}

// links to plugin Settings/Help pages at plugins list table
public function _Settings_link( $links, $file  ) {
	//var_dump($file, plugin_basename( $this->file ) );
	if ( $file == plugin_basename( $this->file ) ) {
	$links[]  =  '<a class="cadreu_link" href="' . admin_url( 'options-general.php?page='.$this->_NAME() ) . '">' . __( 'Settings', $this->_NAME() ) . '</a>';

	if(file_exists(plugin_dir_path( $this->file ) .'Help')) {
		$docs_url = plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/Help/'.$this->_NAME().'-docs.php';
	} else {
		$docs_url = 'http://plugins.cadros.eu/demos/'.$this->_NAME().'/docs';
	}
		
	$links[]  =  '<a class="cadreu_link" href="' . $docs_url.'">' . __( 'Docs', $this->_NAME() ) . '</a>';

	$links[]  =  '<a class="cadreu_link" href="http://plugins.cadros.eu/demos/'.$this->_NAME().'/support">' . __( 'Support', $this->_NAME() ) . '</a>';
	}
	return  $links;
}

public function _scripts() {
		wp_enqueue_style( 'iconic-nav-style', plugins_url().'/'.plugin_basename( dirname( $this->file ) ). '/css/screen.css' );

		$binary = get_option( $this->_NAME().'increase_font_support');
		if( $this->help->issetField($binary, 'binary_encoding') ) {
			wp_enqueue_style( 'iconic-nav-binary-font-support', plugins_url().'/'.plugin_basename( dirname( $this->file ) ). '/css/binary-icon-font.css' );
		}
		global $is_iis7;
		if( $is_iis7 ) {
		wp_enqueue_script('jquery',null,null, '', true);
		wp_enqueue_script( 'ie_seven', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/lte-ie7.js', array( 'jquery' ), '2011-04-28', true );
		wp_enqueue_script( 'respond.min', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/respond.min.js', '', '', false );
		
		if( ! $this->help->issetField($binary, 'binary_encoding') ) { // include if for old bro regardless
				wp_enqueue_style( 'iconic-nav-binary-font-support', plugins_url().'/'.plugin_basename( dirname( $this->file ) ). '/css/binary-icon-font.css' );
			}
		}
}

public function _admin_scripts() {
		wp_enqueue_media('media-models');
		wp_enqueue_media('media-upload');
		wp_enqueue_style( 'cadreu_admin_style', plugins_url().'/'.plugin_basename( dirname( $this->file ) ). '/css/screen.css' );
		wp_enqueue_script( 'respond.min', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/respond.min.js', '', '', false );
		wp_enqueue_script( 'admin_myscripts', plugins_url().'/'.plugin_basename( dirname( $this->file ) ) . '/js/admin_myscripts.js', '', '', true );
	}

} // end class
} // end if class exists
