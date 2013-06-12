<?php
App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'MeTools.Markdown/Markdown');
use \Michelf\Markdown;

/**
 * Convert the Markdown syntax to HTML.
 *
 * Look at {@link http://michelf.ca/projects/php-markdown PHP Markdown link} and {@link http://daringfireball.net/projects/markdown/syntax Markdown syntax}.
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools.View.Helper
 */
class MarkdownHelper extends AppHelper {
	/**
	 * Convert a string with the Markdown syntax to HTML
	 * @param string $string String with the Markdown syntax
	 * @return string Html
	 */
	function toHtml($string) {
		return Markdown::defaultTransform($string);
	}
}