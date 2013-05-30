<?php
App::uses('MeToolsAppHelper', 'MeTools.View/Helper');

/**
 * Provide extended functionalities for forms.
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
class MeFormHelper extends MeToolsAppHelper {
	/**
	 * Helpers used
	 * @var array Helpers name
	 */
	public $helpers = array('Form', 'MeTools.MeHtml');

	/**
	 * Create a form. Rewrite <i>$this->Form->create()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_create CakePHP Api}
	 * @param string $model The model object which the form is being defined for
	 * @param array $options HTML attributes and options
	 * @return string A formatted opening FORM tag
	 */
	public function create($model=null, $options=array()) {
		return $this->Form->create($model, $options);
	}

	/**
	 * Create a (simple) button. Rewrite <i>$this->Form->button()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_submit CakePHP Api}
	 * @param string $caption The label appearing on the button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function button($caption, $options=array()) {
		//"type" option default "button"
		$options['type'] = empty($options['type']) ? 'button' : $options['type'];

		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->_cleanAttribute($options['class'].' btn');

		//Add bootstrap icon to the caption, if there's the "icon" option
		$caption = !empty($options['icon']) ? $this->MeHtml->icon($options['icon']).$caption : $caption;
		unset($options['icon']);

		//Add the "tooltip" rel
		$options['rel'] = empty($options['rel']) ? 'tooltip' : $this->_cleanAttribute($options['rel'].' tooltip');

		return $this->Form->button($caption, $options);
	}
	
	/**
	 * Close a form. Rewrite <i>$this->Form->end()</i> and use the <i>button()</i> method
	 *
	 * If $options is set, a submit button will be created. Options can be either a string or an array
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_end CakePHP Api}
	 * @param string $caption The label appearing on the submit button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function end($caption=null, $options=null) {
		//If the "label" option is not empty, unset
		if(!empty($options['label']))
			unset($options['label']);

		$submit = !empty($caption) ? $this->submit($caption, $options) : null;
		
		return $submit.$this->Form->end();
	}
	
	/**
	 * Check and return a value if this is not empty, else return a default value.
	 * 
	 * It can be useful with the "selected" option, to get a value if this exists or use a default. For example:  
	 * <pre>'selected' => @$this->MeForm->getDefault($this->request->data['User']['group'], 'user')</pre>
	 * Set the "selected" option to 
	 * <pre>$this->request->data['User']['group']</pre>
	 * if this exists (for example, if the form has already been sent), else it will use the "user" default value.
	 * 
	 * It must be used with the "@" operator, otherwise it will generate a notice.
	 * @param string $value Value to check
	 * @param string $default Default value
	 * @return string Value to check if this is not empty, else default value
	 */
	public function getDefault($value, $default) {
		return !empty($value) ? $value : $default;
	}

	/**
	 * Create an input element. Rewrite <i>$this->Form->input()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_input CakePHP Api}
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function input($fieldName, $options=array()) {
		//"escape" option default false
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		//"label" option default false
		$options['label'] = empty($options['label']) ? false : $options['label'];

		return $this->Form->input($fieldName, $options);
	}

	/**
	 * Create a button with a surrounding form that submits via POST. Rewrite <i>$this->Form->postButton()</i>
	 *
	 * This method creates a form element. So don'è use this method in an already opened form.
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_postButton CakePHP Api}
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function postButton($title, $url, $options = array()) {
		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->_cleanAttribute($options['class'].' btn');

		//Add bootstrap icon to the title, if there's the "icon" option
		$title = !empty($options['icon']) ? $this->MeHtml->icon($options['icon']).$title : $title;
		unset($options['icon']);

		//Add the "tooltip" data-toggle
		$options['data-toggle'] = empty($options['data-toggle']) ? 'tooltip' : $this->_cleanAttribute($options['data-toggle'].' tooltip');

		return $this->Form->postButton($title, $url, $options);
	}

	/**
	 * Create a set of radio button inputs. Rewrite <i>$this->Form->radio()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_radio CakePHP Api}
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Radio button options array
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function radio($fieldName, $options=array(), $attributes=array()) {
		//"legend" attribute default false
		$attributes['legend'] = empty($attributes['legend']) ? false : $attributes['legend'];

		//"separator" attribute default "<br />"
		$attributes['separator'] = empty($attributes['separator']) ? '<br />' : $attributes['separator'];

		return $this->Form->radio($fieldName, $options, $attributes);
	}

	/**
	 * Create a select input. Rewrite <i>$this->Form->select()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_radio CakePHP Api}
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Radio button options array
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function select($fieldName, $options=array(), $attributes=array()) {
		//"escape" attribute default false
		$attributes['escape'] = empty($attributes['escape']) ? false : $attributes['escape'];

		return $this->Form->select($fieldName, $options, $attributes);
	}

	/**
	 * Create a submit button. Rewrite <i>$this->Form->Submit()</i> and use the <i>button()</i> method
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-FormHelper.html#_submit CakePHP Api}
	 * @param string $caption The label appearing on the submit button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function submit($caption, $options=array()) {
		//"type" is "submit"
		$options['type'] = 'submit';

		//"icon" option default "icon-ok icon-white"
		$options['icon'] = empty($options['icon']) ? 'icon-ok icon-white' : $options['icon'];

		//"class" option default "btn btn-success"
		$options['class'] = empty($options['class']) ? 'btn btn-success' : $this->_cleanAttribute($options['class'].' btn');

		//If isset "div" option and this is false, return the button
		if(isset($options['div']) && !$options['div'])
			return $this->button($caption, $options);
		//Else, return the button in a wrapper
		else {
			//"div" option default "submit"
			$div = empty($options['div']) ? 'submit' : $this->_cleanAttribute($options['div'].' submit');
			unset($options['div']);

			return $this->MeHtml->tag('div', $this->button($caption, $options), array('class' => $div));
		}
	}
}