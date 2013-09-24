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
	 * It will contain the output code
	 * @var array 
	 */
	private $output = array();
	
	/**
	 * Before layout callback. beforeLayout is called before the layout is rendered.
	 * @param string $layoutFile The layout about to be rendered.
	 * @return void
	 */
	public function beforeLayout($layoutFile) {
		//Write the output
		if(!empty($this->output)) {
			$this->output = array_map(function($v) { return "\t".$v.PHP_EOL; }, $this->output);
			$this->Html->scriptBlock("$(function() {".PHP_EOL.implode('', $this->output)."});");
		}
	}
	
	/**
	 * Adds a datepicker to the `$input` field.
	 * 
	 * To create an input field compatible with datepicker, you should use the method `datepicker` of the <i>Form</i> helper.
	 * 
	 * To know the options to use with datepicker, please refer to the
	 * {@link http://bootstrap-datepicker.readthedocs.org Bootstrap Datepicker documentation}.
	 * @param string $input Target field
	 * @param array $options Options for datepicker
	 */
	public function datepicker($input='.datepicker', $options=array()) {
		$this->Html->js('/MeTools/js/bootstrap-datepicker.min');
		$this->Html->css('/MeTools/css/datepicker.min');
		
		if(empty($options))
			$options = array(
				'autoclose'			=> true,
				'format'			=> 'yyyy-mm-dd',
				'todayBtn'			=> true,
				'todayHighlight'	=> true
			);
	
		//Switch for languange, reading from config
		switch(Configure::read('Config.language')) {
			case 'ita':
				$options['language'] = 'it';
				break;
		}
		
		$this->output[] = "$('{$input}').datepicker(".json_encode($options).");";
	}
	
	/**
	 * Through "slugify.js", it provides the slug of a field. 
	 * 
	 * It reads the value of the `$sourceField` field and it sets its slug in the `$targetField`.
	 * @param string $sourceField Source field
	 * @param string $targetField Target field
	 */
	public function slugify($sourceField='form #title', $targetField='form #slug') {
		$this->Html->js('/MeTools/js/slugify.min');
		$this->output[] = "$().slugify('{$sourceField}', '{$targetField}');";
	}
	
	/**
	 * Adds a timepicker to the `$input` field.
	 * 
	 * To create an input field compatible with timepicker, you should use the method `timepicker` of the <i>Form</i> helper.
	 * 
	 * To know the options to use with timepicker, please refer to the
	 * {@link http://jdewit.github.io/bootstrap-timepicker Bootstrap Timepicker documentation}.
	 * @param string $input Target field
	 * @param array $options Options for timepicker
	 */
	public function timepicker($input='.timepicker', $options=array()) {
		$this->Html->js('/MeTools/js/bootstrap-timepicker.min');
		$this->Html->css('/MeTools/css/bootstrap-timepicker.min');
		
		if(empty($options))
			$options = array(
				'disableFocus'	=> true,
				'minuteStep'	=> 1,
				'showMeridian'	=> false
			);
		
		$this->output[] = "$('{$input}').timepicker(".json_encode($options).");";
	}
}