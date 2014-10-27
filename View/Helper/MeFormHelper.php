<?php

/**
 * MeFormHelper
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
 * @see			http://api.cakephp.org/2.5/class-FormHelper.html FormHelper
 */
App::uses('FormHelper', 'View/Helper');

/**
 * Provides functionalities for forms.
 * 
 * Rewrites {@link http://api.cakephp.org/2.5/class-FormHelper.html FormHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = array('Form' => array('className' => 'MeTools.MeForm'));
 * </code>
 */
class MeFormHelper extends FormHelper {
    /**
     * Helpers
     * @var array
     */
    public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
	
    /**
     * Property to check if we're working with an inline form.
     * It's changed by `create()` and `createInline()`.
     * @var bool
     */
    private $inline = FALSE;

    /**
     * Missing method handler - implements various simple input types. Is used to create inputs of various types.
     * @param string $method Method name/input type to make
     * @param array $params Parameters for the method call
     * @return string Formatted input method
	 * @see http://api.cakephp.org/2.5/class-Helper.html#___call CakePHP Api
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function __call($method, $params) {
		$params[1] = $this->Html->_addOptionValue('class', 'form-control', $params[1]);

        return parent::__call($method, $params);
    }
	
    /**
     * Gets a label text from the label field name.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @return string Label text
     */
    protected function _getLabelText($fieldName = NULL) {
        if(strpos($fieldName, '.') !== FALSE) {
            $fieldElements = explode('.', $fieldName);
            $text = array_pop($fieldElements);
        }
        else
            $text = $fieldName;

        if(substr($text, -3) === '_id')
            $text = substr($text, 0, -3);

        return Inflector::humanize(Inflector::underscore($text));
    }
	
	/**
     * Gets the input type.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options Options
     * @return string Type name
	 */
    protected function _getInputType($fieldName, $options) {
		$this->setEntity($fieldName);
        $options = parent::_parseOptions($options);
		
        return($options['type']);
    }

    /**
     * Creates a button.
     * 
     * This method creates a button. To create a POST button, you should use the `postButton()` method.
     * Instead, to create a link with the appearance of a button, you should use the `button()` method provided by the `MeHtmlHelper`.
     * @param string $title The button label or an image
     * @param array $options HTML attributes and options
     * @return string Html code
     * @see postButton(), MeHtmlHelper::button()
	 * @uses MeHtmlHelper::_addButtonClasses()
	 * @uses MeHtmlHelper::_addIcons()
	 * @uses MeHtmlHelper::_addOptionDefault()
     */
    public function button($title, $options = array()) {
		$options = $this->Html->_addOptionDefault('type', 'button', $options);
		$options = $this->Html->_addButtonClasses($options);
		
		$title = $this->Html->_addIcons($title, $options);
        unset($options['icon']);
		
        return parent::button($title, $options);
    }
	
    /**
     * Creates a CKEditor textarea.
     * 
     * To add the script for CKEditor, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see LibraryHelper::ckeditor()
	 * @uses MeHtmlHelper::_addOptionValue()
     * @uses input()
     */
    public function ckeditor($fieldName, $options = array()) {
		$options = $this->Html->_addOptionValue('class', 'ckeditor', $options);

        return self::input($fieldName, am($options, array('type' => 'textarea')));
    }

    /**
     * Creates a checkbox list with style and buttons to check/uncheck all checkboxes.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Checkbox list as html code
     * @uses button()
     * @uses input()
	 * @uses MeHtmlHelper::_addOptionValue()
	 * @uses MeHtmlHelper::div()
     */
    public function checkboxList($fieldName, $options = array()) {		
		$buttons = $this->Html->div('checkbox-buttons', 
			self::button(__d('me_tools', 'Check all'), array('class' => 'checkAll btn-sm', 'icon' => 'check-square-o')).
			self::button(__d('me_tools', 'Uncheck all'), array('class' => 'uncheckAll btn-sm', 'icon' => 'minus-square-o'))
		);

        $options['between'] = empty($options['between']) ? $buttons : $buttons.$options['between'];
		
		$options['div'] = empty($options['div']) ? array() : $options['div'];
		$options['div'] = $this->Html->_addOptionValue('class', 'checkboxes-list', $options['div']);
		
        return self::input($fieldName, am($options, array('multiple' => 'checkbox')));
    }
	
