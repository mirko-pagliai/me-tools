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
 * @see			http://api.cakephp.org/3.2/class-Cake.View.Helper.FormHelper.html FormHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\FormHelper as CakeFormHelper;
use Cake\View\View;

/**
 * Provides functionalities for forms.
 * 
 * Rewrites {@link http://api.cakephp.org/3.2/class-Cake.View.Helper.FormHelper.html FormHelper}.
 */
class FormHelper extends CakeFormHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.Html'], 'Url'];
	
	/**
	 * Property to check if we're working with an inline form.
     * It's changed by `createInline()` method.
     * @var bool
     */
	protected $inline = FALSE;

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
			'file' => '<input type="file" name="{{name}}"{{attrs}}>',
			'input' => '<input type="{{type}}" name="{{name}}"{{attrs}}>',
			'select' => '<select name="{{name}}"{{attrs}}>{{content}}</select>',
			'selectMultiple' => '<select name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
			'textarea' => '<textarea name="{{name}}"{{attrs}}>{{value}}</textarea>',
			'submitContainer' => '<div class="submit form-group">{{content}}</div>',
		]);
    }
	
	/**
	 * Generates an input element.
	 * This method is used only to provide the "tip" functionality.
	 * @param string $fieldName the field name
	 * @param array $options The options for the input element
	 * @return string The generated input element
	 * @see http://api.cakephp.org/3.2/class-Cake.View.Helper.FormHelper.html#__getInput
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
	 * @see http://api.cakephp.org/3.2/class-Cake.View.Helper.FormHelper.html#__inputContainerTemplate
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
             'type' => $options['options']['type'],
         ]);
     }
	
	/**
     * Creates a button.
     * 
     * This method creates a button. To create a POST button, you should use 
     *  the `postButton()` method.
     * Instead, to create a link with the appearance of a button, you should 
     *  use the `button()` method provided by `HtmlHelper`.
     * @param string $title The button label or an image
     * @param array $options HTML attributes and options
     * @return string Html code
     * @see postButton(), HtmlHelper::button()
	 * @uses MeTools\View\Helper\HtmlHelper::_addButtonClass()
	 * @uses MeTools\View\Helper\HtmlHelper::_addIcon()
	 */
	public function button($title, array $options = []) {
		$options = optionDefaults('type', 'button', $options);
		
		if($options['type'] !== 'submit') {
			$options = $this->Html->_addButtonClass($options);
        }
		else {
			$options = $this->Html->_addButtonClass($options, 'success');
        }
        
		$title = $this->Html->_addIcon($title, $options);
        unset($options['icon'], $options['icon-align']);
		
		return parent::button($title, $options);
	}
	
	/**
	 * Creates a checkbox input element.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
	 * @return string Html code
	 */
	public function checkbox($fieldName, array $options = []) {		
		if($options['hiddenField'] !== FALSE) {
			$options['hiddenField'] = TRUE;
        }
		
		//Checkboxes inputs outside of the label
		$this->templates([
			'nestingLabel' => '{{input}}<label{{attrs}}>{{text}}</label>',
			'formGroup' => '{{input}}{{label}}',
		]);
		
		return parent::checkbox($fieldName, $options);
	}
	
    /**
     * Creates a CKEditor textarea.
     * 
     * To add the script for CKEditor, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see MeTools\View\Helper\LibraryHelper::ckeditor()
     * @uses input()
     */
    public function ckeditor($fieldName, array $options = []) {
		$options = optionValues('class', 'ckeditor', $options);

        return self::input($fieldName, am($options, ['type' => 'textarea']));
    }
	
    /**
     * Returns a `<form>` element.
     * @param mixed $model The model name for which the form is being defined. 
     *  If `FALSE` no model is used
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
	 * @uses createInline()
     */
    public function create($model = NULL, array $options = []) {		
        if(!empty($options['inline'])) {
            return self::createInline($model, $options);
        }
        
        return parent::create($model, $options);
    }
	
    /**
     * Returns an inline form element.
     * 
     * You can also create an inline form using the `create()` method with 
     *  the `inline` option.
     * 
     * Note that by default `createInline` doesn't display errors.
     * @param mixed $model The model name for which the form is being defined. 
     *  If `FALSE` no model is used
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
     * @uses create()
     * @uses inline
     */
    public function createInline($model = NULL, array $options = []) {
        $this->inline = TRUE;
        unset($options['inline']);

		$options = optionValues('class', 'form-inline', $options);

        return self::create($model, $options);
    }

    /**
     * Creates a datepicker input.
     * 
     * To add the script for datepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see MeTools\View\Helper\LibraryHelper::datepicker()
     * @uses input()
     */
    public function datepicker($fieldName, array $options = []) {
		$options = optionValues('class', 'datepicker', $options);
		$options = optionDefaults('data-date-format', 'YYYY-MM-DD', $options);
		
        return self::input($fieldName, am($options, ['type' => 'text']));
    }
	
    /**
     * Creates a datetimepicker input.
     * 
     * To add the script for datetimepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see MeTools\View\Helper\LibraryHelper::datetimepicker()
     * @uses input()
     */
    public function datetimepicker($fieldName, array $options = []) {
		$options = optionValues('class', 'datetimepicker', $options);
		$options = optionDefaults('data-date-format', 'YYYY-MM-DD HH:mm', $options);
		
        return self::input($fieldName, am($options, ['type' => 'text']));
	}
	
	/**
	 * Closes an HTML form, cleans up values set by `FormHelper::create()`, 
     *  and writes 
	 * hidden input fields where appropriate.
	 * @param array $secureAttributes Secure attibutes which will be passed 
     *  as HTML attributes into the hidden input elements generated for the 
     *  Security Component.
	 * @return string Html code
	 * @uses inline
	 */
	public function end(array $secureAttributes = []) {
		$this->inline = FALSE;
		
		return parent::end($secureAttributes);
	}
	
	/**
	 * Generates an input element complete with label and wrapper div.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
	 * @return string Html code
	 * @uses MeTools\View\Helper\HtmlHelper::span()
	 */
    public function input($fieldName, array $options = []) {
		//Gets the input type
		$type = empty($options['type']) ? self::_inputType($fieldName, $options) : $options['type'];
		
        if($type !== 'file') {
            $options = optionValues('class', 'form-control', $options);
        }
        
		//If the field name contains the word "password", then the field type 
        //  is "password"
		if(preg_match('/password/', $fieldName)) {
			$options = optionDefaults('type', 'password', $options);
        }
        
		//Changes the "autocomplete" value from "FALSE" to "off"
		if(isset($options['autocomplete']) && !$options['autocomplete']) {
			$options['autocomplete'] = 'off';
        }
		
		//If it's a select
		if($type === 'select') {
			//By default, the `empty` option will be automatically added 
            //  (with `FALSE` value). This option will be used by the 
            //  `select()` method to see if the option has been added by the 
            //  user or not
			if(!isset($options['empty'])) {
				$options = optionDefaults('remove_empty', TRUE, $options);
            }
		}
		//Else, if it's a textarea
		elseif($type === 'textarea') {
			$options = optionDefaults('cols', NULL, $options);
			$options = optionDefaults('rows', NULL, $options);
        }
		
		//Sets the default templates
		//These values can be overwritten by other methods
		$this->templates([
			'formGroup' => '{{label}}{{input}}',
			'inputContainer' => '<div class="input form-group {{type}}{{required}}">{{content}}{{tip}}</div>',
			'inputContainerError' => '<div class="input form-group {{type}}{{required}} has-error">{{content}}{{tip}}{{error}}</div>',
			'nestingLabel' => '{{hidden}}<label{{attrs}}>{{input}}{{text}}</label>',
		]);
		
		//Sets tips ("help text")
		//See http://getbootstrap.com/css/#forms-help-text
		if(!empty($options['tip'])) {
			$options['tip'] = implode(PHP_EOL, array_map(function($v) {
				return $this->Html->span(trim($v), ['class' => 'help-block']);
			}, is_array($options['tip']) ? $options['tip'] : [$options['tip']]));
        }
        
		//Sets "button addon"
		//See http://getbootstrap.com/components/#input-groups-buttons
		if(!empty($options['button'])) {
			//Fixes templates
			$this->templates(['formGroup' => preg_replace('/\{\{input\}\}/', '<div class="input-group">{{input}}{{button}}</div>', $this->templates('formGroup'))]);
			
			$options['templateVars']['button'] = $this->Html->span($options['button'], ['class' => 'input-group-btn']);
			unset($options['button']);
		}
		
		//If is an inline form
		if($this->inline) {
			//By default, disables tips and error messages
			$this->templates([
				'inputContainer' => '<div class="input form-group {{type}}{{required}}">{{content}}</div>',
				'inputContainerError' => '<div class="input form-group {{type}}{{required}} has-error">{{content}}</div>',
			]);
			
			//If it is not a checkbox
			if($type !== "checkbox") {
				if(empty($options['label'])) {
					$options['label'] = [];
                }
				elseif(is_string($options['label'])) {
					$options['label'] = ['text' => $options['label']];
                }
                
				$options['label'] = optionValues('class', 'sr-only', $options['label']);
			}
		}
		
        return parent::input($fieldName, $options);
	}
	
	/**
     * Returns a formatted `<label>` element. 
	 * Will automatically generate a `for` attribute if one is not provided.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param string $text Text that will appear in the label field. If is 
     *  left undefined the text will be inflected from the fieldName
     * @param array|string $options HTML attributes, or a string to be used 
     *  as a class name
	 * @return string Html code
	 * @uses MeTools\View\Helper\HtmlHelper::_addIcon()
	 */
	public function label($fieldName, $text = NULL, array $options = []) {
		$options = optionDefaults('escape', FALSE, $options);

		$text = $this->Html->_addIcon($text, $options);
        unset($options['icon'], $options['icon-align']);
		
		return parent::label($fieldName, $text, $options);
	}
	
	/**
     * Creates a button with a surrounding form that submits via POST.
     * 
     * This method creates a button in a form element. So don't use this 
     *  method in an already opened form.
     * 
     * To create a normal button, you should use the `button()` method.
     * To create a button with the appearance of a link, you should use the 
     *  `button()` method provided by the `HtmlHelper`.
     * @param string $title Button title
	 * @param string|array $url Cake-relative URL or array of URL parameters 
     *  or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses MeTools\View\Helper\HtmlHelper::_addButtonClass()
	 * @uses postLink()
	 */
	public function postButton($title, $url, array $options = []) {
		$options = optionValues('role', 'button', $options);
		$options = $this->Html->_addButtonClass($options);

        return self::postLink($title, $url, $options);		
	}
	
	/**
     * Creates a link with a surrounding form that submits via POST.
     * 
     * This method creates a link in a form element. So don't use this method 
     *  in an already opened form.
     *  
     * To create a normal link, you should use the `link()` method of the 
     *  `HtmlHelper`.
	 * @param string $title The content to be wrapped by <a> tags
	 * @param string|array $url Cake-relative URL or array of URL parameters 
     *  or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses MeTools\View\Helper\HtmlHelper::_addIcon()
	 */
	public function postLink($title, $url = NULL, array $options = []) {
		$title = $this->Html->_addIcon($title, $options);
		unset($options['icon'], $options['icon-align']);
				
		$options = optionDefaults('title', $title, $options);
		$options['title'] = trim(h(strip_tags($options['title'])));

		$options = optionDefaults('escape', FALSE, $options);
		$options = optionDefaults('escapeTitle', FALSE, $options);

        return parent::postLink($title, $url, $options);
	}
	
	/**
	 * Returns a formatted SELECT element
	 * @param string $fieldName Name attribute of the SELECT
	 * @param array|\Traversable $options Array of the OPTION elements 
     *  (as 'value'=>'Text' pairs) to be used in the SELECT element
	 * @param array $attributes The HTML attributes of the select element
	 * @return string Formatted SELECT element
	 */
	public function select($fieldName, $options = [], array $attributes = []) {
		//If there's the `remove_empty` option, it means that the `empty` 
        //  option has been added automatically  by default and not by the 
        //  user. Then, it removes the `empty` and `remove_empty` options
		if(!empty($attributes['remove_empty'])) {
			unset($attributes['empty'], $attributes['remove_empty']);
        }
        
		if(!isset($attributes['empty']) && empty($attributes['default']) && empty($attributes['value'])) {
			$attributes = optionDefaults('empty', TRUE, $attributes);
        }
		
		return parent::select($fieldName, $options, $attributes);
	}
	
	/**
     * Creates a submit button.
     * @param string $caption The label appearing on the submit button or an 
     *  image
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @uses button()
	 */
	public function submit($caption = NULL, array $options = []) {	
		return self::button($caption, am(['type' => 'submit'] ,$options));
	}

    /**
     * Creates a text input for timepicker.
     * 
     * To add the script for timepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see MeTools\View\Helper\LibraryHelper::timepicker()
     * @uses input()
     */
    public function timepicker($fieldName, array $options = []) {
		$options = optionValues('class', 'timepicker', $options);
		$options = optionDefaults('data-date-format', 'HH:mm', $options);
		
        return self::input($fieldName, am($options, ['type' => 'text']));
    }
}