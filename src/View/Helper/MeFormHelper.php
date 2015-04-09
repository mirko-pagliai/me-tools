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
 * @see			http://api.cakephp.org/3.0/class-Cake.View.Helper.FormHelper.html FormHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\FormHelper;
use Cake\View\View;

/**
 * Provides functionalities for forms.
 * 
 * Rewrites {@link http://api.cakephp.org/3.0/class-Cake.View.Helper.FormHelper.html FormHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = ['Form' => ['className' => 'MeTools.MeForm']];
 * </code>
 */
class MeFormHelper extends FormHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.MeHtml'], 'Url'];
	
	/**
	 * Construct the widgets and binds the default context providers.
	 * 
	 * This method only ewrites the default configuration (`$_defaultConfig`).
	 * @param Cake\View\View $view The View this helper is being attached to
	 * @param array $config Configuration settings for the helper
	 */
	public function __construct(View $view, $config = []) {
        parent::__construct($view, $config);
		
		//Rewrites templates
		$this->templates([
			'file' => '<input type="file" class="form-control" name="{{name}}"{{attrs}}>',
			'input' => '<input type="{{type}}" class="form-control" name="{{name}}"{{attrs}}>',
			'inputContainer' => '<div class="input form-group {{type}}{{required}}">{{content}}{{tip}}</div>',
			'inputContainerError' => '<div class="input form-group {{type}}{{required}} has-error">{{content}}{{tip}}{{error}}</div>',
			'select' => '<select class="form-control" name="{{name}}"{{attrs}}>{{content}}</select>',
			'selectMultiple' => '<select class="form-control" name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
			'textarea' => '<textarea class="form-control" name="{{name}}"{{attrs}}>{{value}}</textarea>',
			'submitContainer' => '<div class="submit form-group">{{content}}</div>'
		]);
    }
	
	/**
	 * Generates an input element.
	 * This method is used only to provide the "tip" functionality.
	 * @param string $fieldName the field name
	 * @param array $options The options for the input element
	 * @return string The generated input element
	 * @see http://api.cakephp.org/3.0/class-Cake.View.Helper.FormHelper.html#__getInput
	 */
	protected function _getInput($fieldName, $options) {
		unset($options['tip']);
		
		return parent::_getInput($fieldName, $options);
	}
	
	/**
	 * Generates an input container template.
	 * This method is used only to provide the "tip" functionality.
	 * @param array $options The options for input container template
	 * @return string The generated input container template
	 * @see http://api.cakephp.org/3.0/class-Cake.View.Helper.FormHelper.html#__inputContainerTemplate
	 */
	protected function _inputContainerTemplate($options) {
         $inputContainerTemplate = $options['options']['type'] . 'Container' . $options['errorSuffix'];
         if(!$this->templater()->get($inputContainerTemplate))
             $inputContainerTemplate = 'inputContainer' . $options['errorSuffix'];
 
         return $this->templater()->format($inputContainerTemplate, [
             'content' => $options['content'],
             'error' => $options['error'],
             'required' => $options['options']['required'] ? ' required' : '',
			 'tip' => empty($options['options']['tip']) ? NULL : $options['options']['tip'],
             'type' => $options['options']['type']
         ]);
     }
	
	/**
     * Creates a button.
     * 
     * This method creates a button. To create a POST button, you should use the `postButton()` method.
     * Instead, to create a link with the appearance of a button, you should use the `button()` method provided by `MeHtmlHelper`.
     * @param string $title The button label or an image
     * @param array $options HTML attributes and options
     * @return string Html code
     * @see postButton(), MeHtmlHelper::button()
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addButtonClass()
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addDefault()
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addIcon()
	 */
	public function button($title, array $options = []) {
		$options = $this->Html->_addButtonClass($options);
		$options = $this->Html->_addDefault('type', 'button', $options);
		
		$title = $this->Html->_addIcon($title, $options);
        unset($options['icon']);
		
		return parent::button($title, $options);
	}
	
	/**
	 * Creates a checkbox input element.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
	 * @return string Html code
	 */
	public function checkbox($fieldName, array $options = []) {
		//Checkboxes inputs outside of the label
		$this->templates([
			'nestingLabel' => '{{input}}<label{{attrs}}>{{text}}</label>',
			'formGroup' => '{{input}}{{label}}'
		]);
		
		return parent::checkbox($fieldName, $options);
	}
	
	/**
	 * Generates an input element complete with label and wrapper div.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
	 * @return string Html code
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addDefault()
	 * @uses MeTools\View\Helper\MeHtmlHelper::span()
	 */
    public function input($fieldName, array $options = []) {
		//If the field name contains the word "password", then the field type is "password"
		if(preg_match('/password/', $fieldName))
			$options = $this->Html->_addDefault('type', 'password', $options);
		
		//$type = self::_inputType($fieldName, $options);
		
		//Changes the "autocomplete" value from "FALSE" to "off"
		if(isset($options['autocomplete']) && !$options['autocomplete'])
			$options['autocomplete'] = 'off';
		
		//Sets the default templates
		//These values can be overwritten by other methods
		$this->templates([
			'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
			'formGroup' => '{{label}}{{input}}'
		]);
		
		if(!empty($options['tip'])) {
			$options['tip'] = implode(PHP_EOL, array_map(function($v) {
				return $this->Html->span(trim($v), ['class' => 'help-block']);
			}, is_array($options['tip']) ? $options['tip'] : [$options['tip']]));
		}
		
        return parent::input($fieldName, $options);
	}
	
	/**
     * Returns a formatted `<label>` element. 
	 * Will automatically generate a `for` attribute if one is not provided.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param string $text Text that will appear in the label field. If is left undefined the text will be inflected from the fieldName
     * @param array|string $options HTML attributes, or a string to be used as a class name
	 * @return string Html code
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addDefault()
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addIcon()
	 */
	public function label($fieldName, $text = NULL, array $options = []) {
		$options = $this->Html->_addDefault('escape', FALSE, $options);

		$text = $this->Html->_addIcon($text, $options);
        unset($options['icon']);
		
		return parent::label($fieldName, $text, $options);
	}
	
	/**
	 * Creates a `<legend>` tag.
     * @param string $text Legend text
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @uses MeTools\View\Helper\MeHtmlHelper::tag()
	 */
	public function legend($text, array $options = []) {
		return $this->Html->tag('legend', $text, $options);
	}
	
	/**
     * Creates a button with a surrounding form that submits via POST.
     * 
     * This method creates a button in a form element. So don't use this method in an already opened form.
     * 
     * To create a normal button, you should use the `button()` method.
     * To create a button with the appearance of a link, you should use the `button()` method provided by the `MeHtmlHelper`.
     * @param string $title Button title
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addButtonClass()
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addValue()
	 * @uses postLink()
	 */
	public function postButton($title, $url, array $options = []) {
		$options = $this->Html->_addValue('role', 'button', $options);
		$options = $this->Html->_addButtonClass($options);

        return self::postLink($title, $url, $options);		
	}
	
	/**
     * Creates a link with a surrounding form that submits via POST.
     * 
     * This method creates a link in a form element. So don't use this method in an already opened form.
     *  
     * To create a normal link, you should use the `link()` method of the `MeHtmlHelper`.
	 * @param string $title The content to be wrapped by <a> tags
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addDefault()
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addIcon()
	 */
	public function postLink($title, $url = NULL, array $options = []) {
		$title = $this->Html->_addIcon($title, $options);
		unset($options['icon']);
				
		$options = $this->Html->_addDefault('title', $title, $options);
		$options['title'] = trim(h(strip_tags($options['title'])));

		$options = $this->Html->_addDefault('escape', FALSE, $options);
		$options = $this->Html->_addDefault('escapeTitle', FALSE, $options);

        return parent::postLink($title, $url, $options);
	}
	
	/**
     * Creates a submit button.
     * @param string $caption The label appearing on the submit button or an image
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @uses MeTools\View\Helper\MeHtmlHelper::_addButtonClass()
	 */
	public function submit($caption = null, array $options = []) {
		$options = $this->Html->_addButtonClass($options, 'success');
		
		return parent::submit($caption, $options);
	}
}