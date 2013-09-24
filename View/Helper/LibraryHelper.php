<?php
App::uses('AppHelper', 'View/Helper');

/**
 * This helper is for the use of some libraries, particularly JavaScript libraries.
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
class LibraryHelper extends AppHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
	
	/**
	 * Through "slugify.js", it provides the slug of a field. 
	 * 
	 * It reads the value of the `$sourceField` field and it sets its slug in the `$targetField`.
	 * @param string $sourceField Source field
	 * @param string $targetField Target field
	 */
	public function slugify($sourceField='form #title', $targetField='form #slug') {
		$this->Html->js('/MeTools/js/slugify.min');
		
		return $this->Html->scriptBlock("$(function() { $().slugify('{$sourceField}', '{$targetField}'); });");
	}
}