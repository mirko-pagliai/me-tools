/**
 * Slugify plugin
 *
 * It reads the value of a source field, then it generates and sets the slug in the target field
 * 
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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
(function($){
	$.fn.extend({
		slugify: function(source, target) {
			//It sets the default values, if necessary
			source = typeof source !== 'undefined' ? source : 'form #title';
			target = typeof target !== 'undefined' ? target : 'form #slug';

			/**
			 * Get a slug from a string
			 */
			function getSlug(str) {
				str = str.toLowerCase(); //Lowercase
				//Remove accents, swap Ã± for n, etc
				var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
				var to   = "aaaaeeeeiiiioooouuuunc------";
				for (var i=0, l=from.length ; i<l ; i++)
					str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
				return str.replace(/[^a-z0-9 -]/g, '')	//Remove invalid chars
				.replace(/^\s+|\s+$/g, '')			//Trim
				.replace(/\s+/g, '-')				//Collapse whitespace and replace by -
				.replace(/-+/g, '-');				//Collapse dashes
			}

			//When changing the source or the target (the slug field)
			$(source).add(target).change(function() {
				//If the target (the slug field) is empty and the source is set, it sets the target from the source
				if(!$(target).val().length && $(source).val().length)
					$(target).val(getSlug($(source).val()));
				//Else, if the target isn't empty, it sets the target from himself
				else
					$(target).val(getSlug($(target).val()));
			});
		}
	});
})(jQuery);