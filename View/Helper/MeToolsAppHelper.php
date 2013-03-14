<?php

/**
 * Application level view helper.
 *
 * This file is application-wide helper file.
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		AGPL License (http://www.gnu.org/licenses/agpl.txt)
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools.View.Helper
 */
class MeToolsAppHelper extends AppHelper {
	/**
	 * Clean the value of an html attribute, removing blank spaces and duplicates
	 *
	 * For example, the string (and attribute value):
	 * <code>
	 * a a b  b c d e e e
	 * </code>
	 * will become:
	 * <code>
	 * a b c d e
	 * </code>
	 * @param string $value attribute value
	 * @return string cleaned value
	 */
	protected function _cleanAttribute($value) {
		//Trim and remove blank spaces
		$value = preg_replace('/\s+/', ' ', trim($value));
		//Remove duplicates
		$value = implode(' ', array_unique(explode(' ', $value)));

		return $value;
	}
}