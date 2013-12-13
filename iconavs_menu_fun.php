<? 
/*
* Nav Menus / Edit nav-menus funs 
*/

// adds an argument to wp_nav_menu() based on use_nav_icons setting
function iconnavs_custom_NavMenu_Args( $arg ) {
	global $iconic_navigation;
	if( in_array($arg['theme_location'], $iconic_navigation->getTheme_settings( 'mobile_options', 'use_nav_icons') ) )
	$arg['mobile_hide'] = true;
	return $arg;
}
// add this filter if we have use_nav_icons settings only
if( null !== $iconic_navigation->getTheme_settings( 'mobile_options', 'use_nav_icons') )
add_filter('wp_nav_menu_args', 'iconnavs_custom_NavMenu_Args', 9,1);



function iconnavs_showIcon( $item_output, $item, $depth, $args ) {
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		$class_names = $value = '';
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;
		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$output ='';
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
		$output .= $indent . '<li' . $id . $value . $class_names .'>';
		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
//  Adding Menu icons		
		global $iconic_navigation;
		// get img icon size settings for this menu location
		$icon_size = $iconic_navigation->getTheme_settings( $args->theme_location, 'icon_size') ?
		$iconic_navigation->getTheme_settings( $args->theme_location, 'icon_size') : '30px';

		// check for icons' custom style for this menu location
		$imgIcon_customStyle = '';
		$fontIcon_customStyle = '';
		$icon ='';
		$span = '';
		$span_close = '';
		if( $iconic_navigation->getTheme_settings( $args->theme_location, 'img_icons_custom_css') ) 
			$imgIcon_customStyle = ' img_icons_custom_css';
			
		if( $iconic_navigation->getTheme_settings( $args->theme_location, 'font_icons_custom_css'))
			$fontIcon_customStyle = ' font_icons_custom_css';

		// if icons are set to hide on smaller screens
		if( isset($args->mobile_hide) && $args->mobile_hide == true ) { // wrap title in span
			$span = '<span class="mobile_hide">';
			$span_close = '</span>';
		}
		// check what icon type to use and if an item has such icon
		if( $args->theme_location && has_nav_menu( $args->theme_location ) ) { // it's a menu assigned to a location, so check the location options
		if( $item->icon &&  $iconic_navigation->getTheme_settings( $args->theme_location, 'icon_type') !== 'font_ics' ) { // the item has an img icon and the location is not set to use font icons. let img icon be 
			$icon = '<img class="iconic_icon '.$imgIcon_customStyle .'" src="'.$item->icon .'" style="width:'.$icon_size.';height:'.$icon_size.'">'.$span;
		}
		if( $item->font_icon && $iconic_navigation->getTheme_settings( $args->theme_location, 'icon_type') !== 'img_ics' ) { // use font icons
			$icon = '<em class="'. $item->font_icon.$fontIcon_customStyle.'"></em>'.$span;
		}
		} else { // no location
			// it's a created menu
		if( $args->menu ) {
			if( $item->icon )
			$icon = '<img class="iconic_icon'.$imgIcon_customStyle .'" src="'.$item->icon .'" style="width:'.$icon_size.';height:'.$icon_size.'">'.$span;
			if( $item->font_icon )
			$icon = '<em class="'. $item->font_icon.$fontIcon_customStyle.'"></em>'.$span;
		}
		} // end no location else
		 
		$item_output = $args->before;
		$item_output .= '<a class="'.$args->theme_location.'" '. $attributes .'>'. $icon;
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		$item_output .= $span_close; // close span if mobile hide
		$item_output .= '</a>';
		$item_output .= $args->after;
	return  $item_output ;
} add_filter( 'walker_nav_menu_start_el', 'iconnavs_showIcon', 10,4 );




