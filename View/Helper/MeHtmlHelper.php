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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\View\Helper
 * @see			http://api.cakephp.org/2.5/class-HtmlHelper.html HtmlHelper
 */
App::uses('HtmlHelper', 'View/Helper');

/**
 * Provides functionalities for HTML code.
 * 
 * Rewrites {@link http://api.cakephp.org/2.5/class-HtmlHelper.html HtmlHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
 * </code>
 */
class MeHtmlHelper extends HtmlHelper {
	/**
	 * Method that is called automatically when the method doesn't exist.
	 * If you pass no more than two parameters, it tries to generate a html tag with the name of the method.
	 * Otherwise, it provides non fatal errors on missing method calls.
	 * @param string $method Method to invoke
	 * @param array $params Array of params for the method
	 * @return string Html, tag
	 * @see http://api.cakephp.org/2.5/class-Helper.html#___call CakePHP Api
	 */
	public function __call($method, $params) {		
		if(count($params) <= 2)
			return self::tag($method, empty($params[0]) ? NULL : $params[0], empty($params[1]) ? NULL : $params[1]);
		
		parent::__call($method, $params);
	}
	
    /**
     * Cleans values to be used as html attributes, removing blank spaces and duplicates. For example:
     * <pre>a a b  b c d e e e</pre>
     * will become:
     * <pre>a b c d e</pre>
     * @param mixed $value Value as string or array
     * @return string Cleaned value
     */
    public function _clean() {
        $values = func_get_args();

        if(empty($values))
            return NULL;

        //If an argument is an array, turns it into a string
        $values = array_map(function($v) {
            return !is_array($v) ? $v : implode(' ', $v);
        }, $values);
        //Turns all arguments into a string
        $values = implode(' ', $values);

        return implode(' ', array_unique(array_filter(explode(' ', $values))));
    }

    /**
     * Get classes for a button
     * @param array $option Button options
     * @return string Button classes
     */
    public function _getBtnClass($option) {
        if(empty($option['class']))
            return 'btn btn-default';

        //If "class" doesn't contain a button style, adds "btn-default" to class
        if(!preg_match('/btn-(default|primary|success|info|warning|danger)/', $class = $option['class']))
            return self::_clean('btn', 'btn-default', $class);
        else
            return self::_clean('btn', $class);
    }

    /**
     * Parses and handles title and options used to create a link or a button to open a dropdown.
     * 
     * You should not use this method directly, but `buttonDropdown()` or `linkDropdown()`.
     * @param string $title Link/button title
     * @param array $options HTML attributes
     * @return array Title and options
     */
    private function _parseLinkDropdown($title, $options = array()) {
        $options['class'] = empty($options['class']) ? 'dropdown-toggle' : self::_clean('dropdown-toggle', $options['class']);
        $options['data-toggle'] = empty($options['data-toggle']) ? 'dropdown' : self::_clean('dropdown', $options['data-toggle']);

        $title .= '&nbsp;'.self::icon('fa-caret-down');

        return array($title, $options);
    }
	
	/**
	 * Handles the options of an element to add tooltips
	 * @param array $options Element options
	 * @return array Options
	 */
	private function _setTooltip($options) {
		if(!empty($options['tooltip'])) {
			$options['data-toggle'] = 'tooltip';
			$options['title'] = $options['tooltip'];
			unset($options['tooltip']);
		}
		
		return $options;
	}

    /**
     * Returns an audio element
     * @param string|array $path File path, relative to the `webroot/{$options['pathPrefix']}` directory
     * or an array where each item itself can be a path string or an array containing `src` and `type` keys.
     * @param array $options Array of HTML attributes
     * @return string Html, audio tag
     * @see http://repository.novatlantis.it/metools-sandbox/html/audiovideo Examples
     * @uses media()
     */
    public function audio($path, $options = array()) {
        return self::media($path, am($options, array('tag' => 'audio')));
    }

