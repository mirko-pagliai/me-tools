<?php
App::uses('FormHelper', 'View/Helper');

/**
 * Provide extended functionalities for forms.
 * 
 * Rewrites the {@link http://api.cakephp.org/2.4/class-FormHelper.html FormHelper}
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
class MeFormHelper extends FormHelper {
	/**
	 * Helpers used
	 * @var array Helpers
	 */
	public $helpers = array('MeTools.MeHtml');

	/**
	 * Creates a form. Rewrites <i>$this->Form->create()</i>
	 * @param string $model The model object which the form is being defined for
	 * @param array $options HTML attributes and options
	 * @return string A formatted opening FORM tag
	 */
	public function create($model=null, $options=array()) {
		return parent::create($model, $options);
	}

	/**
	 * Creates a (simple) button. Rewrites <i>$this->Form->button()</i>
	 * @param string $caption The label appearing on the button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function button($caption, $options=array()) {
		//"type" option default "button"
		$options['type'] = empty($options['type']) ? 'button' : $options['type'];

		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->MeHtml->cleanAttribute($options['class'].' btn');

		//Add bootstrap icon to the caption, if there's the "icon" option
		$caption = !empty($options['icon']) ? $this->MeHtml->icon($options['icon']).$caption : $caption;
		unset($options['icon']);

		//Add the "tooltip" rel
		$options['rel'] = empty($options['rel']) ? 'tooltip' : $this->MeHtml->cleanAttribute($options['rel'].' tooltip');

		return parent::button($caption, $options);
	}
	
	/**
	 * Closes a form. Rewrites <i>$this->Form->end()</i> and use the <i>button()</i> method
	 *
	 * If $options is set, a submit button will be created. Options can be either a string or an array
	 * @param string $caption The label appearing on the submit button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function end($caption=null, $options=null) {
		//If the "label" option is not empty, unset
		if(!empty($options['label']))
			unset($options['label']);

		$submit = !empty($caption) ? $this->submit($caption, $options) : null;
		
		return $submit.parent::end();
	}
	
	/**
	 * Checks and returns a value if this is not empty, else returns a default value.
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
	 * Creates an input element. Rewrites <i>$this->Form->input()</i>
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function input($fieldName, $options=array()) {
		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		//"label" option default FALSE
		$options['label'] = empty($options['label']) ? false : $options['label'];

		return parent::input($fieldName, $options);
	}

	/**
	 * Creates a button with a surrounding form that submits via POST. Rewrites <i>$this->Form->postButton()</i>
	 *
	 * This method creates a form element. So don't use this method in an already opened form.
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function postButton($title, $url, $options = array()) {
		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->MeHtml->cleanAttribute($options['class'].' btn');

		//Adds bootstrap icon to the title, if there's the "icon" option
		$title = !empty($options['icon']) ? $this->MeHtml->icon($options['icon']).$title : $title;
		unset($options['icon']);

		return parent::postButton($title, $url, $options);
	}

	/**
	 * Creates a set of radio button inputs. Rewrites <i>$this->Form->radio()</i>
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

		return parent::radio($fieldName, $options, $attributes);
	}

	/**
	 * Creates a select input. Rewrites <i>$this->Form->select()</i>
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Radio button options array
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function select($fieldName, $options=array(), $attributes=array()) {
		//"escape" attribute default false
		$attributes['escape'] = empty($attributes['escape']) ? false : $attributes['escape'];

		return parent::select($fieldName, $options, $attributes);
	}

	/**
	 * Creates a submit button. Rewrites <i>$this->Form->Submit()</i> and uses the <i>$this->button()</i> method
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
		$options['class'] = empty($options['class']) ? 'btn btn-success' : $this->MeHtml->cleanAttribute($options['class'].' btn');

		//If isset "div" option and this is false, returns the button
		if(isset($options['div']) && !$options['div'])
			return $this->button($caption, $options);
		//Else, returns the button in a wrapper
		else {
			//"div" option default "submit"
			$div = empty($options['div']) ? 'submit' : $this->MeHtml->cleanAttribute($options['div'].' submit');
			unset($options['div']);

			return $this->MeHtml->tag('div', $this->button($caption, $options), array('class' => $div));
		}
	}
}