class iconNavs_Edit_Nav_Menu_Walker extends Walker_Nav_Menu {	
	function start_el(&$output, $item, $depth, $args) {
	global $_wp_nav_menu_max_depth, $help;
		$_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
		ob_start();
		$item_id = esc_attr( $item->ID );
		$removed_args = array(
			'action',
			'customlink-tab',
			'edit-menu-item',
			'menu-item',
			'page-tab',
			'_wpnonce',
		);
		$original_title = '';
		if ( 'taxonomy' == $item->type ) {
			$original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
			if ( is_wp_error( $original_title ) )
				$original_title = false;
		} elseif ( 'post_type' == $item->type ) {
			$original_object = get_post( $item->object_id );
			$original_title = $original_object->post_title;
		}
		$classes = array(
			'menu-item menu-item-depth-' . $depth,
			'menu-item-' . esc_attr( $item->object ),
			'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
		);
		$title = $item->title;
		if ( ! empty( $item->_invalid ) ) {
			$classes[] = 'menu-item-invalid';
			/* translators: %s: title of menu item which is invalid */
			$title = sprintf( __( '%s (Invalid)' ), $item->title );
		} elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
			$classes[] = 'pending';
			/* translators: %s: title of menu item in draft status */
			$title = sprintf( __('%s (Pending)'), $item->title );
		}
		$title = empty( $item->label ) ? $title : $item->label;
		?>
		<li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<span class="item-title"><?php echo esc_html( $title ); ?></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-up-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
								echo wp_nonce_url(
									add_query_arg(
										array(
											'action' => 'move-down-menu-item',
											'menu-item' => $item_id,
										),
										remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
									),
									'move-menu_item'
								);
							?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
							echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
						?>"><?php _e( 'Edit Menu Item' ); ?></a>
					</span>
				</dt>
			</dl>
			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">
				<?php if( 'custom' == $item->type ) : ?>
					<p class="field-url description description-wide">
						<label for="edit-menu-item-url-<?php echo $item_id; ?>">
							<?php _e( 'URL' ); ?><br />
							<input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
						</label>
					</p>
				<?php endif; ?>
				<p class="description description-thin">
					<label for="edit-menu-item-title-<?php echo $item_id; ?>">
						<?php _e( 'Navigation Label' ); ?><br />
						<input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
					</label>
				</p>
				<p class="description description-thin">
					<label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
						<?php _e( 'Title Attribute' ); ?><br />
						<input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
					</label>
				</p>
				<p class="field-link-target description">
					<label for="edit-menu-item-target-<?php echo $item_id; ?>">
						<input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
						<?php _e( 'Open link in a new window/tab' ); ?>
					</label>
				</p>
				<p class="field-css-classes description description-thin">
					<label for="edit-menu-item-classes-<?php echo $item_id; ?>">
						<?php _e( 'CSS Classes (optional)' ); ?><br />
						<input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
					</label>
				</p>
				<p class="field-xfn description description-thin">
					<label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
						<?php _e( 'Link Relationship (XFN)' ); ?><br />
						<input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
					</label>
				</p>
				<p class="field-description description description-wide">
					<label for="edit-menu-item-description-<?php echo $item_id; ?>">
						<?php _e( 'Description' ); ?><br />
						<textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
						<span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
					</label>
				</p>

<? // Add icon upload  ?>
<? global $iconic_navigation; 
include_once( "iconavs_icons.php" ); // font icons funs
?>
<p class="icon description description-wide">
	<label>Icon
<i class="comment">Icons style and display options for each menu location can be set up at <a href='<?=admin_url( 'options-general.php?page='.$iconic_navigation->_NAME() )?>'><?=$iconic_navigation->help->UseName( $iconic_navigation->_NAME(),1 )?> Settings</a></i>
	</label>
<? if( ! isset( $_REQUEST['edit-menu-item']) ) { // if js enabled ?>
	<span class="icon_thumb"><? // div for img insert right after upload | js/admin_mysripts.js 
	if( $item->icon  ) { // we have icon in db, show icon ?>
	<img class="item_icon" src="<?= $item->icon; ?>" width="50px" height="50px" />
	<input type="submit" class="delete_icon butt button" name="delete_icon" id="<?= $item_id ?>" value="Delete icon"/>
	<? } ?>
	</span>
	<? echo $iconic_navigation->help->cadreu_call_WP_Uploader('icon', $item_id ); // call uploader ?>
	<input type="hidden" id="icon_url" name="img_url[<?= $item_id ?>]" value="<?= $item->icon ?>" />

	<? // font icon choice popup link ?>
	<span class="font_icons">Chose from font icons</span>
	<span class="screen_shade">
	<span class="close_up">x</span>
		<span class="font_icon_popup">
		<? echo iconnavs_Font_Icons( $item_id ) ?>
		</span>
	</span>

	<? $font_ic_class = $item->font_icon ? ' class="'.$item->font_icon.'"' : ''; // $item font_icon meta is the icon's class. assign this class to the icon holder tag (<em>) to show the icon
	$show_remove_link = $item->font_icon ? ' display' : ''; // add display class for display:block the remove link when t'item has a font_icon
	?>
	<em name="<?= $item_id ?>" <?=$font_ic_class ?>></em>

	<span name="<?= $item_id ?>" class="remove_font_icon_js <?=$show_remove_link?>">Remove font icon</span>
	<input type="hidden" id="font_icon" name="font_ic[<?= $item_id ?>]" value="<?= $item->font_icon ?>" />
	 <?
}

