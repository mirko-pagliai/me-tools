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
 * @see			http://api.cakephp.org/3.2/class-Cake.View.Helper.PaginatorHelper.html PaginatorHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\PaginatorHelper as CakePaginatorHelper;

/**
 * Provides functionalities to the generation of pagers.
 * 
 * Rewrites {@link http://api.cakephp.org/3.2/class-Cake.View.Helper.PaginatorHelper.html PaginatorHelper}.
 */
class PaginatorHelper extends CakePaginatorHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Url', 'Number', 'Html' => ['className' => 'MeTools.Html']];
	
	/**
	 * Generates a "next" link for a set of paged records
	 * @param string $title Title for the link
	 * @param array $options Options for pagination link
	 * @return string A "next" link or a disabled link
	 */
	public function next($title = 'Next >>', array $options = []) {
		$title = $this->Html->_addIcon($title, $options);
		unset($options['icon'], $options['icon-align']);
		
		$options = addDefault('escape', FALSE, $options);
		
		return parent::next($title, $options);
	}
	
	/**
	 * Generates a "previous" link for a set of paged records
	 * @param string $title Title for the link
	 * @param array $options Options for pagination link
	 * @return string A "previous" link or a disabled link
	 */
	public function prev($title = '<< Previous', array $options = []) {
		$title = $this->Html->_addIcon($title, $options);
		unset($options['icon'], $options['icon-align']);
		
		$options = addDefault('escape', FALSE, $options);
		
		return parent::prev($title, $options);
	}
}