    /**
     * Creates a badge, according to the Bootstrap component.
     * @param string $text Badge text
     * @param array $options HTML attributes
     * @return string Html, badge element
     * @see http://getbootstrap.com/components/#badges Bootstrap documentation
     * @see http://repository.novatlantis.it/metools-sandbox/html/labelbadges Examples
     */
    public function badge($text, $options = array()) {
        $options['class'] = empty($options['class']) ? 'badge' : self::_clean('badge', $options['class']);

        return self::tag('span', $text, $options);
    }

    /**
     * Creates a link with the appearance of a button.
     * 
     * This method creates a link with the appearance of a button.
     * To create a POST button, you should use the `postButton()` method provided by the `MeForm` helper.
     * Instead, to create a normal button, you should use the `button()` method provided by the `MeForm` helper.
     * @param string $title Link title
     * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
     * @param array $options HTML attributes
     * @param string $confirmMessage JavaScript confirmation message
     * @return string Html, link
     * @see MeFormHelper::button(), MeFormHelper::postButton()
     * @see http://repository.novatlantis.it/metools-sandbox/html/buttonslinks Examples
     * @uses link()
     */
    public function button($title, $url = '#', $options = array(), $confirmMessage = FALSE) {
        $options['role'] = empty($options['role']) ? 'button' : self::_clean('button', $options['role']);

        return self::link($title, $url, am($options, array('class' => self::_getBtnClass($options))), $confirmMessage);
    }

    /**
     * Creates a button to open a dropdown menu, according to the Bootstrap component.
     * 
     * Note that this method creates only a button. To create a dropdown menu, you should use the `dropdown()` method.
     * @param string $title Button title
     * @param array $options HTML attributes
     * @return string Html, button
     * @see dropdown()
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
     * @see http://repository.novatlantis.it/metools-sandbox/html/dropdown Examples
     * @uses _parseLinkDropdown() to parse options
     * @uses button() to get the button
     */
    public function buttonDropdown($title, $options = array()) {
        //Backward compatibility, in which case they are 3 passed arguments
        if(func_num_args() === 3)
            $options = func_get_arg(2);

        list($title, $options) = self::_parseLinkDropdown($title, $options);

        return self::button($title, '#', $options);
    }

    /**
     * Adds a css file to the layout.
     *
     * If it's used in the layout, you should set the `inline` option to `TRUE`
     * @param mixed $path Css filename or an array of css filenames
     * @param array $options HTML attributes
     * @return string Html, `link` or `style` tag
     */
    public function css($path, $options = array()) {
        /**
         * From 2.4, the API for HtmlHelper::css() has been changed and the 
         * method accepts only two options. This code ensures backward compatibility.
         */
        if(!is_array($options)) {
            $rel = $options;
            $options = array();
            if($rel)
                $options['rel'] = $rel;

            if(func_num_args() > 2)
                $options = func_get_arg(2) + $options;

            unset($rel);
        }

        $options['inline'] = empty($options['inline']) ? FALSE : $options['inline'];
		
		if(is_array($path)) {
			$out = NULL;
			foreach($path as $sPath)
				$out .= parent::css($sPath, $options);
			
			return $out;
		}

        return parent::css($path, $options);
    }

    /**
     * Ends capturing output for a CSS block.
     * 
     * To start capturing output, see the `cssStart()` method.
     * @see cssStart()
     */
    public function cssEnd() {
        $this->_View->end();
    }

    /**
     * Starts capturing output for a CSS block.
     * 
     * To end capturing output, see the `cssEnd()` method.
     * @see cssEnd()
     */
    public function cssStart() {
        $this->_View->start('css');
    }

