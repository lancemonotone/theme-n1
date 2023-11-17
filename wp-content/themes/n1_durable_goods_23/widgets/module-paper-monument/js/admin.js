(function ($) {
	"use strict";
	$(function () {
		$('.custom_media_upload').media_upload();
	});

	$.fn.media_upload = function(){
		return this.each(function(){
			var $button = $(this);
			var $del = $button.siblings('.custom_media_delete');
			var $img = $button.siblings('.custom_media_img');
			var $src = $button.siblings('.custom_media_src');
			
			$button.click(function(e) {
				e.preventDefault();
				
				var send_attachment_bkp = wp.media.editor.send.attachment;
		
				wp.media.editor.send.attachment = function(props, attachment) {
					$img.attr('src', attachment.url);
					$src.attr('value', attachment.url);
					$del.show();
					$button.html('Update');
					wp.media.editor.send.attachment = send_attachment_bkp;
				}
		
				wp.media.editor.open($button);
			});
				
			$del.click(function(e){
				e.preventDefault();
				$img.attr('src', '');
				$src.val('');
				$del.hide();
				$button.html('Upload');
			});
		});
	}
}(jQuery));