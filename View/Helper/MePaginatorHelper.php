<?php
/**
 * MePaginatorHelper
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
 * @author	Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license	http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link	http://git.novatlantis.it Nova Atlantis Ltd
 * @package	MeTools\View\Helper
 * @see		http://api.cakephp.org/2.4/class-PaginatorHelper.html PaginatorHelper
 * @see         http://repository.novatlantis.it/metools-sandbox/html/pagination Examples
 */
App::uses('PaginatorHelper', 'View/Helper');

/**
 * Provides functionalities to the generation of pagers.
 * 
 * Rewrites {@link http://api.cakephp.org/2.4/class-PaginatorHelper.html PaginatorHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = array('Paginator' => array('className' => 'MeTools.MePaginator'));
 * </code>
 */
class MePaginatorHelper extends PaginatorHelper {
	/**
	 * Helpers
	 * @var array 
	 */
	public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
	
	/**
	 * Internal function for jump links.
	 * 
	 * For example:
	 * <code>
	 * list($title, $options, $disabledTitle, $disabledOptions) = $this->__jump_link($title, $options, $disabledTitle, $disabledOptions);
	 * </code>
	 * @param string $title Link title
	 * @param array $options Options for pagination link
	 * @param string $disabledTitle Title when the link is disabled
	 * @param array $disabledOptions Options for the disabled pagination link
	 * @return array Passed arguments
	 */
	private function __jump_link($title = NULL, $options = array(), $disabledTitle = NULL, $disabledOptions = array()) {
		//If the "disabled title" is empty, it will be set to the title
		$disabledTitle = !empty($disabledTitle) ? $disabledTitle : $title;
		
		$options['escape'] = !empty($options['escape']) ? $options['escape'] : FALSE;
		$options['tag'] = !empty($options['tag']) ? $options['tag'] : 'li';
		
		$disabledOptions['class'] = !empty($disabledOptions['class']) ? $this->Html->__clean($disabledOptions['class']) : 'disabled';
		$disabledOptions['disabledTag'] = !empty($disabledOptions['disabledTag']) ? $disabledOptions['disabledTag'] : 'a';
		$disabledOptions['tag'] = !empty($disabledOptions['tag']) ? $disabledOptions['tag'] : 'li';
		
		return array($title, $options, $disabledTitle, $disabledOptions);
	}
	
	/**
	 * Returns a counter string for the paged result set.
	 * 
	 * By default, returns something like "1 - 20 of 50", which means that you're viewing records 1 to 20 of 50 total
	 * @param array $options Options for the counter string
	 * @return string Counter string
	 */
	public function counter($options=array()) {
		//"format" option default "{:start} - {:end} of {:count}"
		$options['format'] = !empty($options['format']) ? $options['format'] :  __d('me_tools', '%s - %s of %s', '{:start}', '{:end}', '{:count}');
		
		return parent::counter($options);
	}
	
	/**
	 * Returns the counter string as disabled link
	 * @param array $options Options for the counter string
	 * @return string Counter string as disabled link
	 */
	public function counterLink($options=array()) {
		return $this->Html->tag(
			'li', 
			$this->Html->link($this->counter($options), '#'), 
			array('class' => 'disabled')
		);
	}
	
	/**
	 * Generates a "next" link for a set of paged records.
	 * @param string $title Link title
	 * @param array $options Options for pagination link
	 * @param string $disabledTitle Title when the link is disabled
	 * @param array $disabledOptions Options for the disabled pagination link
	 * @return string A "next" link or $disabledTitle text if the link is disabled
	 * @uses __jump_link() jump links
	 */
	public function next($title = NULL, $options = array(), $disabledTitle = NULL, $disabledOptions = array()) {
		$title = !empty($title) ? $title : __d('me_tools', 'Next').' »';
		
		//Uses $this->__jump->link() to set arguments
		list($title, $options, $disabledTitle, $disabledOptions) = $this->__jump_link($title, $options, $disabledTitle, $disabledOptions);
		
		return parent::next($title, $options, $disabledTitle, $disabledOptions);
	}
	
	/**
	 * Returns a set of numbers for the paged result set uses a modulus to decide how many numbers 
	 * to show on each side of the current page (default: 8). Rewrites <i>$this->Paginator->numbers()</i>
	 * @param array $options Options for the numbers
	 * @return string Mumbers string
	 */
	public function numbers($options = array()) {
		$options['currentClass'] = !empty($options['currentClass']) ? $this->Html->__clean($options['currentClass']) : 'active';
		$options['currentTag'] = !empty($options['currentTag']) ? $options['currentTag'] : 'a';
		$options['separator'] = !empty($options['separator']) ? $options['separator'] : FALSE;
		$options['tag'] = !empty($options['tag']) ? $options['tag'] : 'li';
		
		return parent::numbers($options);
	}
	
	/**
	 * Generates a "prev" link for a set of paged records.
	 * @param string $title Link title
	 * @param array $options Options for pagination link
	 * @param string $disabledTitle Title when the link is disabled
	 * @param array $disabledOptions Options for the disabled pagination link
	 * @return string A "prev" link or $disabledTitle text if the link is disabled
	 * @uses __jump_link() jump links
	 */
	public function prev($title = NULL, $options = array(), $disabledTitle = NULL, $disabledOptions = array()) {
		$title = !empty($title) ? $title : '« '.__d('me_tools', 'Previous');
		
		//Uses $this->__jump->link() to set arguments
		list($title, $options, $disabledTitle, $disabledOptions) = $this->__jump_link($title, $options, $disabledTitle, $disabledOptions);
		
		return parent::prev($title, $options, $disabledTitle, $disabledOptions);
	}
}