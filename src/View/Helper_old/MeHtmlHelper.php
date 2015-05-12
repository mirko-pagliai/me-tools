<?php

/**
 * MeHtmlHelper
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
 * @see			http://api.cakephp.org/2.6/class-HtmlHelper.html HtmlHelper
 */
App::uses('HtmlHelper', 'View/Helper');

/**
 * Provides functionalities for HTML code.
 * 
 * Rewrites the {@link http://api.cakephp.org/2.6/class-HtmlHelper.html HtmlHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = ['Html' => ['className' => 'MeTools.MeHtml']];
 * </code>
 */
class MeHtmlHelper extends HtmlHelper {

    /**
     * Returns the breadcrumb as a links sequence.
     * 
     * Note that it's better to use the `getCrumbList()` method, which offers better compatibility with Bootstrap.
     * @param string $separator Crumbs separator
     * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are "text", "url" and "icon"
     * @return string Html code
     * @see getCrumbList()
	 * @uses div()
	 * @uses span()
     */
    public function getCrumbs($separator = '/', $startText = FALSE) {
        if(is_array($startText) && empty($startText['text']))
            $startText['text'] = FALSE;

        $separator = self::span(trim($separator), array('class' => 'separator'));

        return self::div('breadcrumb', parent::getCrumbs($separator, $startText));
    }

    /**
     * Returns the breadcrumb as a (x)html list.
     * 
     * Note that the `lastClass` option is set automatically as required by Bootstrap and separators (`separator` option) are
	 * automatically  added by Bootstrap in CSS through :before and content. So you should not use any of these two options.
	 * @param array $options Array of options and HTML attributes
     * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are `text`, `url` and `icon`
     * @return string Html code
	 * @uses _addOptionValue()
     */
    public function getCrumbList($options = array(), $startText = FALSE) {
        if(is_array($startText) && empty($startText['text']))
            $startText['text'] = FALSE;

		$options = self::_addOptionValue('class', 'breadcrumb', $options);
		$options = self::_addOptionValue('escape', FALSE, $options);
		
        //Separators are automatically added by Bootstrap in CSS through :before and content.
        unset($options['separator']);

        return parent::getCrumbList(am($options, array('lastClass' => 'active')), $startText);
    }

    /**
     * Returns a tip block.
     * 
     * By default, the tip block will have a title. To change the title, use the `title` option. 
	 * If the `title` option is  If you don't want to have a title, the `title` option should be `FALSE`.
     * @param string $text Tip text
     * @param array $options HTML attributes
     * @return Html code
	 * @uses _addIcons()
	 * @uses _addOptionValue()
	 * @uses _addOptionDefault()
	 * @uses div()
	 * @uses h4()
	 * @uses para()
     */
    public function tip($text, $options = array()) {
        $text = is_array($text) ? $text : array($text);
        array_walk($text, function(&$v) {
            $v = self::para(NULL, $v);
        });
        $text = self::div('tip-text', implode(PHP_EOL, $text));
		
		if(!isset($options['title']) || $options['title']) {
			$options = self::_addOptionDefault('title', __d('me_tools', 'Tip'), $options);
			$options = self::_addOptionDefault('icon', 'magic', $options);
			$options['title'] = self::_addIcons($options['title'], $options);
			
			$text = self::h4($options['title'], array('class' => 'tip-title')).PHP_EOL.$text;
		}
		
		unset($options['icon'], $options['title']);        

		$options = self::_addOptionValue('class', 'tip', $options);
		
        return self::div($options['class'], $text, $options);
    }
}