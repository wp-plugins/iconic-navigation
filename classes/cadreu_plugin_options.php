<?php
/*
Plugin Options Page
*/
//include_once( "cadreu_plugin.php" ); // parent class there
if ( ! defined( 'ABSPATH' ) ) exit; // shoo on direct access

if ( ! class_exists( 'cadreuPlugin_Options' ) ) {


class cadreuPlugin_Options extends cadrEu_Plugin {
static $options_call;
public $sections = array( 'menu_locations', 'mobile_options', 'increase_font_support' ); // add sections names here | will be used by cadreu_Add_Sections() 


	// adds plugin's actions to wp 
	// this class instance must call for this fun 
function add_options() {
add_action( 'admin_menu', array( $this, 'cadreu_settings_page' ), 0 );
add_action( 'admin_init', array( $this, 'admin_init' ), 10 );
add_action('wp_head', array( $this, 'cadreu_NavMenus_custom_css') );
//register_uninstall_hook( screw it ); 
} 


function admin_init() {
self::cadreu_Add_Sections();
self::cadreu_Setting_Fields();
}


		// add a plugin Settings page 
public function cadreu_settings_page() {
	$cap = !isset($this->cap) ? 'manage_options' : $this->cap;
	add_options_page( 
		$this->help->UseName( $this->_NAME(),1).' settings', 
		$this->help->UseName( $this->_NAME(),1).' Settings', $cap,
		$this->_NAME(),
		array( $this, '_options_form') );
}

		// rendere settings form 
public function _options_form() { ?>
<div>
<div class="cadreu_credits"> 
<a href="http://cadros.eu" class="logo">
<img src="<?php echo plugins_url().'/'.plugin_basename( dirname( $this->file ) ) ?>/css/img/cadrosSite.png" class="cadro_logo"/>
</a>
<?php $handy_links = parent::_Settings_link('', plugin_basename( $this->file ) );
foreach ($handy_links as $key=>$link ) {
	if($key == 0) continue;
	echo ' • '.$link;
}
?>
</div>
<h2 class="setting_page_title"><?php echo $this->help->UseName( $this->_NAME(), 1)?> Settings</h2>
<form action="options.php" class="cadreu_pluginForm" method="post">
<?php settings_fields( $this->_NAME().'settings' ); ?>
<?php do_settings_sections( $this->_NAME() ); ?>
<input name="Submit" type="submit" class="butt button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
</form></div> <?php
} 

		// add sections
function cadreu_Add_Sections( ) {
	foreach ($this->sections as $section ) {
		add_settings_section( $this->_NAME().$section, $this->help->UseName($section, 'nice_name'), array( $this, $section.'_deza'), $this->_NAME() );
	}
}

function cadreu_Section_Fields() {
	foreach ($this->sections as $section ) {
		$options_call = $section;
		$options_fields[$section] = self::$options_call();
	} return $options_fields;
} 

function cadreu_Setting_Fields( ){
	$options_fields = self::cadreu_Section_Fields();
	
	foreach( $options_fields as $group => $options ) {
		if( !is_array( $options)  ) continue;

		foreach ($options as $option => $field ) {

		if( null == $field  ) continue;

		register_setting( $this->_NAME().'settings', $this->_NAME().$option ); 
		
			// displaying fields
		foreach( $field as $key => $val) {
			// if args, pass them to a field output callback
		$title =  isset( $val['title'] ) ? $val['title'] : '';
		$help =  isset( $val['help'] ) ? $val['help'] : ''; 
 		$select =  isset( $val['select'] ) ? $val['select'] : ''; 
		$label =  isset( $val['label'] ) ? $val['label'] : ''; 
		$args = array('id' => $this->_NAME().$option.'_'.$key, 
					  'name' => $this->_NAME().$option.'['.$key.']', 
					  'option' => $this->_NAME().$option, 
					  'field' => $key,
					  'help' => $help,
					  'select' => $select,
					  'label_for' => $label );
			add_settings_field(
				$this->_NAME().$option.'_'.$key, 
				$title,
				array( $this, '_'.$val['type'] ),
				$this->_NAME(),
				$this->_NAME().$val['section'], 
				$args
				);			
			}
		}  
	} 		
}


	// field output calbacks
function _radio() {
	$arg = func_get_args(); 	
	$options = get_option($arg[0]['option']);

	$items = $arg[0]['select'] ? $arg[0]['select'] : array('Yes', 'No');
	if($arg[0]['label_for'] )
	echo '<label class="block" for="'.$arg[0]['name'] .'">'.$arg[0]['label_for'].'</label>';

	foreach($items as $key => $item) {
		$val = ! is_integer($key) ? $key : $item;
		$checked = '';

	if( $options ) {
	if( array_key_exists($arg[0]['field'], $options) )
	$checked = $options[$arg[0]['field']] == $val ? ' checked="checked" ' : '';
	}
	echo '<input'. $checked.' id="'.$arg[0]['id'].'" name="'.$arg[0]['name'].'" type="radio" value="'.$val.'"/><span>'.$item.'</span>';
	}
	if ($arg[0]['help']) {
		echo '<p>'.$arg[0]['help'].'</p>';
	}
}

function _check() {
	$arg = func_get_args(); 
	$options = get_option($arg[0]['option']);

	if( $arg[0]['select'] ) { // do multiple checkboxes
	foreach( $arg[0]['select'] as $k => $item) {
		$checked = '';

	if( $options ) {
	if($options[$arg[0]['field']]) // if an option is already saved, mark the ch-box checked
	$checked = array_key_exists( $k, $arg[0]['select'] ) ? ' checked="checked" ' : '';
	}
	echo '<input'. $checked.' id="'.$arg[0]['id'].'" name="'.$arg[0]['name'].'[]" type="checkbox" value="'. $k.'"  /><span>'.$item.'</span>';
	} 
	} else { // single checkbox
	$checked = $options[$arg[0]['field']] == 'on' ? ' checked="checked" ' : '';
	echo '<input'. $checked.' id="'.$arg[0]['id'].'" name="'.$arg[0]['name'].'" type="checkbox" />';
	}
	if ($arg[0]['help']) {
		echo '<p>'.$arg[0]['help'].'</p>';
	}
}

function _text() {
	$arg = func_get_args();
	$options = get_option($arg[0]['option']);

	$value = $options[$arg[0]['field']];
	echo '<label for="'.$arg[0]['name'] .'">'.$arg[0]['label_for'].'</label>';
	echo '<input id="'.$arg[0]['id'].'" name="'.$arg[0]['name'].'" size="20" type="text" value="'.$value.'" />';
	if ($arg[0]['help']) {
		echo '<p>'.$arg[0]['help'].'</p>';
	}
}

function  _dropdown() {
	$arg = func_get_args();
	$options = get_option($arg[0]['option']);
	$items = $arg[0]['select'];
	echo '<select "'.$arg[0]['id'].'" name="'.$arg[0]['name'].'">';
	foreach($items as $item) {
		 
		$selected = ($options[$arg[0]['field']] == $item) ? 'selected="selected"' : '';
		echo "<option value='$item' $selected>$item</option>";
	}
	echo "</select>";
}


// sections' callbacks

function menu_locations_deza() {
	echo "<p class='section_deza'>Set up icons display for each menu location that has a menu assigned to it</p>";
	if(! get_nav_menu_locations() || array_sum( get_nav_menu_locations() ) == 0 ) {
		global $wp_version;
		// if higher then 3.6, give this new link to locations
		$sublink = 3.6 >= substr($wp_version, 0, 3 ) ? 
		'?action=locations' : '';
		echo '<b class="warning">Looks like no menu has been chosen for any location. Visit <a href="'.admin_url( 'nav-menus.php'.$sublink, 'http' ) .'">Menu section</a> to set it up.</b>';
	}
}

function menu_locations() {
	$menus = get_registered_nav_menus();
	$assigned_menus = get_nav_menu_locations();

	if( ! $assigned_menus ) return;

	foreach ($menus as $location => $nice_locationName) {

		// check if location has menu
	if( ! array_key_exists($location, $assigned_menus) ) {
		$options_fields[$location] = null;
		continue;
	}
	if( $assigned_menus[$location] == 0) {
		$options_fields[$location] = null;
		continue;
	}
	$options_fields[$location] = array(
		'icon_type' => array(
				 'title'=> __($nice_locationName, 'twentytwelve'),
				 'help' => 'Choose icon type to use at this menu location',
				 'type'=>'radio',
				 'section'=>__FUNCTION__,
				 'select' => array( 'img_ics' => 'Image icons','font_ics' => 'Font icons'),
				 'label' => 'Use :',
				 ),

		'img_icons_custom_css' => array(
				 'help' => 'Type in CSS rules to be used at the image icons. E.g.: color:red; padding:5px',
				 'type'=>'text',
				 'section'=>__FUNCTION__,
				 'label' => 'Custom CSS for imgage icons of this menu',
				 ),
		'font_icons_custom_css' => array(
				 'help' => 'Type in custom style for font icons. E.g.: color:red; font-size:30px',
				 'type'=>'text',
				 'section'=>__FUNCTION__,
				 'label' => 'Custom CSS for font icons of this menu'
				 ),
		'icon_size' => array(
				 'help' => 'Add image icon size in px or %. Default is 30px',
				 'type'=>'text',
				 'section'=>__FUNCTION__,
				 'label' => 'Image icons size for this menu'
				 ) 
		);
	}  	return $options_fields;
}


function mobile_options_deza() { 
	echo "<p class='section_deza'>Set up display for smaller screens</p>";
	// if no menu assigned give link to menu edit
	if(! get_nav_menu_locations() || array_sum( get_nav_menu_locations() ) == 0  ) {
		global $wp_version;
		// if higher then 3.6, give new link to locations
		$sublink = 3.6 >= substr($wp_version, 0, 3 ) ? 
		'?action=locations' : '';
		echo '<b class="warning">Set up menus at <a href="'.admin_url( 'nav-menus.php'.$sublink, 'http' ) .'">Menu section</a> before using this option.</b>';
	}
}

function mobile_options() {
	$menus = get_registered_nav_menus();
	$assigned_menus = get_nav_menu_locations();

	if( ! $assigned_menus || array_sum( $assigned_menus ) == 0  ) return;

	$allowed_menu = array();
	foreach ($menus as $slug => $description ) {

		if( array_key_exists($slug, $assigned_menus) && 0 !== $assigned_menus[$slug] ) 

		$allowed_menu[$slug] = $description;
	}
	$options_fields = array(
		'mobile_options' => array(
			'use_nav_icons'=> array(
				  'title'=>'Display icons only at:',
				  'type'=>'check',
				  'section' => __FUNCTION__,
				  'select' => $allowed_menu
				  )
					)				
	);  return $options_fields;
}


function increase_font_support_deza() { 
	echo "<p class='section_deza'>Chose this option if you experience some icon not showing in some browsers or need to support older versions of Internet Explorer (older then ie7).</p>";
}

function increase_font_support() {
	$menus = get_registered_nav_menus();
	$assigned_menus = get_nav_menu_locations();

	if( ! $assigned_menus || array_sum( $assigned_menus ) == 0  ) return;

	$allowed_menu = array();
	foreach ($menus as $slug => $description ) {

		if( array_key_exists($slug, $assigned_menus) && 0 !== $assigned_menus[$slug] ) 

		$allowed_menu[$slug] = $description;
	}
	$options_fields = array(
		'increase_font_support' => array(
			'binary_encoding'=> array(
				  'title'=>'Include Base64 encoded font in CSS',
				  'type'=>'radio',
				  'section' => __FUNCTION__,
				  )
				)				
	);  return $options_fields;
}


	// ~~~~~~~~~~~~~ end settings page ~~~~~~~~~~~~~~~~~~~~

// usage: (pluginName.my_option, use_nav_icons)
function getTheme_settings($group, $field = null ) {
	$get_o = get_option( $this->_NAME().$group);
	if(! $get_o ) return;

	if( ! $field ) {
		return $get_o;
	}
	if( isset( $get_o[$field] ) )  return $get_o[$field]; 
}

// inline custom style for menu icons
 function cadreu_NavMenus_custom_css() {

	foreach( get_registered_nav_menus() as $location => $value ) {
		// get each menu settings
		$menu_setting = get_option( $this->_NAME().$location );
		if( ! $menu_setting ) return;
		// if custom style is even set for any menu
		if( array_key_exists('img_icons_custom_css', $menu_setting ) || array_key_exists('font_icons_custom_css', $menu_setting ) ) 
		{ // send inline style tags to header
		$css = '<!-- Custom Menu Styles --><style type = "text/css">';
		foreach ( $menu_setting as $key => $style ) {
			// $key matches the class given to an icon in case it has custom style at this menu
			if( $style ) 
			$css .= 'a.'.$location.' .'.$key.
			'{'.$style.'}';
		}
			$css .= '</style>';
			echo $css;
		}
	}
} 

	} // end class
} // end if class exists

$plugin_name = str_replace('-', '_',  basename(dirname(dirname(__FILE__)) ) );
$fileName = basename(dirname(dirname(__FILE__)) );
$$plugin_name = new cadreuPlugin_Options();
$iconic_navigation->file = dirname(dirname(__FILE__)).'/'.$fileName.'.php';
//$old = dirname(dirname(__FILE__)).substr( dirname(dirname(__FILE__)), strripos(dirname(dirname(__FILE__)), '/') ) .'.php';
//var_dump($old, $fileName, $iconic_navigation->file );
//'iconic-navigation';
//dirname(dirname(__FILE__)).substr( dirname(dirname(__FILE__)), strripos(dirname(dirname(__FILE__)), '/') ) .'.php';

$iconic_navigation->actions();
$iconic_navigation->add_options();
//$iconic_navigation->cap = 'edit_posts'; // for demo site to let contributors to options. or change for any admin cap: 'manage_options', 'activate_plugins'..
?>