    /**
     * Returns a `<form>` element.
     * @param mixed $model The model name for which the form is being defined. If `FALSE` no model is used
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
	 * @uses createInline()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function create($model = NULL, $options = array()) {		
        if(!empty($options['inline']))
            return self::createInline($model, $options);
		
		$options = $this->Html->_addOptionValue('role', 'form', $options);

        return parent::create($model, $options);
    }
	
    /**
     * Returns an inline form element.
     * 
     * You can also create an inline form using the `create()` method with the `inline` option.
     * 
     * Note that by default `createInline` doesn't display errors. To view the errors, however, you have to set 
	 * to `TRUE` the `errorMessage` option of `inputDefaults`. For example:
     * <code>$this->Form->createInline('Fake', array('inputDefaults' => array('errorMessage' => TRUE)));</code>
     * @param mixed $model The model name for which the form is being defined. If `FALSE` no model is used
     * @param array $options HTML attributes and options
     * @return string An formatted opening `<form>` tag
     * @uses create()
     * @uses inline
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function createInline($model = NULL, $options = array()) {
        $this->inline = TRUE;
        unset($options['inline']);

		$options = $this->Html->_addOptionValue('class', 'form-inline', $options);
		$options = $this->Html->_addOptionValue('role', 'form', $options);

        //By default it doesn't display errors
		$options['inputDefaults'] = empty($options['inputDefaults']) ? array() : $options['inputDefaults'];
		$options['inputDefaults'] = $this->Html->_addOptionDefault('errorMessage', FALSE, $options);

        return self::create($model, $options);
    }

    /**
     * Creates a datepicker input.
     * 
     * To add the script for datepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see LibraryHelper::datepicker()
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
     * @uses input()
     */
    public function datepicker($fieldName, $options = array()) {
		$options = $this->Html->_addOptionValue('class', 'datepicker', $options);
		$options = $this->Html->_addOptionDefault('data-date-format', 'YYYY-MM-DD HH:mm', $options);
		
        return self::input($fieldName, am($options, array('type' => 'text')));
    }
	
    /**
     * Creates a datetimepicker input.
     * 
     * To add the script for datetimepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see LibraryHelper::datetimepicker()
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
     * @uses input()
     */
    public function datetimepicker($fieldName, $options = array()) {
		$options = $this->Html->_addOptionValue('class', 'datetimepicker', $options);
		$options = $this->Html->_addOptionDefault('data-date-format', 'YYYY-MM-DD HH:mm', $options);
		
        return self::input($fieldName, am($options, array('type' => 'text')));
	}

    /**
     * Closes an HTML form, cleans up values, and writes hidden input fields.
     * @param string $caption The label appearing on the submit button or an image
     * @param array $options HTML attributes and options
     * @return string Html code
     * @uses inline
	 * @uses _addOptionDefault()
     */
    public function end($caption = NULL, $options = array()) {
		if($this->inline)
			$options = $this->Html->_addOptionDefault('div', FALSE, $options);
		
		//Normally, the `end()` method of the `HtmlHelper` has only the `option` argument, which is an array. 
        //So, this allows compatibility with the original method.
        //Look at {@link http://api.cakephp.org/2.5/source-class-FormHelper.html#477-527}		
		if(!empty($options))
			$caption = am($options, array('label' => $caption));
		
        $this->inline = FALSE;
		
		return parent::end($caption);
    }
	
