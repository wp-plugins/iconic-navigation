jQuery(document).ready(function($) {
 	$('input[name="upload_save"], .browse').html('').hide();
	if( $('.icon_thumb').html() == false ) { 
	// no img found in img holder, hide delete butt
	$('.icon_thumb').siblings('.delete_icon').hide();
	}

		var file_frame;
		$(document).on( 'click', '.js_upload_butt', function( event ){
			event.preventDefault();
			
			butt = $(this);
			if( ! butt.attr('id') ) {
					input = 'input[name="img_url"]';
				} else {
					input = 'input[name="img_url['+ butt.attr('id')+']"]';
				}

			chooseNewImg = $(this).val().replace("Upload","Change");
			// Create the media frame.
			file_frame = wp.media.frames.downloadable_file = wp.media({
				title: $(this).data( 'frame_title' ),
				button: {
					text: $(this).data( 'frame_button' )
					},
				multiple: false
			});

			// When an image is selected, run a callback.
			file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();

			$(input).val( attachment.url );
				// insert thumb and delete butt
			$(input).siblings('.icon_thumb').html('<img src="' + attachment.url + '" width="50px" height="50px" /><input type="submit" class="delete_icon butt button" name= "delete_icon" id="' + butt.attr('id') + '" value="Delete icon"/>');
			butt.val( chooseNewImg ); // change upload button value
			});
			// Finally, open the modal.
			file_frame.open();
		});

		$(document).on( 'click', '.delete_icon', function( event ){
			event.preventDefault();

			if( ! $(this).attr('id') ) {
					input = 'input[name="img_url"]';
				} else {
					input = 'input[name="img_url['+ $(this).attr('id')+']"]';
				}
			$(this).parent('.icon_thumb').html('<b class="comment alert">Do not forget to Save Menu when you are finished</b>');
			$(input).val('');
			$(input).siblings('.delete_icon').hide();
			$(input).siblings('.js_upload_butt').val('Upload icon')
			return false;
		});

		
// font icons handlers 
		$('.close_up, .font_icon_use').click(function() {
		$(this).parents('.sucs_mess, span.screen_shade').fadeOut("fast");
		});
		$('.remove_font_icon_js').click(function(){
			$('input[name="font_ic['+ $(this).attr('name')+']"]').val(''); // empty icon val
			$('em[name="' + $(this).attr('name') + '"]' ).attr('class', ''); // remove icon holder class to hide icon
			$(this).hide(); // hide remove link
		});

// open font icons screen to chose an icon for a menu item
		$('.font_icons').click(function() {
		$(this).siblings('.screen_shade').fadeIn('fast');

		$('span.icon_cell em').click(function() { // span attr name would be the menu item class
			$(this).css('border-color', '#ccc');
			ic = $(this).attr('class'); // icon class
			input = 'input[name="font_ic['+ $(this).parent('span').attr('name')+']"]'; // find hidden input by menu item id (input name='font_ic[menu item id]' )

			$(input).val(ic); // pass icon class to the input
			
			$(this).parent('span').siblings('span').children('em').css('border-color', $(this).css('background')); // remove highlighting border from siblings on chosing another icon

			$('em[name="' + $(this).parent('span').attr('name') + '"]' ).attr('class', ic); // find em by the item id and set its class to match the chosen icon 
			$(input).siblings('.remove_font_icon_js').show();
			$('.font_icon_use').click(function(event){
				event.preventDefault();
			});
		});
	}); // end icons

});