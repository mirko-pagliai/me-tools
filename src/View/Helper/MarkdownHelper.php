<?php
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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://getbootstrap.com/components/#dropdowns Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;

require_once ROOT.DS.'vendor'.DS.'michelf'.DS.'php-markdown'.DS.'Michelf'.DS.'Markdown.inc.php';

/**
 * Markdown helper.
 * 
 * Converts from Markdown syntax to HTML.
 */
class MarkdownHelper extends Helper {
	/**
     * Converts a string from the Markdown syntax to HTML.
     * @param string $string Markdown syntax
	 * @param bool $clean TRUE if you want the output to be clean
     * @return string Html code
	 * @see http://michelf.ca/projects/php-markdown PHP Markdown
	 * @uses Michelf\Markdown::defaultTransform()
	 */
    function toHtml($string, $clean = FALSE) {
		//Converts some code blocks as used by some sites, such as Bitbucket
		$string = preg_replace_callback('/```\s+(#!\S+\s+)?(((?!```)\t*.*\s+)+)\s*```/m', function($match) {
			return PHP_EOL.preg_replace('/(\t*.*\s+)/m', '	$1', $match[2]);
		}, $string);

		$html = \Michelf\Markdown::defaultTransform($string);

		if($clean) {
			//Removes the "TOC"
			$html = preg_replace('/(<p>)?\[TOC\](<\/p>)?(\\n)*/', '', $html);

			//Changes headers
			$html = preg_replace('/<h[4-6]>(.*)<\/h[4-6]>/', '<h6>$1</h6>', $html);
			$html = preg_replace('/<h3>(.*)<\/h3>/', '<h5>$1</h5>', $html);
			$html = preg_replace('/<h2>(.*)<\/h2>/', '<h4>$1</h4>', $html);
			$html = preg_replace('/<h1>(.*)<\/h1>/', '<h3>$1</h3>', $html);
		}

		return $html;
	}
}