	/**
	 * Generates an input element complete with label and wrapper div.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
	 * @return string Html code
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
	 * @uses MeHtmlHelper::span()
	 * @uses inline
	 * @uses _getInputType()
	 */
    public function input($fieldName, $options = array()) {
		//If the field name contains the word "password", then the field type is "password"
		if(preg_match('/password/', $fieldName))
			$options = $this->Html->_addOptionDefault('type', 'password', $options);
		
		$type = self::_getInputType($fieldName, $options);
		$options = $this->Html->_addOptionDefault('after', NULL, $options);
		
		//Changes the "autocomplete" value from "FALSE" to "off"
		if(isset($options['autocomplete']) && !$options['autocomplete'])
			$options['autocomplete'] = 'off';
		
		//If it's a checkbox, the input should be before the label
		if($type === 'checkbox')
			$options = $this->Html->_addOptionDefault('format', array('before', 'input', 'between', 'label', 'after', 'error'), $options);
		//If it's a textarea
		elseif($type === 'textarea') {
			$options = $this->Html->_addOptionDefault('cols', NULL, $options);
			$options = $this->Html->_addOptionDefault('rows', NULL, $options);
        }
		
        //If this is an inline form and the field is not a checkbox
        if($this->inline && $type !== "checkbox") {
            if(empty($options['label']))
                $options['label'] = array();
            elseif(is_string($options['label']))
                $options['label'] = array('text' => $options['label']);
			
			$options['label'] = $this->Html->_addOptionValue('class', 'sr-only', $options['label']);
        }
		
		if(!isset($options['div']) || !empty($options['div'])) {
			$options['div'] = empty($options['div']) ? array() : $options['div'];
			$options['div'] = $this->Html->_addOptionValue('class', array('input', $type, 'form-group'), $options['div']);
			
			//If the field has an error
			if(parent::isFieldError($fieldName))
				$options['div'] = $this->Html->_addOptionValue('class', 'has-error', $options['div']);
		}
		
		//Tips are shown only if this's not an inline form
        if(!empty($options['tip']) && !$this->inline) {
			$options['tip'] = is_array($options['tip']) ? $options['tip'] : array($options['tip']);
			
			$options['after'] .= implode(PHP_EOL, array_map(function($v){
				return $this->Html->span(trim($v), array('class' => 'help-block'));
			}, $options['tip']));
		}
			
		unset($options['tip']);
		
        return parent::input($fieldName, $options);
	}

    /**
     * Returns a formatted `<label>` element. 
	 * Will automatically generate a `for` attribute if one is not provided.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param string $text Text that will appear in the label field. If is left undefined the text will be inflected from the fieldName
     * @param array|string $options HTML attributes, or a string to be used as a class name
	 * @return string Html code
	 * @uses MeHtmlHelper::_addIcons()
	 * @uses _getLabelText()
	 * 
     */
    public function label($fieldName = NULL, $text = NULL, $options = array()) {
        if(is_string($options))
            $options = array('class' => $options);

		$text = empty($text) ? self::_getLabelText($fieldName) : $text;

		$text = $this->Html->_addIcons($text, $options);
        unset($options['icon']);

        return parent::label($fieldName, $text, $options);
    }
	
	/**
	 * Creates a `<legend>` tag.
     * @param string $text Legend text
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @uses MeHtmlHelper::legend()
	 */
	public function legend($text, $options = array()) {
		return $this->Html->legend($text, $options);
	}

    /**
     * Creates a link with a surrounding form that submits via POST.
     * 
     * This method creates a link in a form element. So don't use this method in an already opened form.
     *  
     * To create a normal link, you should use the `link()` method of the `MeHtmlHelper`.
     * @param string $title Button title
     * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
     * @param array $options HTML attributes and options
     * @param string $confirmMessage JavaScript confirmation message
     * @return string Html code
     * @see MeHtmlHelper::link()
	 * @uses MeHtmlHelper::_addOptionDefault()
     */
    public function postLink($title, $url = NULL, $options = array(), $confirmMessage = FALSE) {
		$options = $this->Html->_addOptionDefault('escape', FALSE, $options);

        return parent::postLink($title, $url, $options, $confirmMessage);
    }

