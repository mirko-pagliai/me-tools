<?php
App::uses('AppHelper', 'View/Helper');

/**
 * Creates form inputs to be used with TinyMCE.
 * 
 * You should use this helper as an alias, for example:
 * <pre>public $helpers = array('Wysiwyg' => array('className' => 'MeTools.TinyMce'));</pre>
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
class TinyMceHelper extends AppHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('Form' => array('className' => 'MeTools.MeForm'));
	
	/**
	 * Creates a textarea for TinyMce
	 * @param string $fieldName Field name. Should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function textarea($fieldName, $options=array()) {
		//Adds "wysiwyg tinymce" to the class
		$options['class'] = empty($options['class']) ? 'wysiwyg tinymce' : $options['class'].'wysiwyg tinymce';
		
		//Set the "require" attribute to FALSE, otherwise it will fail the field validation
		$options['required'] = false;
		
		$options['type'] = 'textarea';
		
		return $this->Form->input($fieldName, $options);
	}
}