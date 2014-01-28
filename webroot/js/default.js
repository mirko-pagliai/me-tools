/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author	Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license	http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link	http://git.novatlantis.it Nova Atlantis Ltd
 */

/**
 * Closes a flash messages with an animation
 */
function close_flashMessage() {
	$('#flashMessage').animate({opacity: '0'}, '800', function(){ $(this).slideUp('200'); });
}

$(function() {	
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
	$('#flashMessage .close').click(function() {
		close_flashMessage();
		//It requires "return false" to prevent the default behavior of jQuery
		return false;
	});
	
	/**
	 * Closes automatically the flash messages after a preset time
	 */
	setTimeout(close_flashMessage, 3500);
});