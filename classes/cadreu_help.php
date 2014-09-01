<?php 
/*
* Plugin help class
*/

if ( ! defined( 'ABSPATH' ) ) exit; // shoo on direct access
if ( ! class_exists( 'cadreuHelp' ) ) {


class cadreuHelp {

// returns either Human Readable String or script_usable_string
function UseName( $name, $for = 'system' ) {
		if( $for == 'system' ) {
			return strtolower(str_replace(' ', '_', $name ));
		} else {
			return ucwords(str_replace(array('_', '-'), ' ', $name));
		}
}

function issetField( $post, $index ) {
	    return isset($post[$index]) ? $post[$index] : null;
	}


function tags( $tag, $class=null ) {
	$tags = array();
		if(isset( $class ) ) 
			$class = ' class="'.$class.'"';
		$tags['open'] = '<'.$tag.$class. '>';
		$tags['close'] = '</'.$tag.'>';
	return $tags;
}

/**
* @return upload field
*/
function uploadForm( $file = 'image', $item_id='' ) { 
	$max_size=  wp_max_upload_size(); // byte
	$input = '<span class="browse">'.
'<input type="file" name="upload" value=""/>'.
'<input type="submit" name="save_upload" formaction="" class="upload_image_button butt button"  value="Upload '.$file.'"/></span>';
	$input .= '<i class="comment">Please be advised that max upload size at your site is '. $max_size/ 1024 . 'kb</i>'; // the default upload will be hiden if js, showing the js_upload_butt instead to triger the wp uploader frame. using data attrs to send the uploader texts to wp.media.frames
	return $input;
}


function cadreu_call_WP_Uploader( $file = 'image', $item_id=''  ) {
	$max_size=  wp_max_upload_size(); // byte
	$input = '';
	$input .= '<input type="submit" class="js_upload_butt butt button" id="'.$item_id.'" data-frame_title="Choose '.$file.'" data-frame_button="Use '.$file.'" value="Upload '.$file.'" />'.
'<i class="comment">Please be advised that max upload size at your site is '. $max_size/ 1024 . 'kb</i>';
	return $input;
} 

function moveUploadedFile( ) {
		if( !$_FILES || $_FILES && $_FILES['upload']["size"] ==0 ) return;  
		if ( ! function_exists( 'wp_handle_upload' ) ) require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$uploadedfile = $_FILES['upload'];
		if(!is_uploaded_file($uploadedfile["tmp_name"]) && $uploadedfile["size"]!==0) { die ('Not cool');  
		} else  {
		if( $uploadedfile['error']!== 0 && $uploadedfile["size"]!==0 ) {
			switch( $uploadedfile['error'] ) {    
				case 1:
				case 2: $wehave= 'The file may have been too large. It must be smaller than '.$max_allowed;
					break;
				case 3:
				case 4:
				case 7: $wehave='';
					break;
				case 8: $wehave='The file was not in .jpg, .png or .gif format. ';
					break;
				case 6: $wehave='Could not access the upload folder. Contact admin. ';
				break; }
			return  '<div class="sucs_mess fail">'.$wehave.'Please try again or e-mail the file to <a target="_blank" href="mailto:'.get_bloginfo('admin_email').'?Subject=Upload help">'.get_bloginfo('admin_email').'</a><b class="close_up">x</b></div>';  
			} else {
			$upload_overrides = array( 'test_form' => false );
			
			$movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
			return $movefile;
			}
		}
}

	} // end class
} // end if class exists

?>