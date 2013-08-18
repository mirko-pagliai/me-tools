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
 * @package		MeTools.View.Helper
 */
class MeFormHelper extends FormHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));

	/**
	 * Creates a simple button. Rewrites <i>$this->Form->button()</i>
	 * @param string $caption The button label or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function button($caption, $options=array()) {
		//"type" option default "button"
		$options['type'] = empty($options['type']) ? 'button' : $options['type'];

		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->Html->cleanAttribute($options['class'].' btn');

		//Adds an icon to the label, if the "icon" option exists
		$caption = !empty($options['icon']) ? $this->Html->icon($options['icon']).$caption : $caption;
		unset($options['icon']);

		//Adds the "tooltip" rel
		$options['rel'] = empty($options['rel']) ? 'tooltip' : $this->Html->cleanAttribute($options['rel'].' tooltip');

		return parent::button($caption, $options);
	}
	
	/**
	 * Closes a form. Rewrites <i>$this->Form->end()</i> and use the <i>button()</i> method
	 *
	 * If $options is set, a submit button will be created. Options can be either a string or an array
	 * @param string $caption The submit button label or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function end($caption=null, $options=null) {
		//Unsets the "label" option 
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
	 * @param string $fieldName Field name. Should be "Modelname.fieldname"
	 * @param array $options Options
	 * @return string Html
	 */
	public function input($fieldName, $options=array()) {
		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		
		//"escape" options for errors default FALSE
		if(!empty($options['error']) && empty($options['error']['attributes']['escape']))
			$options['error']['attributes']['escape'] = false;
		
		//"after" option (text after the input)
		if(!empty($options['after']))
			$options['after'] = '<div class="after-input">'.trim($options['after']).'</div>';
		
		//"before" option (text before the input)
		if(!empty($options['before']))
			$options['before'] = '<div class="before-input">'.trim($options['before']).'</div>';
			
		//If the div class is not empty, prepend the "input" class and the input type
		if(!empty($options['div']['class']))
			$options['div']['class'] = $this->Html->cleanAttribute('input '.$this->getInputType($options).' '.$options['div']['class']);

		return parent::input($fieldName, $options);
	}
	
	/**
	 * Get the input type
	 * @param array $options Options
	 * @return string Type name
	 */
	protected function getInputType($options) {
		$options = parent::_parseOptions($options);
		return($options['type']);
	}

	/**
	 * Creates a button with a surrounding form that submits via POST. Rewrites <i>$this->Form->postButton()</i> 
	 * and uses <i>$this->Form->postLink()</i>
	 *
	 * This method creates a form element. So don't use this method in an already opened form
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function postButton($title, $url, $options=array(), $confirmMessage=false) {
		//"class" option default "btn"
		$options['class'] = empty($options['class']) ? 'btn' : $this->Html->cleanAttribute($options['class'].' btn');

		return $this->postLink($title, $url, $options, $confirmMessage);
	}
	
	/**
	 * Creates a link with a surrounding form that submits via POST. Rewrites <i>$this->Form->postLink()</i>
	 * 
	 * This method creates a form element. So don't use this method in an already opened form
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function postLink($title, $url=null, $options=array(), $confirmMessage=false) {
		//Adds an icon to the title, if the "icon" option exists
		$title = !empty($options['icon']) ? $this->Html->icon($options['icon']).$title : $title;
		unset($options['icon']);
		
		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		
		//Adds the tooltip, if there's the "tooptip" option
		if(!empty($options['tooltip'])) {
			$options['data-toggle'] = 'tooltip';
			$options['title'] = $options['tooltip'];
			unset($options['tooltip']);
		}
		
		return parent::postLink($title, $url, $options, $confirmMessage);
	}

	/**
	 * Creates a set of radio button inputs. Rewrites <i>$this->Form->radio()</i>
	 * @param string $fieldName Field name, should be "Modelname.fieldname"
	 * @param array $options Radio button options array
	 * @param array $attributes HTML attributes
	 * @return string Html
	 */
	public function radio($fieldName, $options=array(), $attributes=array()) {
		//"legend" attribute default FALSE
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
		//"empty" attribute default "Select a value" (if "default" and "selected" attributes are empty)
		$attributes['empty'] = empty($attributes['empty']) && empty($attributes['default']) && empty($attributes['selected']) ? __('Select a value') : $attributes['empty'];

		//"escape" attribute default FALSE
		$attributes['escape'] = empty($attributes['escape']) ? false : $attributes['escape'];
		
		return parent::select($fieldName, $options, $attributes);
	}

	/**
	 * Creates a submit button. Rewrites <i>$this->Form->Submit()</i> and uses the <i>$this->button()</i> method
	 * @param string $caption The label appearing on the submit button or an image
	 * @param array $options Options
	 * @return string Html
	 */
	public function submit($caption=null, $options=array()) {
		//Caption default "Submit"
		$caption = !empty($caption) ? $caption : __('Submit');
		
		//"type" must be "submit"
		$options['type'] = 'submit';

		//"icon" option default "icon-ok icon-white"
		$options['icon'] = empty($options['icon']) ? 'icon-ok icon-white' : $options['icon'];

		//"class" option default "btn btn-success"
		$options['class'] = empty($options['class']) ? 'btn btn-success' : $this->Html->cleanAttribute($options['class'].' btn');

		//If isset "div" option and this is false, returns the button
		if(isset($options['div']) && !$options['div'])
			return $this->button($caption, $options);
		//Else, returns the button in a wrapper
		else {
			//"div" option default "submit"
			$div = empty($options['div']) ? 'submit' : $this->Html->cleanAttribute($options['div'].' submit');
			unset($options['div']);

			return $this->Html->tag('div', $this->button($caption, $options), array('class' => $div));
		}
	}
}