	if( isset($_REQUEST['edit-menu-item']) && $_REQUEST['edit-menu-item'] == $item_id ) { // js disabled. render the upload at the active menu item only so we have one upload at a time. otherwise !is_uploaded_file from the inacitve items forse it to die on handling | slave_funs.php | moveUploadedFile() ?>

	<?  $icon_data = $iconic_navigation->help->moveUploadedFile(); // handle FILES, get the icon url | slave_funs.php
	// render icon thumb
	//$_POST['delete_icon'] = null; 
	if( null == $_POST['delete_icon'] ) {
		$icon_url = $icon_data ? $icon_data['url'] : $item->icon; // if the file is uploaded, use the new url, else get the old url from $item obj 
		if( $icon_url ) {
	  // show new icon & delete option right on upload, before we insert it to db | js-less
		echo '<span class="icon_thumb">';
		echo '<img class="item_icon" src="'.$icon_url .'" width="50px" height="50px" />';
		echo '<input type="submit" class="delete_icon butt button" formaction="" name="delete_icon" id="'.$item_id.'" value="Delete icon"/>';
		echo '</span>';
		}
	} else { // delete_icon isset
		$icon_url = ''; // pass the empty icon value to update_post_meta at insert_delete_nav_icon()
	} ?>
	<input type="hidden" id="icon_url" name="img_url[<?= $item_id ?>]" value="<?= $icon_url?>" />
	<? echo $iconic_navigation->help->uploadForm('icon', $item_id ); // do upload form ?>


	<? // font icon choice link | js-less ?>
	<a class="font_icons" href="<?php
					echo esc_url(
						add_query_arg(
							array(
								'chose_icon' => 'font'
							))							
					); ?>">Chose from font icons</a>

<? if( isset( $_REQUEST['selected_font_icon'] ) ) { // new icon is chosen suddenly
	$_REQUEST['delete_font_icon'] = ''; // forget deleting 
	$item->font_icon = ''; // forget old icon
	$font_ic_class = $_REQUEST['selected_font_icon']; // show new icon immidiately
	remove_query_arg('delete_font_icon'); // remove a possible delete_icon request ?>
<? } ?>
	
<? if( ! $_REQUEST['delete_font_icon'] ) { // no deleting, all is cool
	$font_ic_class = $item->font_icon ? $item->font_icon : $_REQUEST['selected_font_icon'] ; // show either old or new one ?>
	<em id="<?= $item_id ?>" class="<?=$font_ic_class ?>"></em>

	<? if( $item->font_icon || $_REQUEST['selected_font_icon'] ) { // we have icon, show remove link ?>
	<a class="remove_font_icon" href="<?php
					echo esc_url(
						add_query_arg(
							array(
								'delete_font_icon' => $item_id
							))							
					); ?>">Remove font icon</a>
					<? } // end remove link ?>
	<? } else { // delete request isset, no new icon chosen, pass empty icon value

	$font_ic_class = ''; 
	if(! isset($_REQUEST['chose_icon'] ) ) { ?>
	<b class="comment alert">Don't forget to Save Menu!</b>
	<? } // end if 
    } // end else ?>

	<? if( isset($_REQUEST['chose_icon'] ) && ! $_REQUEST['selected_font_icon'] ) { // link is clicked but icon is not chosen yet, show the font icons list ?>
		<span class="font_icon_list">
		<? echo iconnavs_Font_Icons_Jsless( $item_id ) ?>
		</span>
	<? } ?>
	<input type="hidden" id="font_icon" name="font_ic[<?= $item_id ?>]" value="<?= $font_ic_class ?>" />
	
<? } // finita icon upload   ?></p>

	            <div class="menu-item-actions description-wide submitbox">
					<?php if( 'custom' != $item->type && $original_title !== false ) : ?>
						<p class="link-to-original">
							<?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
						</p>
					<?php endif; ?>
					<a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
					echo wp_nonce_url(
						add_query_arg(
							array(
								'action' => 'delete-menu-item',
								'menu-item' => $item_id,
							),
							remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
						),
						'delete-menu_item_' . $item_id
					); ?>"><?php _e('Remove'); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-<?php echo $item_id; ?>" href="<?php	echo esc_url( add_query_arg( array('edit-menu-item' => $item_id, 'cancel' => time()), remove_query_arg( $removed_args, admin_url( 'nav-menus.php' ) ) ) );
						?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
				</div>

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$output .= ob_get_clean();
	    } 
}

function iconnavs_use_Custom_NavMenu($walker,$menu_id) {
	return 'iconNavs_Edit_Nav_Menu_Walker';
} add_filter( 'wp_edit_nav_menu_walker', 'iconnavs_use_Custom_NavMenu', 32, 10, 2 );


// adds icon value to nav menu item object
function iconnavs_item_meta( $item ) {
    $item->icon = get_post_meta( $item->ID, '_menu_icon', true );
    $item->font_icon = get_post_meta( $item->ID, '_menu_font_icon', true );
    return $item;
} add_filter( 'wp_setup_nav_menu_item','iconnavs_item_meta' );



// $_REQUEST.. here grabs the val of the icon src. The function updates _menu_icon meta column accordingly inserting the img url or inserting void ('')
function iconnavs_insert_delete_nav_icon( $menu_id, $item_id, $args ) {
    if (  isset($_REQUEST['img_url'][$item_id] ) )
    update_post_meta( $item_id, '_menu_icon', $_REQUEST['img_url'][$item_id] );
	if (  isset($_REQUEST['font_ic'][$item_id] ) )
    update_post_meta( $item_id, '_menu_font_icon', $_REQUEST['font_ic'][$item_id] );
// js-less 
	if (  isset($_POST['img_url'][$item_id] ) )
    update_post_meta( $item_id, '_menu_icon', $_POST['img_url'][$item_id] );
	if( isset($_POST['font_ic'][$item_id] ) )
	update_post_meta( $item_id, '_menu_font_icon', $_POST['font_ic'][$item_id] );	
} add_action('wp_update_nav_menu_item', 'iconnavs_insert_delete_nav_icon', 10,3 );
?>