<?php

/**
 * LibraryHelper
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
 * @package		MeTools\View\Helper
 */
App::uses('AppHelper', 'View/Helper');

/**
 * Allows to easily use some libraries, particularly JavaScript libraries.
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
	 * Internal function to generate datepicker and timepicker.
     * @param string $input Target field
     * @param array $options Options for datepicker
	 * @return string jQuery code
	 */
	private function _datetimepicker($input, $options = array()) {
		 $this->Html->js(array(
			'/MeTools/js/moment-with-locales.min',
			'/MeTools/js/bootstrap-datetimepicker.min'
		), array('inline' => FALSE));
        $this->Html->css('/MeTools/css/bootstrap-datetimepicker.min', array('inline' => FALSE));
		
        //Switch for languange, reading from config
		if(empty($options['language']))
			switch(Configure::read('Config.language')) {
				case 'ita':
					$options['language'] = 'it';
					break;
			}
		
		return "$('{$input}').datetimepicker(".json_encode($options).");";
	}
	
    /**
     * Before layout callback. beforeLayout is called before the layout is rendered.
     * @param string $layoutFile The layout about to be rendered
     * @return void
     * @see http://api.cakephp.org/2.5/class-Helper.html#_beforeLayout CakePHP Api
     */
    public function beforeLayout($layoutFile) {
        //Writes the output
        if(!empty($this->output)) {
            $this->output = array_map(function($v) {
                return "\t".$v.PHP_EOL;
            }, $this->output);
            $this->Html->scriptBlock("$(function() {".PHP_EOL.implode('', $this->output)."});");
        }
    }

    /**
     * Loads all CKEditor scripts.
     * 
     * To know how to install and configure CKEditor, please refer to the `README` file.
     * 
     * To create an input field compatible with CKEditor, you should use the `ckeditor()` method provided by `MeForm` helper.
     * @param bool $jquery FALSE if you don't want to use the jquery adapter
     * @return mixed String of <script /> tags
     * @see MeFormHelper::ckeditor()
     * @see http://docs.cksource.com CKEditor documentation
     */
    public function ckeditor($jquery = TRUE) {
        $url = '/ckeditor';

        //Checks if CKEditor script (ckeditor.js) exists
        //It seeks in app/webroot/ckeditor and app/webroot/js/ckeditor
        if(fileExistsInPath(WWW_ROOT.'ckeditor'.DS.'ckeditor.js'))
            $path = WWW_ROOT.'ckeditor';
        elseif(fileExistsInPath(WWW_ROOT.'js'.DS.'ckeditor'.DS.'ckeditor.js')) {
            $path = WWW_ROOT.'js'.DS.'ckeditor';
            $url = '/js'.$url;
        }

        //If CKEditor script exists
        if(!empty($path) && !empty($url)) {
            $script = array($url.'/ckeditor');

            //Checks if the jQuery adapter exists
            if($jquery && fileExistsInPath($path.DS.'adapters'.DS.'jquery.js'))
                $script[] = $url.'/adapters/jquery';

            //Checks if the init script exists
            //It seeks in app/webroot/js, app/webroot/ckeditor, app/webroot/js/ckeditor and app/Plugin/MeTools/webroot/ckeditor
            if(fileExistsInPath(WWW_ROOT.'js'.DS.'ckeditor_init.js'))
                $script[] = 'ckeditor_init';
            elseif(fileExistsInPath($path.DS.'ckeditor_init.js'))
                $script[] = $url.'/ckeditor_init';
            elseif(fileExistsInPath(App::pluginPath('MeTools').'webroot'.DS.'ckeditor'.DS.'ckeditor_init.js'))
                $script[] = '/MeTools/ckeditor/ckeditor_init';
            else
                return FALSE;

            return $this->Html->js($script, array('inline' => FALSE));
        }

        return FALSE;
    }

    /**
     * Adds a datepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `datepicker()` method provided by `MeForm` helper.
     * @param string $input Target field. Default is '.datepicker'
     * @param array $options Options for datepicker
     * @see MeFormHelper::datepicker()
     * @see https://github.com/Eonasdan/bootstrap-datetimepicker Bootstrap v3 datetimepicker widget documentation
	 * @uses _datetimepicker() to generate the timepicker
     */
	public function datepicker($input = NULL, $options = array()) {
		$input = empty($input) ? '.datepicker' : $input;
		
		//Merge options with defaults
		$options = am($options, array('pickTime' => FALSE));
		
        $this->output[] = $this->_datetimepicker($input, $options);
	}

    /**
     * Through "slugify.js", it provides the slug of a field. 
     * 
     * It reads the value of the `$sourceField` field and it sets its slug in the `$targetField`.
     * @param string $sourceField Source field
     * @param string $targetField Target field
     */
    public function slugify($sourceField = 'form #title', $targetField = 'form #slug') {
        $this->Html->js('/MeTools/js/slugify.min', array('inline' => FALSE));
        $this->output[] = "$().slugify('{$sourceField}', '{$targetField}');";
    }
	
    /**
     * Adds a timepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `timepicker()` method provided by `MeForm` helper.
     * @param string $input Target field. Default is '.timepicker'
     * @param array $options Options for timepicker
     * @see MeFormHelper::timepicker()
     * @see https://github.com/Eonasdan/bootstrap-datetimepicker Bootstrap v3 datetimepicker widget documentation
	 * @uses _datetimepicker() to generate the timepicker
     */
	public function timepicker($input = NULL, $options = array()) {
		$input = empty($input) ? '.timepicker' : $input;
		
		//Merge options with defaults
		$options = am($options, array('pickDate' => FALSE));
		
		$this->output[] = $this->_datetimepicker($input, $options);
	}
}