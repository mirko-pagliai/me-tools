<?php
/**
 * MarkdownHelper
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
 * @package		MeTools\View\Helper
 * @see			http://michelf.ca/projects/php-markdown PHP Markdown
 */
App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'MeTools.Markdown/Markdown.inc');
use \Michelf\Markdown;

/**
 * Converts from Markdown syntax to HTML.
 */
class MarkdownHelper extends AppHelper {
	/**
	 * Alias for `toHtml()` method
	 */
	public function fromMarkdown() { 
		return call_user_func_array(array('MarkdownHelper', 'toHtml'), func_get_args());
	}
	
	/**
	 * Converts a string from the Markdown syntax to HTML
	 * @param string $string Markdown syntax
	 * @return string Html
	 */
	function toHtml($string) {
		return Markdown::defaultTransform($string);
	}
}