    /**
     * Creates a dropdown, according to the Bootstrap component. For example:
     * <code>
     * <div class="dropdown">
     *    <?php
     *       echo $this->Html->buttonDropdown('Open the dropdown', array('icon' => 'fa-bell'));
     *       echo $this->Html->dropdown(array(
     *          $this->Html->link('Github', 'http://github.com', array('icon' => 'fa-github')),
     *          'divider',
     *          $this->Html->link('Stack Overflow', 'http://stackoverflow.com', array('icon' => 'fa-stack-overflow'))
     *       ));
     *    ?>
     * </div>
     * </code>
     * @param array $links Array of links for the dropdown (you should use the `link()` method for each link) or "divider" to create a divider
     * @param array $ulOptions Options for the dropdown
     * @param array $itemOptions Options for each item (`li`) of the dropdown
     * @return string Html, dropdown menu
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
     * @see http://repository.novatlantis.it/metools-sandbox/html/dropdown Examples
     * @uses ul() to create the ul element
     */
    public function dropdown($links = array(), $ulOptions = array(), $itemOptions = array()) {
        $ulOptions['class'] = empty($ulOptions['class']) ? 'dropdown-menu' : self::_clean('dropdown-menu', $ulOptions['class']);
        $ulOptions['role'] = empty($ulOptions['role']) ? 'menu' : self::_clean('menu', $ulOptions['role']);
        $itemOptions['role'] = empty($itemOptions['role']) ? 'presentation' : self::_clean('presentation', $itemOptions['role']);

        //Sets eventual separators
        array_walk($links, function(&$v) {
            if($v === 'divider' || $v === 'separator')
                $v = array(NULL, array('class' => 'divider'));
        });

        return self::ul($links, $ulOptions, $itemOptions);
    }

    /**
     * Returns the breadcrumb as links sequence.
     * 
     * Note that it's better to use the `getCrumbList()` method, which offers better compatibility with Bootstrap.
     * @param string $separator Crumbs separator
     * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are "text", "url" and "icon"
     * @return string Html, breadcrumb
     * @see getCrumbList()
     * @see http://repository.novatlantis.it/metools-sandbox/html/breadcrumb Examples
     */
    public function getCrumbs($separator = '/', $startText = FALSE) {
        if(is_array($startText) && empty($startText['text']))
            $startText['text'] = FALSE;

        $separator = $this->tag('span', trim($separator), array('class' => 'separator'));

        return $this->div('breadcrumb', parent::getCrumbs($separator, $startText));
    }

    /**
     * Returns the breadcrumb as a (x)html list.
     * 
     * Note that the `lastClass` option is set automatically as required by Bootstrap and separators (`separator` option) are automatically 
     * added by Bootstrap in CSS through :before and content. So you should not use any of these two options.
     * @param array $options HTML attributes
     * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are "text", "url" and "icon"
     * @return string Html, breadcrumb
     * @see http://repository.novatlantis.it/metools-sandbox/html/breadcrumb Examples
     */
    public function getCrumbList($options = array(), $startText = FALSE) {
        if(is_array($startText) && empty($startText['text']))
            $startText['text'] = FALSE;

        $options['class'] = empty($options['class']) ? 'breadcrumb' : self::_clean('breadcrumb', $options['class']);
		$options['escape'] = empty($options['escape']) ? FALSE : $options['escape'];
		
        //Separators are automatically added by Bootstrap in CSS through :before and content.
        unset($options['separator']);

        return parent::getCrumbList(am($options, array('lastClass' => 'active')), $startText);
    }
	
	/**
	 * Creates an heading. 
	 * 
	 * This method is useful if you want to create an heading with a secondary text.
	 * In this case you have to use the option "small".
	 * 
	 * By default, this method creates an h2 tag. To create a different tag, you have to use the "type" option.
     * @param string $text heading content
	 * @param array $options HTML attributes
     * @return string Html, heading tag
	 */
	public function heading($text = NULL, $options = array()) {
		$type = empty($options['type']) ? 'h2' : $options['type'];
		
        $options['class'] = empty($options['class']) ? 'heading' : self::_clean('heading', $options['class']);
		
		if(!empty($options['small']))
			$text = sprintf('%s %s', $text, self::tag('small', $options['small']));
		
		unset($options['type'], $options['small']);
		
		return self::tag($type, $text, $options);
	}
	
	/**
	 * Creates an "hr" (horizontal rule) tag.
     * @param array $options HTML attributes
     * @return string Html, hr element
	 */
	public function hr($options = array()) {
		return self::tag('hr', NULL, $options);
	}

