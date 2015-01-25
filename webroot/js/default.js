/*!
 * This file is part of MeTools.
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 */

/**
 * Function to auto-submit a form.
 * 
 * Example:
 * <select onchange="send_form(this)"></select>
 */
function send_form(element) {
	$(element).closest('form').submit();
}

/**
 * Extended disable function.
 * 
 * Example:
 * $(this).disable(true);
 * @see http://stackoverflow.com/a/16788240/1480263
 */
jQuery.fn.extend({
	disable: function(state) {
		return this.each(function() {			
			if($(this).is('input, button'))
				$(this).prop('disabled', state);
			else
				$(this).toggleClass('disabled', state);
		});
	}
});

/**
 * Closes a flash messages with an animation
 */
function close_flashMessage() {
	$('.alert').animate({opacity: '0'}, '800', function() { 
		$(this).slideUp('200');
	});
}

$(function() {
	//Enables the tooltips, if there are elements that require them
	if($('[data-toggle~="tooltip"]').length)
		$('[data-toggle~="tooltip"]').tooltip();
	
	/**
	 * When you click on "check/uncheck all", it changes the checkboxed state
	 */
	$('.checkboxes-list .checkAll, .checkboxes-list .uncheckAll').click(function(event) {
		event.preventDefault();
		
		var checkboxes = $(this).parents('.checkboxes-list').find('input[type="checkbox"]');
		
		//If you have clicked on "check all"
		if($(this).hasClass('checkAll'))
			checkboxes.prop('checked', true);
		//Else, if you have clicked on "uncheck all"
		else
			checkboxes.prop('checked', false);
	});
	
	/**
	 * When you click on the title of an hidden tip, it shows the text
	 */
	$('.tip.tip-hidden .tip-title').click(function() {
		$(this).css('cursor', 'default');
		$(this).next('.tip-text').slideDown('slow');
	});
	
	/**
	 * Closes a flash message when clicking the close button
	 */
	$('.alert .close').click(function() {
		close_flashMessage();
		//It requires "return false" to prevent the default behavior of jQuery
		return false;
	});
	
	/**
	 * Submits button will be disabled when the form is submitted.
	 */
	$('form').submit(function() {
		$(':submit', this).disable(true);
	});
	
	/**
	 * Closes automatically the flash messages after a preset time
	 */
	setTimeout(close_flashMessage, 3500);
});