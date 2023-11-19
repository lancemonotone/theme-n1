function popitup(url) {
	newwindow=window.open(url,'tw','height=400,width=500');
	if (window.focus) {newwindow.focus()}
	return false;
}

(function ($) {
	"use strict";
	$(function () {
		$('.subscribe-module .mm-form input[type=radio]').change(function(){
			$(this).parents('.mm-form').find('a.mm-form-submit').attr('href', $(this).attr('value'));
		});
	});
}(jQuery));