	/**
     * Returns icons. Examples:
     * <code>
     * echo $this->Html->icon('home');
     * </code>
     * <code>
     * echo $this->Html->icon(array('hand-o-right', '2x'));
     * </code>
     * @param mixed $icons Icons as string or array
     * @param array $options HTML attributes
     * @return string Html, icons
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
     * @see http://repository.novatlantis.it/metools-sandbox/html/icons Examples
	 */
    public function icon($icons = NULL, $options = array()) {
        //Adds the "fa" class and prepende the string "fa-" to any other class
        $icons = preg_replace('/(?<![^ ])(?=[^ ])(?!fa)/', 'fa-', self::_clean('fa', $icons));
		
		$options['class'] = empty($options['class']) ? $icons : self::_clean($icons, $options['class']);
		
        return self::tag('i', ' ', $options);
    }

    /**
     * Alias for `icon()` method
     * @see icon()
     */
    public function icons() {
        return call_user_func_array(array('MeHtmlHelper', 'icons'), func_get_args());
    }

    /**
     * Creates an IMG element.
     * @param string $path Image path (will be relative to `app/webroot/img/`)
     * @param $options HTML attributes
     * @return string Html, image
     * @see http://repository.novatlantis.it/metools-sandbox/html/images Examples
     */
    public function image($path, $options = array()) {
        $options['class'] = empty($options['class']) ? 'img-responsive' : self::_clean('img-responsive', $options['class']);

        return parent::image($path, $options);
    }

    /**
     * Alias for `image()` method
     * @see image()
     */
    public function img() {
        return call_user_func_array(array('MeHtmlHelper', 'image'), func_get_args());
    }

    /**
     * Alias for `script()` method
     * @see script()
     */
    public function js() {
        return call_user_func_array(array('MeHtmlHelper', 'script'), func_get_args());
    }

    /**
     * Create a label, according to the Bootstrap component.
     * 
     * This method creates only a label element. Not to be confused with the `label()` method of the `MeForm` helper, which 
     * creates a label for a form input.
     * 
     * Supported type are: `default`, `primary`, `success`, `info`, `warning` and `danger`
     * @param string $text Label text
     * @param array $options HTML attributes of the list tag
     * @param string $type Label type
     * @return string Html, label
     * @see http://getbootstrap.com/components/#labels Bootstrap documentation
     * @see http://repository.novatlantis.it/metools-sandbox/html/labelbadges Examples
     */
    public function label($text, $options = array(), $type = 'default') {
		
        $type = self::_clean('label', 'label-'.$type);
        $options['class'] = empty($options['class']) ? $type : self::_clean($type, $options['class']);

        return self::tag('span', $text, $options);
    }

    /**
     * Returns element list (`li`) out of an array.
     * 
     * If the element is an array, then the firse value is the element and the second value are options.
     * @param array $elements Element list
     * @param array $options HTML attributes of the list tag
     * @return string Html, element list
     * @see http://repository.novatlantis.it/metools-sandbox/html/lists Examples
     */
    public function li($elements, $options = array()) {
        $html = '';

        //If it's only one element
        if(!is_array($elements)) {
            $elements = empty($options['icon']) ? $elements : self::icon($options['icon']).$elements;
            unset($options['icon']);
            return self::tag('li', $elements, $options);
        }

        foreach($elements as $element) {
            //If the element is an array, then the first value is the element and the second value are element options
            if(is_array($element)) {
                $elementOption = am($options, $element[1]);
                $element = $element[0];
            }
            else
                $elementOption = $options;

            $element = empty($elementOption['icon']) ? $element : self::icon($elementOption['icon']).$element;
            unset($elementOption['icon']);

            $html .= self::tag('li', $element, $elementOption);
        }

        return $html;
    }

