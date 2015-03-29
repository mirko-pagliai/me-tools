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
 * @see			http://api.cakephp.org/2.6/class-FormHelper.html FormHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\FormHelper;
use Cake\View\View;

/**
 * MeForm helper
 */
class MeFormHelper extends FormHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.MeHtml'], 'Url'];
	
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
	 * @uses MeHtmlHelper::_addButtonClass()
	 * @uses MeHtmlHelper::_addValue()
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
	 * @uses MeHtmlHelper::_addDefault()
	 * @uses MeHtmlHelper::_addIcon()
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
}