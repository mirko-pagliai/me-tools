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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://getbootstrap.com/components/#dropdowns Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\View\Helper;
use Cake\View\View;
use MeTools\Utility\MePlugin as Plugin;

/**
 * Library helper
 */
class LibraryHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.MeHtml']];
	
    /**
     * It will contain the output code
     * @var array 
     */
    protected $output = [];
	
	/**
	 * Internal function to generate datepicker and timepicker.
     * @param string $input Target field
     * @param array $options Options for the datepicker
	 * @return string jQuery code
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addDefault()
	 * @uses MeTools\View\Helper\MeHtmlHelper::css()
	 * @uses MeTools\View\Helper\MeHtmlHelper::js()
	 */
	protected function _datetimepicker($input, array $options = []) {
		$this->Html->js([
			'MeTools.moment-with-locales.min',
			'MeTools.bootstrap-datetimepicker.min'
		], ['block' => 'script_bottom']);
		
        $this->Html->css('MeTools.bootstrap-datetimepicker.min', ['block' => 'css_bottom']);
		
		//Shows the "Clear" button in the icon toolbar
		$options = $this->Html->_addDefault('showClear', TRUE, $options);
		
		$options = $this->Html->_addDefault('icons', [
			'time' => 'fa fa-clock-o',
			'date' => 'fa fa-calendar',
			'up' => 'fa fa-arrow-up',
			'down' => 'fa fa-arrow-down',
			'previous' => 'fa fa-arrow-left',
			'next' => 'fa fa-arrow-right',
			'today' => 'fa fa-dot-circle-o',
			'clear' => 'fa fa-trash'
		], $options);
		
		//TO-DO: fix
//		$locale = Configure::read('Config.language');
//		
//		if(empty($options['locale']) && !empty($locale))
//			$options = $this->Html->_addDefault('locale', $locale, $options);
		//Shows the "Clear" button in the icon toolbar
		$options = $this->Html->_addDefault('locale', 'en-gb', $options);
		
		return sprintf('$("%s").datetimepicker(%s);', $input, json_encode($options));
	}

    /**
     * Before layout callback. beforeLayout is called before the layout is rendered.
	 * @param Event $event An Event instance
     * @param string $layoutFile The layout about to be rendered
	 * @uses MeTools\View\Helper\MeHtmlHelper::scriptBlock()
	 * @uses output
     */
    public function beforeLayout(Event $event, $layoutFile) {
        //Writes the output
        if(!empty($this->output)) {
            $this->output = implode(PHP_EOL, array_map(function($v){ 
				return "\t".$v;
			}, $this->output));
			
			$this->Html->scriptBlock(sprintf('$(function() {%s});', PHP_EOL.$this->output.PHP_EOL), ['block' => 'script_bottom']);
        
			//Resets the output
			$this->output = [];
		}
    }
	
	/**
     * Loads all CKEditor scripts.
     * 
     * To know how to install and configure CKEditor, please refer to the `README.md` file.
	 * 
	 * CKEditor must be located into `APP/webroot/ckeditor` or `APP/webroot/js/ckeditor`.
     * 
     * To create an input field for CKEditor, you should use the `ckeditor()` method provided by the `MeFormHelper`.
     * @param bool $jquery FALSE if you don't want to use the jQuery adapter
     * @return bool
     * @see MeTools\View\Helper\MeFormHelper::ckeditor()
     * @see http://docs.cksource.com CKEditor documentation
	 * @uses MeTools\View\Helper\MeHtmlHelper::js()
	 * @uses MeTools\Utility\Plugin::getPath()
     */
    public function ckeditor($jquery = TRUE) {
        //Checks for CKEditor into `APP/webroot/ckeditor/`
        if(is_readable(WWW_ROOT.'ckeditor'.DS.'ckeditor.js')) {
            $path = WWW_ROOT.'ckeditor';
			$url = '/ckeditor';
		}
        //Else, checks for CKEditor into `APP/webroot/js/ckeditor/`
        elseif(is_readable(WWW_ROOT.'js'.DS.'ckeditor'.DS.'ckeditor.js')) {
            $path = WWW_ROOT.'js'.DS.'ckeditor';
            $url = '/js/ckeditor';
        }
		else
			return FALSE;

		$scripts = [$url.'/ckeditor'];

		//Checks for the jQuery adapter
		if($jquery && is_readable($path.DS.'adapters'.DS.'jquery.js'))
			$scripts[] = $url.'/adapters/jquery';

		//Checks for the init script into `APP/webroot/js/`
		if(is_readable(WWW_ROOT.'js'.DS.'ckeditor_init.js'))
			$scripts[] = 'ckeditor_init';
		//Else, checks for the init script into `APP/Plugin/MeTools/webroot/ckeditor/`
		elseif(is_readable(Plugin::getPath('MeTools').'webroot'.DS.'ckeditor'.DS.'ckeditor_init.js'))
			$scripts[] = '/MeTools/ckeditor/ckeditor_init';
		else
			return FALSE;

		$this->Html->js($scripts, ['block' => 'script_bottom']);
		
		return TRUE;
    }

    /**
     * Adds a datepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `datepicker()` method provided by the `MeFormHelper`.
     * @param string $input Target field. Default is `.datepicker`
     * @param array $options Options for the datepicker
     * @see MeTools\View\Helper\MeFormHelper::datepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addDefault()
	 * @uses output
	 * @uses _datetimepicker()
     */
	public function datepicker($input = NULL, array $options = []) {
		$input = empty($input) ? '.datepicker' : $input;
		
		$options = $this->Html->_addDefault('format', 'YYYY/MM/DD', $options);
		
        $this->output[] = self::_datetimepicker($input, $options);
	}
	
	 /**
     * Adds a datetimepicker to the `$input` field.
     * 
     * To create an input field compatible with datetimepicker, you should use the `datetimepicker()` method provided by the `MeFormHelper`.
     * @param string $input Target field. Default is `.datetimepicker`
     * @param array $options Options for the datetimepicker
     * @see MeTools\View\Helper\MeFormHelper::datetimepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses output
	 * @uses _datetimepicker()
     */
	public function datetimepicker($input = NULL, array $options = []) {
		$input = empty($input) ? '.datetimepicker' : $input;
		
        $this->output[] = self::_datetimepicker($input, $options);
	}
	
    /**
     * Through `slugify.js`, it provides the slug of a field. 
     * 
     * It reads the value of the `$sourceField` field and it sets its slug in the `$targetField`.
     * @param string $sourceField Source field
     * @param string $targetField Target field
	 * @uses MeTools\View\Helper\MeHtmlHelper::js()
	 * @uses output
     */
    public function slugify($sourceField = 'form #title', $targetField = 'form #slug') {
        $this->Html->js('MeTools.slugify.min', ['block' => 'script_bottom']);
		
        $this->output[] = sprintf('$().slugify("%s", "%s");', $sourceField, $targetField);
    }
	
    /**
     * Adds a timepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `timepicker()` method provided by the `MeFormHelper`.
     * @param string $input Target field. Default is `.timepicker`
     * @param array $options Options for the timepicker
     * @see MeTools\View\Helper\MeFormHelper::timepicker()
     * @see https://github.com/Eonasdan/bootstrap-datetimepicker Bootstrap v3 datetimepicker widget documentation
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addDefault()
	 * @uses output
	 * @uses _datetimepicker()
     */
	public function timepicker($input = NULL, array $options = []) {
		$input = empty($input) ? '.timepicker' : $input;
		
		$options = $this->Html->_addDefault('pickTime', FALSE, $options);
		
		$this->output[] = self::_datetimepicker($input, $options);
	}
}