    /**
     * Creates a link.
     * 
     * Note: this method creates a normal link.
     * To create a POST link, you should use the `postLink()` method of the `MeForm` helper.
     * @param string $title Link title
     * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
     * @param array $options HTML attributes
     * @param string $confirmMessage JavaScript confirmation message
     * @return string Html, link
     * @see http://repository.novatlantis.it/metools-sandbox/html/buttonslinks Examples
     */
    public function link($title, $url = '#', $options = array(), $confirmMessage = FALSE) {
		$options = self::_setTooltip($options);
		
        $options['escape'] = empty($options['escape']) ? FALSE : $options['escape'];
		$options['title'] = empty($options['title']) ? strip_tags($title) : $options['title'];
        $title = empty($options['icon']) ? $title : self::icon($options['icon']).$title;
        unset($options['icon']);
		
		 //If "class" contains a button style, adds "btn" to class
        if(!empty($options['class']) && preg_match('/btn-(default|primary|success|info|warning|danger)/', $options['class']))
			$options['class'] = self::_clean('btn', $options['class']);

        return parent::link($title, $url, $options, $confirmMessage);
    }

    /**
     * Alias for `button()`
     * @see button()
     */
    public function linkButton() {
        return call_user_func_array(array('MeHtmlHelper', 'button'), func_get_args());
    }

    /**
     * Creates a link to open a dropdown menu, according to the Bootstrap component.
     * 
     * Note that this method creates only a link. To create a dropdown menu, you should use the `dropdown()` method.
     * @param string $title Link title
     * @param array $options HTML attributes
     * @return string Html, link
     * @see dropdown()
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
     * @see http://repository.novatlantis.it/metools-sandbox/html/dropdown Examples
     * @uses _parseLinkDropdown() to parse options
     * @uses link() to get the link
     */
    public function linkDropdown($title, $options = array()) {
        //Backward compatibility, in which case they are 3 passed arguments
        if(func_num_args() === 3)
            $options = func_get_arg(2);
		
		$options['title'] = empty($options['title']) ? strip_tags($title) : $options['title'];
		
        list($title, $options) = self::_parseLinkDropdown($title, $options);

        return self::link($title, '#', $options);
    }

    /**
     * Returns an audio/video element
     * @param string|array $path File path, relative to the `webroot/{$options['pathPrefix']}` directory
     * or an array where each item itself can be a path string or an array containing `src` and `type` keys.
     * @param array $options Array of HTML attributes
     * @return string Html, audio or video tag
     */
    public function media($path, $options = array()) {
        $options['controls'] = isset($options['controls']) && empty($options['controls']) ? FALSE : TRUE;

        return parent::media($path, $options);
    }

    /**
     * Creates a meta tag. 
     *
     * For a custom meta tag, the first parameter should be set to an array. For example:
     * <code>echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex'));</code>
     * @param string $type The title of the external resource
     * @param mixed $url The address of the external resource or string for content attribute
     * @param array $options Other attributes for the generated tag
     * @return string Html, meta tag
     * @see http://repository.novatlantis.it/metools-sandbox/html/audiovideo Examples
     */
    public function meta($type, $url = NULL, $options = array()) {
        $options['inline'] = empty($options['inline']) ? FALSE : $options['inline'];

        return parent::meta($type, $url, $options);
    }

    /**
     * Returns a list (`ol`/`ul`) out of an array.
     * @param array $list Element list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @param string $tag Type of list tag (ol/ul)
     * @return string Html, ol/ul list
     * @see http://repository.novatlantis.it/metools-sandbox/html/lists Examples
     */
    public function nestedList($list, $options = array(), $itemOptions = array(), $tag = 'ul') {
        if(!empty($itemOptions['icon'])) {
            $options['class'] = empty($options['class']) ? 'fa-ul' : self::_clean('fa-ul', $options['class']);
            array_walk($list, function(&$v, $k, $icon) {
                $v = self::icon($icon).$v;
            }, $itemOptions['icon']);
            unset($itemOptions['icon']);
        }

        return self::tag($tag, self::li($list, $itemOptions), $options);
    }

