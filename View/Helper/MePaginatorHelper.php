<?php
App::uses('PaginatorHelper', 'View/Helper');

/**
 * Provides functionalities to the generation of pagers.
 * 
 * Extends {@link http://api.cakephp.org/2.4/class-PaginatorHelper.html PaginatorHelper}.
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
class MePaginatorHelper extends PaginatorHelper {
	/**
	 * Helpers
	 * @var array 
	 */
	public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
	
	/**
	 * Internal function per jump links. It's used by <i>$this->next()</i> and <i>$this->prev()</i>
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
	private function __jump_link($title = null, $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		//If the "disabled title" is empty, it will be set to the title
		$disabledTitle = !empty($disabledTitle) ? $disabledTitle : $title;
		
		//"escape" option default FALSE
		$options['escape'] = !empty($options['escape']) ? $options['escape'] : false;
		//"tag" option default "li"
		$options['tag'] = !empty($options['tag']) ? $options['tag'] : 'li';
		
		//"class" disabled option default "disabled"
		$disabledOptions['class'] = !empty($disabledOptions['class']) ? $this->Html->cleanAttribute($disabledOptions['class']) : 'disabled';
		//"disabledTag" disabled option default "a"
		$disabledOptions['disabledTag'] = !empty($disabledOptions['disabledTag']) ? $disabledOptions['disabledTag'] : 'a';
		//"tag" disabled option default "li"
		$disabledOptions['tag'] = !empty($disabledOptions['tag']) ? $disabledOptions['tag'] : 'li';
		
		return array($title, $options, $disabledTitle, $disabledOptions);
	}
	
	/**
	 * Returns a counter string for the paged result set. Rewrites <i>$this->Paginator->counter()</i>
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
	 * Generates a "next" link for a set of paged records. Rewrites <i>$this->Paginator->next()</i>
	 * @param string $title Link title
	 * @param array $options Options for pagination link
	 * @param string $disabledTitle Title when the link is disabled
	 * @param array $disabledOptions Options for the disabled pagination link
	 * @return string A "next" link or $disabledTitle text if the link is disabled
	 */
	public function next($title = null, $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		//Title default
		$title = !empty($title) ? $title : __d('me_tools', 'next').' »';
		
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
		//"currentClass" option default "disabled"
		$options['currentClass'] = !empty($options['currentClass']) ? $this->Html->cleanAttribute($options['currentClass']) : 'disabled';
		//"currentTag" option default "a"
		$options['currentTag'] = !empty($options['currentTag']) ? $options['currentTag'] : 'a';
		//"separator" option default FALSE
		$options['separator'] = !empty($options['separator']) ? $options['separator'] : false;
		//"tag" option default "li"
		$options['tag'] = !empty($options['tag']) ? $options['tag'] : 'li';
		
		return parent::numbers($options);
	}
	
	/**
	 * Generates a "prev" link for a set of paged records. Rewrites <i>$this->Paginator->prev()</i>
	 * @param string $title Link title
	 * @param array $options Options for pagination link
	 * @param string $disabledTitle Title when the link is disabled
	 * @param array $disabledOptions Options for the disabled pagination link
	 * @return string A "prev" link or $disabledTitle text if the link is disabled
	 */
	public function prev($title = null, $options = array(), $disabledTitle = null, $disabledOptions = array()) {
		//Title default
		$title = !empty($title) ? $title : '« '.__d('me_tools', 'previous');
		
		//Uses $this->__jump->link() to set arguments
		list($title, $options, $disabledTitle, $disabledOptions) = $this->__jump_link($title, $options, $disabledTitle, $disabledOptions);
		
		return parent::prev($title, $options, $disabledTitle, $disabledOptions);
	}
}