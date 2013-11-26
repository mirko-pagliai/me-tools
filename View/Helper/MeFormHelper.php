<?php
App::uses('FormHelper', 'View/Helper');

/**
 * Provides functionalities for forms.
 * 
 * You should use this helper as an alias, for example:
 * <pre>public $helpers = array('Form' => array('className' => 'MeTools.MeForm'));</pre>
 * 
 * MeFormHelper extends {@link http://api.cakephp.org/2.4/class-FormHelper.html FormHelper}
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
 * @package		MeTools\View\Helper
 */
class MeFormHelper extends FormHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
	
	/*
	 * Property to check if we're working with an inline form.
	 * It's changed by `create()` and `createInline()`.
	 */
	protected $inline = FALSE;
	
	/**
	 * Missing method handler - implements various simple input types. Is used to create inputs of various types.
	 * @param string $method Method name/input type to make
	 * @param array $params Parameters for the method call
	 * @return string Formatted input method
	 */
	 public function __call($method, $params) {
		$params[1]['class'] = empty($params[1]['class']) ? 'form-control' : $this->Html->__clean('form-control', $params[1]['class']);		

		return parent::__call($method, $params);
	 }

	/**
	 * Gets the input type
	 * @param array $options Options
	 * @return string Type name
	 */
	protected function __getInputType($options) {
		$options = parent::_parseOptions($options);
		return($options['type']);
	}
	
	/**
	 * Gets a label text from the label field name
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @return string Label text
	 */
	protected function __getLabelText($fieldName = null) {
		if(strpos($fieldName, '.') !== false) {
			$fieldElements = explode('.', $fieldName);
			$text = array_pop($fieldElements);
		}
		else
			$text = $fieldName;
		
		if(substr($text, -3) === '_id')
			$text = substr($text, 0, -3);
		
		return __(Inflector::humanize(Inflector::underscore($text)));
	}

	/**
	 * Creates a button.
	 * 
	 * Note: this method creates a button. 
	 * To create a POST button, you should use the `postButton()` method.
	 * Instead, to create a link with the appearance of a button, you should use the `button()` method of the `MeHtml` helper.
	 * @param string $caption The button label or an image
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function button($title, $options = array()) {
		$options['type'] = empty($options['type']) ? 'button' : $options['type'];

		$title = empty($options['icon']) ? $title : $this->Html->icon($options['icon']).$title;
		unset($options['icon']);
		
		return parent::button($title, am($options, array('class' => $this->Html->__getBtnClass($options))));
	}
	
	/**
	 * Creates a checkbox list with style and buttons to check/uncheck all checkboxes.
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options HTML attributes
	 * @return string Checkbox list as Html
	 */
	public function checkboxList($fieldName, $options = array()) {
		$buttons = self::button(__d('me_tools', 'Check all'), array('class' => 'checkAll btn-default', 'icon' => 'fa-check-square-o'));
		$buttons .= self::button(__d('me_tools', 'Uncheck all'), array('class' => 'uncheckAll btn-default', 'icon' => 'fa-minus-square-o'));
		$buttons = $this->Html->div('checkbox-buttons', $buttons);
		
		$options['between'] = empty($options['between']) ? $buttons : $button.$options['between'];
		$options['div']['class'] = empty($options['div']['class']) ? 'checkboxes-list' : $this->Html->__clean('checkboxes-list', $options['div']['class']);
		
		return self::input($fieldName, am($options, array('multiple' => 'checkbox')));
	}
	
	/**
	 * Creates a CKEditor textarea.
	 * 
	 * To add the script for CKEditor, you should use the `Library` helper.
	 * Please refer to the `README` file.
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function ckeditor($fieldName, $options=array()) {
		$options['class'] = empty($options['class']) ? 'wysiwyg' : $this->Html->__clean('wysiwyg', $options['class']);
		
		return self::input($fieldName, am($options, array('label' => FALSE, 'required' => FALSE, 'type' => 'textarea')));
	}
	
	/**
	 * Returns a form element.
	 * @param mixed $model The model name for which the form is being defined. If `false` no model is used
	 * @param array $options HTML attributes and options
	 * @return string An formatted opening FORM tag
	 */
	public function create($model = null, $options = array()) {
		$options['class'] = empty($options['class']) ? 'form-base' : $this->Html->__clean($options['class']);
		
		return parent::create($model, $options);
	}
	
	/**
	 * Returns an inline form element.
	 * 
	 * Note that by default `createInline` doesn't display errors. To view the errors, however, you must set to TRUE 
	 * the `errorMessage` of `inputDefaults`. For example:
	 * <pre>$this->Form->createInline('Fake', array('inputDefaults' => array('errorMessage' => true)));</pre>
	 * @param mixed $model The model name for which the form is being defined. If `false` no model is used
	 * @param array $options HTML attributes and options
	 * @return string An formatted opening FORM tag
	 */
	public function createInline($model = null, $options = array()) {
		$this->inline = TRUE;
		
		$options['class'] = empty($options['class']) ? 'form-base form-inline' : $this->Html->__clean($options['class']);
		
		//By default it doesn't display errors
		$options['inputDefaults']['errorMessage'] = empty($options['inputDefaults']['errorMessage']) ? false : $options['inputDefaults']['errorMessage'];
		
		return self::create($model, $options);
	}
	
	/**
	 * Creates a datepicker input.
	 * 
	 * To add the script for datepicker, you should use the `Library` helper.
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function datepicker($fieldName, $options=array()) {
		$options['class'] = empty($options['class']) ? 'datepicker' : $this->Html->__clean('datepicker', $options['class']);
		
		return self::input($fieldName, am($options, array('type' => 'text')));
	}
	
	/**
	 * Closes an HTML form, cleans up values, and writes hidden input fields.
	 * @param string $caption The label appearing on the submit button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function end($caption = null, $options = array()) {
		$this->inline = FALSE;
		
		//Normally, the `end()` method of the HtmlHelper has only the "option" argument, which is an array. 
		//So, this allows compatibility with the original method.
		//Look at {@link http://api.cakephp.org/2.4/source-class-FormHelper.html#477-527}
		if(is_array($caption) && empty($options))
			return parent::end($caption);
		unset($options['label']);
		
		$submit = !empty($caption) ? self::submit($caption, $options) : null;
		
		return $submit.parent::end();
	}
	
	/**
	 * Checks and returns a value if this is not empty, else returns a default value.
	 * 
	 * It can be useful with the "selected" option, to get a value if this exists or use a default. For example:  
	 * <code>
	 * 'selected' => @$this->Form->getDefault($this->request->data['User']['group'], 'user')
	 * </code>
	 * will set the "selected" option to 
	 * <pre>$this->request->data['User']['group']</pre>
	 * if this exists (for example, if the form has already been sent), else it will use the "user" default value.
	 * 
	 * It must be used with the "@" operator, otherwise it will generate a notice.
	 * @param string $value Value to check
	 * @param string $default Default value
	 * @return string Value to check if this is not empty, else default value
	 */
	public function getDefault($value, $default) {
		return empty($value) ? $default : $value;
	}
	
	/**
	 * Generates an input element complete with label and wrapper div.
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function input($fieldName, $options = array()) {
		$type = self::__getInputType($options);
		
		if(!isset($options['div']) || !empty($options['div'])) {
			$hasError = !parent::isFieldError($fieldName) ? null : 'has-error';
			$options['div']['class'] = empty($options['div']['class']) ? "input {$type} {$hasError} form-group" : $this->Html->__clean('input', $type, $hasError, 'form-group', $options['div']['class']);
		}			
		
		if($type === 'textarea') {
			$options['cols'] = empty($options['cols']) ? null : $options['cols'];
			$options['rows'] = empty($options['rows']) ? null : $options['rows'];
		}
		
		//If it's not a checkbox and if this is an inline form
		if($this->inline && $type !== "checkbox") {
			if(empty($options['label']))
				$options['label'] = array();
			elseif(is_string($options['label']))
				$options['label']= array('text' => $options['label']);
			
			$options['label']['class'] = empty($options['label']['class']) ? 'sr-only' : $this->Html->__clean('sr-only', $options['label']['class']);
		}
		
		if(!empty($options['tip'])) {
			$options['after'] = empty($options['after']) ? null : $options['after'];
			if(!is_array($options['tip']))
				$options['after'] .= $this->Html->tag('span', trim($options['tip']), array('class' => 'help-block'));
			else
				$options['after'] .= implode(PHP_EOL, array_map(function($v){ return $this->Html->tag('span', trim($v), array('class' => 'help-block')); }, $options['tip']));
		}
				
		return parent::input($fieldName, $options);
	}
	
	/**
	 * Returns a formatted LABEL element. Will automatically generate a `for` attribute if one is not provided.
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param string $text Text that will appear in the label field. If is left undefined the text will be inflected from the fieldName
	 * @param array|string $options HTML attributes, or a string to be used as a class name
	 * @return string Html
	 */
	public function label($fieldName = null, $text = null, $options = array()) {
		if(!empty($options) && is_string($options))
			$options = array('class' => $options);
				
		$text = empty($text) ? self::__getLabelText($fieldName) : $text;
		
		$text = empty($options['icon']) ? $text : $this->Html->icon($options['icon']).$text;
		unset($options['icon']);
		
		$options['class'] = empty($options['class']) ? 'control-label' : $this->Html->__clean('control-label', $options['class']);
		
		return parent::label($fieldName, $text, $options);
	}
	
	/**
	 * Creates a button with a surrounding form that submits via POST.
	 * 
	 * Note: this method creates a button in a form element. So don't use this method in an already opened form.
	 * To create a normal button, you should use the `button()` method.
	 * To create a button with the appearance of a link, you should use the `button()` method of the `MeHtml` helper.
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function postButton($title, $url, $options = array(), $confirmMessage = false) {
		//In CakePHP, the `postButton()` method doesn't have the `$confirmMessage`, then in this case we need to use `postLink()`
		if($confirmMessage) {
			$options['class'] = $this->Html->__getBtnClass($options);
			return self::postLink($title, $url, $options, $confirmMessage);
		}
		
		return parent::postButton($title, $url, am($options, array('type' => 'submit')));
	}

	/**
	 * Creates a link with a surrounding form that submits via POST.
	 * 
	 * Note: this method creates a link in a form element. So don't use this method in an already opened form. 
	 * To create a normal link, you should use the `link()` method of the `MeHtml` helper.
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function postLink($title, $url = null, $options = array(), $confirmMessage = false) {
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		
		return parent::postLink($title, $url, $options, $confirmMessage);
	}
	
	/**
	 * Creates a set of radio button inputs. Rewrites <i>$this->Form->radio()</i>
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Radio options
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function radio($fieldName, $options = array(), $attributes = array()) {
		$attributes['separator'] = empty($attributes['separator']) ? '<br />' : $attributes['separator'];
		
		return parent::radio($fieldName, $options, $attributes);
	}
	
	/**
	 * Creates a select input. Rewrites <i>$this->Form->select()</i>
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Select options
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function select($fieldName, $options = array(), $attributes = array()) {
		//Sets the "empty" attribute to "Select an option" only if:
		// 1) "empty", "default" and "value" attributes are empty
		// 2) this isn't a multiple select or a multiple checkbox
		if(empty($attributes['empty']) && 
				empty($attributes['default']) && 
				empty($attributes['value']) &&
				empty($attributes['selected']) &&
				!in_array('multiple', $attributes) && 
				empty($attributes['multiple']))
			$attributes['empty'] = __d('me_tools', 'Select an option');
		elseif(empty($attributes['empty']))
			$attributes['empty'] = false;
		
		if(empty($attributes['multiple']) || $attributes['multiple']!=='checkbox')
			$attributes['class'] = empty($attributes['class']) ? 'form-control' : $this->Html->__clean('form-control', $attributes['class']);
		
		return parent::select($fieldName, $options, $attributes);
	}
	
	/**
	 * Creates a submit button
	 * @param string $caption The label appearing on the submit button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function submit($caption = null, $options = array()) {
		$caption = !empty($caption) ? $caption : __d('me_tools', 'Submit');
		
		$options['class'] = empty($options['class']) ? 'btn btn-success' : $this->Html->__clean('btn', $options['class']);
		$options['type'] = 'submit';
		$options['value'] = $caption;
		
		//If is set the "div" option and this is FALSE or if this is an inline form, returns the button without a wrapper
		if((isset($options['div']) && !$options['div']) || !empty($this->inline))
			return self::button($caption, $options);
		
		$divOptions = empty($options['div']) ? array() : $options['div'];
		unset($options['div']);
		
		$divOptions['class'] = empty($divOptions['class']) ? 'submit' : $this->Html->__clean('submit', $divOptions['class']);

		return $this->Html->div($divOptions['class'], self::button($caption, $options), $divOptions);
	}
	
	/**
	 * Creates a textarea element.
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function textarea($fieldName, $options = array()) {
		$options['class'] = empty($options['class']) ? 'form-control' : $this->Html->__clean('form-control', $options['class']);
		
		return parent::textarea($fieldName, $options);
	}
	
	/**
	 * Creates a text input for timepicker.
	 * 
	 * To add the script for timepicker, you should use the `Library` helper.
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function timepicker($fieldName, $options=array()) {
		$options['class'] = empty($options['class']) ? 'timepicker' : $this->Html->__clean('timepicker', $options['class']);

		$options['div']['class'] = empty($options['div']['class']) ? 'bootstrap-timepicker' : $this->Html->__clean('bootstrap-timepicker', $options['div']['class']);

		return self::input($fieldName, am($options, array('type' => 'text')));
	}
}