    /**
     * Returns an ordered list (`ol`) out of an array.
     * @param array $list Element list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string Html, ordered list
     * @see http://repository.novatlantis.it/metools-sandbox/html/lists Examples
     * @uses nestedList() to create the list
     */
    public function ol($list, $options = array(), $itemOptions = array()) {
        return self::nestedList($list, $options, $itemOptions, 'ol');
    }

    /**
     * Returns a formatted P tag.
     * @param type $class Class name of the element
     * @param string $text Paragraph text
     * @param array $options HTML attributes
     * @return string Html, P tag.
     */
    public function para($class, $text, $options = array()) {
        $text = empty($options['icon']) ? $text : self::icon($options['icon']).$text;
        unset($options['icon']);

        return parent::para($class, $text, $options);
    }
	
	/**
	 * Creates a pre tag.
	 * 
	 * For use with SyntaxHighlighter, you can use the `brush` option.
     * @param string $text Pre text
     * @param array $options HTML attributes
     * @return string Html, pre element
	 */
	public function pre($text, $options = array()) {
		if(!empty($options['brush'])) {
			$options['class'] = sprintf('brush: %s', $options['brush']);
			unset($options['brush']);
		}
		
		return self::tag('pre', $text, $options);
	}

    /**
     * Adds a js file to the layout.
     * 
     * If it's used in the layout, you should set the `inline` option to `TRUE`
     * @param mixed $url Javascript files as string or array
     * @param array $options HTML attributes
     * @return mixed String of <script /> tags or NULL if `$inline` is FALSE or if `$once` is TRUE and the file has been included before
     */
    public function script($url, $options = array()) {
        $options['inline'] = empty($options['inline']) ? FALSE : $options['inline'];
		
		if(is_array($url)) {
			$out = NULL;
			foreach($url as $path)
				$out .= parent::script($path, $options);
			
			return $out;
		}
		
        return parent::script($url, $options);
    }

    /**
     * Returns a Javascript code block.
     * @param string $code Javascript code
     * @param array $options HTML attributes
     * @return string Html, javascript code
     */
    public function scriptBlock($code, $options = array()) {
        $options['inline'] = empty($options['inline']) ? FALSE : $options['inline'];

        return parent::scriptBlock($code, $options);
    }

    /**
     * Ends capturing output for Javascript code.
     * 
     * To start capturing output, see the `scriptStart()` method.
     * 
     * To capture output with a single method, you can also use the `scriptBlock()` method.
     * @return mixed A script tag or NULL
     * @see scriptBlock()
     * @see scriptStart()
     */
    public function scriptEnd() {
        return parent::scriptEnd();
    }

    /**
     * Starts capturing output for Javascript code.
     * 
     * To end capturing output, see the `scriptEnd()` method.
     * 
     * To capture output with a single method, you can also use the `scriptBlock()` method.
     * @param array $options Options for the code block
     * @return mixed A script tag or NULL
     * @see scriptBlock()
     * @see scriptEnd()
     */
    public function scriptStart($options = array()) {
        $options['inline'] = empty($options['inline']) ? FALSE : $options['inline'];

        return parent::scriptStart($options);
    }

    /**
     * Returns a formatted block tag.
     * @param string $name Tag name.
     * @param string $text Tag content. If NULL, only a start tag will be printed
     * @param array $options HTML attributes
     * @return string Html, tag element
     */
    public function tag($name, $text = NULL, $options = array()) {
		$options = self::_setTooltip($options);
		
        $text = empty($options['icon']) ? $text : self::icon($options['icon']).$text;
        unset($options['icon']);

        return parent::tag($name, $text, $options);
    }

    /**
	 * Creates and returns a thumbnail of an image or a video.
     * 
     * To get a thumbnail, you have to use `width` and/or `height` options. 
     * To get a square thumbnail, you have to use the `side` option.
	 * 
	 * You can use the `height` option only for image files.
     * @param string $path Image path (absolute or relative to the webroot)
     * @param array $options HTML attributes
     * @return string Html, tag element
     * @see http://repository.novatlantis.it/metools-sandbox/html/images Examples
     * @uses thumbUrl() to get the url thumbnail
     * @uses image() to display the thumbnail
     */
    public function thumb($path, $options = array()) {
        $path = self::thumbUrl($path, $options);
        unset($options['side'], $options['width'], $options['height']);

        $options['class'] = empty($options['class']) ? 'thumb' : self::_clean('thumb', $options['class']);

        return self::image($path, $options);
    }