    /**
     * Creates a button with a surrounding form that submits via POST.
     * 
     * This method creates a button in a form element. So don't use this method in an already opened form.
     * 
     * To create a normal button, you should use the `button()` method.
     * To create a button with the appearance of a link, you should use the `button()` method provided by the `MeHtmlHelper`.
     * @param string $title Button title
     * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
     * @param array $options HTML attributes and options
     * @param string $confirmMessage JavaScript confirmation message
     * @return string Html code
     * @see button(), MeHtmlHelper::button()
	 * @uses MeHtmlHelper::_addButtonClasses()
     * @uses postLink()
     */
    public function postButton($title, $url, $options = array(), $confirmMessage = FALSE) {
        //The `postButton()` method doesn't have the `$confirmMessage`, then in this case we need to use `postLink()`
        if($confirmMessage) {
			$options = $this->Html->_addButtonClasses($options);
            return self::postLink($title, $url, $options, $confirmMessage);
        }

        return parent::postButton($title, $url, am($options, array('type' => 'submit')));
    }
	
    /**
     * Creates a set of radio button inputs.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options Radio options
     * @param array $attributes HTML attributes
     * @return string Html code
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes = $this->Html->_addOptionValue('separator', '<br />', $attributes);

        return parent::radio($fieldName, $options, $attributes);
    }

    /**
     * Creates a select input.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options Select options
     * @param array $attributes HTML attributes
     * @return string Html code
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function select($fieldName, $options = array(), $attributes = array()) {
		if(empty($attributes['default']) && !in_array('multiple', $attributes))
			$attributes = $this->Html->_addOptionDefault('empty', __d('me_tools', 'Select an option'), $attributes);
		if(empty($attributes['default']))
			$attributes = $this->Html->_addOptionDefault('empty', FALSE, $attributes);
		
		if(empty($attributes['multiple']) || $attributes['multiple'] !== 'checkbox')
			$attributes = $this->Html->_addOptionValue('class', 'form-control', $attributes);

        return parent::select($fieldName, $options, $attributes);
    }
	
    /**
     * Creates a submit button.
     * @param string $caption The label appearing on the submit button or an image
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @uses inline
     * @uses button()
	 * @uses div()
	 * @uses MeHtmlHelper::_addButtonClasses()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
	public function submit($caption = NULL, $options = array()) {
		$options = $this->Html->_addButtonClasses($options, 'success');
				
		//Gets the submit button
		$button = self::button($caption, am($options, array('type' => 'submit', 'value' => $caption)));
		
		//If is set the "div" option and this is FALSE or if this is an inline form and it's not 
		//set the "div" option, returns the button without a wrapper
        if((isset($options['div']) && !$options['div']) || ($this->inline && !isset($options['div'])))
            return $button;
				
		$divOptions = empty($options['div']) ? array() : $options['div'];
        unset($options['div']);
		
		$divOptions = $this->Html->_addOptionValue('class', 'submit', $divOptions);
		
        return $this->Html->div($divOptions['class'], $button, $divOptions);
	}

    /**
     * Creates a textarea element.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @uses _addOptionValue()
     */
    public function textarea($fieldName, $options = array()) {
		$options = $this->Html->_addOptionValue('class', 'form-control', $options);

        return parent::textarea($fieldName, $options);
    }

    /**
     * Creates a text input for timepicker.
     * 
     * To add the script for timepicker, you should use the `LibraryHelper`.
     * @param string $fieldName Field name, should be "Modelname.fieldname"
     * @param array $options HTML attributes and options
     * @return string Html code
	 * @see LibraryHelper::timepicker()
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
     * @uses input()
     */
    public function timepicker($fieldName, $options = array()) {
		$options = $this->Html->_addOptionValue('class', 'timepicker', $options);
		$options = $this->Html->_addOptionDefault('data-date-format', 'YYYY-MM-DD HH:mm', $options);
		
		$options['div'] = empty($options['div']) ? array() : $options['div'];		
		$options['div'] = $this->Html->_addOptionValue('class', 'bootstrap-timepicker', $options['div']);
		
        return self::input($fieldName, am($options, array('type' => 'text')));
    }
}