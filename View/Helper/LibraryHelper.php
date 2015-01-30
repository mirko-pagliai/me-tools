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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\View\Helper
 */

App::uses('AppHelper', 'View/Helper');
App::uses('Plugin', 'MeTools.Utility');

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
     * @param array $options Options for the datepicker
	 * @return string jQuery code
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::css()
	 * @uses MeHtmlHelper::js()
	 */
	private function _datetimepicker($input, $options = array()) {
		$this->Html->js(array(
			'/MeTools/js/moment-with-locales.min',
			'/MeTools/js/bootstrap-datetimepicker.min'
		), array('block' => 'script_bottom'));
		
        $this->Html->css('/MeTools/css/bootstrap-datetimepicker.min', array('block' => 'css_bottom'));
				
		$options = $this->Html->_addOptionDefault('icons', array(
			'time' => 'fa fa-clock-o',
			'date' => 'fa fa-calendar',
			'up' => 'fa fa-arrow-up',
			'down' => 'fa fa-arrow-down',
			'previous' => 'fa fa-arrow-left',
			'next' => 'fa fa-arrow-right',
			'today' => 'fa fa-dot-circle-o',
			'clear' => 'fa fa-trash'
		), $options);
		
		$locale = Configure::read('Config.language');
		
		if(empty($options['locale']) && !empty($locale))
			$options = $this->Html->_addOptionDefault('locale', $locale, $options);
		
		return sprintf('$("%s").datetimepicker(%s);', $input, json_encode($options));
	}
	
    /**
     * Before layout callback. beforeLayout is called before the layout is rendered.
     * @param string $layoutFile The layout about to be rendered
	 * @uses output
	 * @uses MeHtmlHelper::scriptBlock()
     */
    public function beforeLayout($layoutFile) {
        //Writes the output
        if(!empty($this->output)) {
            $this->output = array_map(function($v) {
                return "\t".$v.PHP_EOL;
            }, $this->output);
			
			$this->Html->scriptBlock(sprintf('$(function() {%s});', PHP_EOL.implode('', $this->output)));
        }
    }
	
	/**
	 * Create a script block for Google Analytics.
	 * @param string $id Analytics ID
     * @param array $options Options
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::scriptBlock()
	 */
	public function analytics($id = FALSE, $options = array()) {
		if(empty($id))
			return NULL;
		
		$options = $this->Html->_addOptionDefault('block', 'script_bottom', $options);
		
		$this->Html->scriptBlock(sprintf('!function(e,a,t,n,c,o,s){e.GoogleAnalyticsObject=c,e[c]=e[c]||function(){(e[c].q=e[c].q||[]).push(arguments)},e[c].l=1*new Date,o=a.createElement(t),s=a.getElementsByTagName(t)[0],o.async=1,o.src=n,s.parentNode.insertBefore(o,s)}(window,document,"script","//www.google-analytics.com/analytics.js","ga"),ga("create","%s","auto"),ga("send","pageview");', $id), $options);
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
     * @return mixed String of <script /> tags
     * @see MeFormHelper::ckeditor()
     * @see http://docs.cksource.com CKEditor documentation
	 * @uses MeHtmlHelper::js()
	 * @uses Plugin::getPath()
     */
    public function ckeditor($jquery = TRUE) {
        //Checks for CKEditor into APP/webroot/ckeditor/
        if(is_readable(WWW_ROOT.'ckeditor'.DS.'ckeditor.js')) {
            $path = WWW_ROOT.'ckeditor';
			$url = '/ckeditor';
		}
        //Else, checks for CKEditor into APP/webroot/js/ckeditor/
        elseif(is_readable(WWW_ROOT.'js'.DS.'ckeditor'.DS.'ckeditor.js')) {
            $path = WWW_ROOT.'js'.DS.'ckeditor';
            $url = '/js/ckeditor';
        }
		
		//Checks for CKEditor scripts
		if(empty($path) || empty($url))
			return FALSE;

		$scripts = array($url.'/ckeditor');

		//Checks for the jQuery adapter
		if($jquery && is_readable($path.DS.'adapters'.DS.'jquery.js'))
			$scripts[] = $url.'/adapters/jquery';

		//Checks for the init script into APP/webroot/js/
		if(is_readable(WWW_ROOT.'js'.DS.'ckeditor_init.js'))
			$scripts[] = 'ckeditor_init';
		//Else, checks for the init script into APP/Plugin/MeTools/webroot/ckeditor/
		elseif(is_readable(Plugin::getPath('MeTools').'webroot'.DS.'ckeditor'.DS.'ckeditor_init.js'))
			$scripts[] = '/MeTools/ckeditor/ckeditor_init';
		else
			return FALSE;

		return $this->Html->js($scripts, array('block' => 'script_bottom'));
    }
	
	/**
     * Loads all FancyBox scripts.
	 * 
	 * FancyBox must be located into `APP/webroot/fancybox`.
     * @return mixed String of <script /> tags
     * @see http://fancyapps.com/fancybox/#docs FancyBox documentation
	 * @uses MeHtmlHelper::css()
	 * @uses MeHtmlHelper::js()
	 * @uses Plugin::getPath()
	 */
	public function fancybox() {
        //Checks for FancyBox into APP/webroot/fancybox
        if(!is_readable(WWW_ROOT.'fancybox'.DS.'jquery.fancybox.pack.js'))
			return FALSE;
		
		$this->Html->css(array(
			'/fancybox/jquery.fancybox',
			'/fancybox/helpers/jquery.fancybox-buttons',
			'/fancybox/helpers/jquery.fancybox-thumbs',
		), array('block' => 'css_bottom'));
		
		$scripts = array(
			'/fancybox/jquery.fancybox.pack',
			'/fancybox/helpers/jquery.fancybox-buttons',
			'/fancybox/helpers/jquery.fancybox-thumbs'
		);
		
		//Checks for the init script into APP/webroot/js/
		if(is_readable(WWW_ROOT.'js'.DS.'fancybox_init.js'))
			$scripts[] = 'fancybox_init';
		//Else, checks for the init script into APP/Plugin/MeTools/webroot/fancybox/
		elseif(is_readable(Plugin::getPath('MeTools').'webroot'.DS.'fancybox'.DS.'fancybox_init.js'))
			$scripts[] = '/MeTools/fancybox/fancybox_init';
		else
			return FALSE;
		
		return $this->Html->js($scripts, array('block' => 'script_bottom'));
	}

    /**
     * Adds a datepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `datepicker()` method provided by the `MeFormHelper`.
     * @param string $input Target field. Default is `.datepicker`
     * @param array $options Options for the datepicker
     * @see MeFormHelper::datepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses output
	 * @uses _datetimepicker()
	 * @uses MeHtmlHelper::_addOptionDefault()
     */
	public function datepicker($input = NULL, $options = array()) {
		$input = empty($input) ? '.datepicker' : $input;
		
		$options = $this->Html->_addOptionDefault('pickTime', FALSE, $options);
		
        $this->output[] = self::_datetimepicker($input, $options);
	}
	
	 /**
     * Adds a datetimepicker to the `$input` field.
     * 
     * To create an input field compatible with datetimepicker, you should use the `datetimepicker()` method provided by the `MeFormHelper`.
     * @param string $input Target field. Default is `.datetimepicker`
     * @param array $options Options for the datetimepicker
     * @see MeFormHelper::datetimepicker()
     * @see http://eonasdan.github.io/bootstrap-datetimepicker Bootstrap 3 Datepicker v4 documentation
	 * @uses output
	 * @uses _datetimepicker()
     */
	public function datetimepicker($input = NULL, $options = array()) {
		$input = empty($input) ? '.datetimepicker' : $input;
		
        $this->output[] = self::_datetimepicker($input, $options);
	}

    /**
     * Through `slugify.js`, it provides the slug of a field. 
     * 
     * It reads the value of the `$sourceField` field and it sets its slug in the `$targetField`.
     * @param string $sourceField Source field
     * @param string $targetField Target field
	 * @uses output
	 * @uses MeHtmlHelper::js()
     */
    public function slugify($sourceField = 'form #title', $targetField = 'form #slug') {
        $this->Html->js('/MeTools/js/slugify.min', array('block' => 'script_bottom'));
		
        $this->output[] = sprintf('$().slugify("%s", "%s");', $sourceField, $targetField);
    }
	
    /**
     * Adds a timepicker to the `$input` field.
     * 
     * To create an input field compatible with datepicker, you should use the `timepicker()` method provided by the `MeFormHelper`.
     * @param string $input Target field. Default is `.timepicker`
     * @param array $options Options for the timepicker
     * @see MeFormHelper::timepicker()
     * @see https://github.com/Eonasdan/bootstrap-datetimepicker Bootstrap v3 datetimepicker widget documentation
	 * @uses output
	 * @uses _datetimepicker()
	 * @uses MeHtmlHelper::_addOptionDefault()
     */
	public function timepicker($input = NULL, $options = array()) {
		$input = empty($input) ? '.timepicker' : $input;
		
		$options = $this->Html->_addOptionDefault('pickTime', FALSE, $options);
		
		$this->output[] = self::_datetimepicker($input, $options);
	}
}