    /**
	 * Creates and returns the url for a thumbnail of an image or a video.
     * 
     * To get a thumbnail, you have to use `width` and/or `height` options. 
     * To get a square thumbnail, you have to use the `side` option.
	 * 
	 * You can use the `height` option only for image files.
     * 
     * Note that to directly display a thumbnail, you should use the `thumb()` method. 
	 * This method only returns the url of the thumbnail.
     * @param string $path Image path (absolute or relative to the webroot)
     * @param array $options HTML attributes
     * @return string Html, tag element
     * @see thumb()
     * @see http://repository.novatlantis.it/metools-sandbox/html/images Examples
     * @uses url() to generate the thumbnail url
     */
    public function thumbUrl($path, $options = array()) {		
        //If the side is defined, then the width and height are NULL (we don't need these)
        if($options['side'] = empty($options['side']) ? NULL : $options['side'])
            $options['width'] = $options['height'] = NULL;
        else {
            $options['width'] = empty($options['width']) ? NULL : $options['width'];
            $options['height'] = empty($options['height']) ? NULL : $options['height'];
        }

        return self::url(am(array('controller' => 'thumbs', 'action' => 'thumb', 'plugin' => 'me_tools', 'admin' => FALSE), array('?' => array('s' => $options['side'], 'w' => $options['width'], 'h' => $options['height'])), array(base64_encode($path))), TRUE);
    }

    /**
     * Returns a tip block.
     * 
     * By default, the tip block will have a title. To change the title, use the "title" option. If the "title" option is 
     * an array, you can use "text" and "options" keys. If you don't want to have a title, the "title" option should be `FALSE`.
     * @param string|array $text Tip text, as string or array
     * @param array $options HTML attributes
     * @return Html, tip block
     * @see http://repository.novatlantis.it/metools-sandbox/html/tips Examples
     */
    public function tip($text, $options = array()) {
        $text = is_array($text) ? $text : array($text);
        array_walk($text, function(&$v) {
            $v = self::para(NULL, $v);
        });
        $text = self::div('tip-text', implode(NULL, $text));

        $options['class'] = empty($options['class']) ? 'tip' : self::_clean('tip', $options['class']);

        if(!isset($options['title']) || $title = $options['title']) {
            if(empty($title))
                $title = array('text' => __d('me_tools', 'Tip'));
            elseif(!is_array($title))
                $title = array('text' => $title);

            $title['class'] = empty($title['class']) ? 'tip-title' : self::_clean('tip-title', $title['class']);
            $title['icon'] = empty($title['icon']) ? 'fa-magic' : $title['icon'];
            $title['options'] = $title;
            unset($title['options']['text']);

            $text = self::tag('h4', $title['text'], $title['options']).$text;
        }
        unset($options['icon'], $options['title']);

        return self::div($options['class'], $text, $options);
    }

    /**
     * Returns an unordered list (`ul`) out of an array.
     * @param array $list Element list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string Html, unordered list
     * @see http://repository.novatlantis.it/metools-sandbox/html/lists Examples
     * @uses nestedList() to create the list
     */
    public function ul($list, $options = array(), $itemOptions = array()) {
        return self::nestedList($list, $options, $itemOptions, 'ul');
    }

    /**
     * Returns a video element
     * @param string|array $path File path, relative to the `webroot/{$options['pathPrefix']}` directory
     * or an array where each item itself can be a path string or an array containing `src` and `type` keys.
     * @param array $options Array of HTML attributes
     * @return string Html, video tag
     * @see http://repository.novatlantis.it/metools-sandbox/html/audiovideo Examples
     * @uses media()
     */
    public function video($path, $options = array()) {
        return self::media($path, am($options, array('tag' => 'video')));
    }
}