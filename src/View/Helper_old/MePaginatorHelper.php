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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\View\Helper
 * @see			http://api.cakephp.org/2.6/class-PaginatorHelper.html PaginatorHelper
 */
App::uses('PaginatorHelper', 'View/Helper');

/**
 * Provides functionalities to the generation of pagers.
 * 
 * Rewrites {@link http://api.cakephp.org/2.6/class-PaginatorHelper.html PaginatorHelper}.
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
     * list($title, $options, $disabledTitle, $disabledOptions) = $this->_jump_link($title, $options, $disabledTitle, $disabledOptions);
     * </code>
     * @param string $title Link title
     * @param array $options Options for pagination link
     * @param string $disabledTitle Title when the link is disabled
     * @param array $disabledOptions Options for the disabled pagination link
     * @return array Passed arguments
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    private function _jump_link($title = NULL, $options = array(), $disabledTitle = NULL, $disabledOptions = array()) {
        //If the "disabled title" is empty, it will be set to the title
        $disabledTitle = empty($disabledTitle) ? $title : $disabledTitle;

		$options = $this->Html->_addOptionDefault('escape', FALSE, $options);
		$options = $this->Html->_addOptionDefault('tag', 'li', $options);

		$disabledOptions = $this->Html->_addOptionValue('class', 'disabled', $disabledOptions);
		$disabledOptions = $this->Html->_addOptionDefault('disabledTag', 'a', $disabledOptions);
		$disabledOptions = $this->Html->_addOptionDefault('tag', 'li', $disabledOptions);

        return array($title, $options, $disabledTitle, $disabledOptions);
    }

    /**
     * Returns a counter string for the paged result set.
     * 
     * By default, returns something like "1 - 20 of 50", which means that you're viewing records 1 to 20 of 50 total.
     * @param array $options Options for the counter string
     * @return string Counter string
     */
    public function counter($options = array()) {
        //"format" option default "{:start} - {:end} of {:count}"
		$options = $this->Html->_addOptionDefault('format', __d('me_tools', '%s - %s of %s', '{:start}', '{:end}', '{:count}'), $options);

        return parent::counter($options);
    }

    /**
     * Returns the counter string as disabled link.
     * @param array $options Options for the counter string
     * @return string Counter string as disabled link
	 * @uses counter()
	 * @uses MeHtmlHelper::li()
	 * @uses MeHtmlHelper::link()
     */
    public function counterLink($options = array()) {
        return $this->Html->li($this->Html->link(self::counter($options), '#'), array('class' => 'disabled'));
    }

    /**
     * Generates a "next" link for a set of paged records.
     * @param string $title Link title
     * @param array $options Options for pagination link
     * @param string $disabledTitle Title when the link is disabled
     * @param array $disabledOptions Options for the disabled pagination link
     * @return string A "next" link or $disabledTitle text if the link is disabled
     * @uses _jump_link()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function next($title = NULL, $options = array(), $disabledTitle = NULL, $disabledOptions = array()) {
		if(empty($disabledOptions) && !empty($options)) {
			$disabledOptions = $options;
			$disabledOptions = $this->Html->_addOptionValue('class', 'disabled', $disabledOptions);
		}
		
        //Uses `self::__jump->link()` to set arguments
        list($title, $options, $disabledTitle, $disabledOptions) = self::_jump_link($title, $options, $disabledTitle, $disabledOptions);

        return parent::next($title, $options, $disabledTitle, $disabledOptions);
    }

    /**
     * Returns a set of numbers for the paged result set uses a modulus to decide how many numbers .
     * to show on each side of the current page (default: 8).
     * @param array $options Options for the numbers.
     * @return string Numbers string
	 * @uses MeHtmlHelper::_addOptionDefault()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function numbers($options = array()) {
		$options = $this->Html->_addOptionValue('currentClass', 'active', $options);
		$options = $this->Html->_addOptionDefault('currentTag',  'a', $options);
		$options = $this->Html->_addOptionDefault('separator', FALSE, $options);
		$options = $this->Html->_addOptionDefault('tag', 'li', $options);

        return parent::numbers($options);
    }

    /**
     * Generates a "prev" link for a set of paged records.
     * @param string $title Link title
     * @param array $options Options for pagination link
     * @param string $disabledTitle Title when the link is disabled
     * @param array $disabledOptions Options for the disabled pagination link
     * @return string A "prev" link or $disabledTitle text if the link is disabled
     * @uses _jump_link()
	 * @uses MeHtmlHelper::_addOptionValue()
     */
    public function prev($title = NULL, $options = array(), $disabledTitle = NULL, $disabledOptions = array()) {
		if(empty($disabledOptions) && !empty($options)) {
			$disabledOptions = $options;
			$disabledOptions = $this->Html->_addOptionValue('class', 'disabled', $disabledOptions);
		}
		
        //Uses self::__jump->link() to set arguments
        list($title, $options, $disabledTitle, $disabledOptions) = self::_jump_link($title, $options, $disabledTitle, $disabledOptions);

        return parent::prev($title, $options, $disabledTitle, $disabledOptions);
    }
}