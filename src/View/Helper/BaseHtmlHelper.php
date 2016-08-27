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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 * @see         http://api.cakephp.org/3.3/class-Cake.View.Helper.HtmlHelper.html HtmlHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;

/**
 * Provides functionalities for HTML code.
 *
 * This class override `Cake\View\Helper\HtmlHelper` and improve its methods.
 *
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = ['Html' => ['className' => 'MeTools.BaseHtml']];
 * </code>
 */
class BaseHtmlHelper extends CakeHtmlHelper
{
    /**
     * This is called automatically when a method doesn't exist.
     *
     * If you pass no more than two parameters, it tries to generate a html
     *  tag with the name of the method.
     * @param string $method Method to invoke
     * @param array $params Params for the method
     * @return string|void
     * @uses tag()
     */
    public function __call($method, $params)
    {
        if (count($params) <= 2) {
            return self::tag(
                $method,
                empty($params[0]) ? null : $params[0],
                empty($params[1]) ? [] : $params[1]
            );
        }

        parent::__call($method, $params);
    }

    /**
     * Adds icons to text
     * @param string $text Text
     * @param array $options Array of HTML attributes
     * @return string Text with icon or icons
     * @uses icon()
     */
    public function addIcon($text, $options)
    {
        if (!empty($options['icon'])) {
            if (empty($text)) {
                $text = self::icon($options['icon']);
            } elseif (!empty($options['icon-align']) &&
                $options['icon-align'] === 'right'
            ) {
                $text = sprintf('%s %s', $text, self::icon($options['icon']));
            } else {
                $text = sprintf('%s %s', self::icon($options['icon']), $text);
            }
        }
        
        return $text;
    }

    /**
     * Creates a button (`<button>` tag).
     *
     * If `$url` is not null, creates a link (`<a>` tag) with the appearance
     *  of a button.
     * @param string $title Button title
     * @param string|array|null $url Cake-relative URL or array of URL
     *  parameters or external URL
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses link()
     * @uses tag()
     */
    public function button($title, $url = null, array $options = [])
    {
        $options = optionValues(['role' => 'button'], $options);
        $options = buttonClass($options);

        if (!empty($url)) {
            return self::link($title, $url, $options);
        }
        
        $options = optionDefaults(['title' => $title], $options);
        
        $options['title'] = strip_tags($options['title']);
        
        return self::tag('button', $title, $options);
    }

    /**
     * Adds a css file to the layout.
     *
     * If it's used in the layout, you should set the `inline` option to `true`.
     * @param mixed $path Css filename or an array of css filenames
     * @param array $options Array of options and HTML attributes
     * @return string Html, `<link>` or `<style>` tag
     */
    public function css($path, array $options = [])
    {
        $options = optionDefaults(['block' => true], $options);

        return parent::css($path, $options);
    }

    /**
     * Returns a formatted DIV tag
     * @param string $class CSS class name of the div element
     * @param string $text String content that will appear inside the div
     *  element
     * @param array $options Array of options and HTML attributes
     * @return string
     */
    public function div($class = null, $text = null, array $options = [])
    {
        return parent::div($class, $text, $options);
    }

    /**
     * Creates an horizontal rule (`<hr>` tag)
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses tag()
     */
    public function hr(array $options = [])
    {
        return self::tag('hr', null, $options);
    }

    /**
     * Returns icons. Examples:
     * <code>
     * echo $this->Html->icon('home');
     * </code>
     * <code>
     * echo $this->Html->icon(['hand-o-right', '2x']);
     * </code>
     * @param string|array $icon Icons
     * @return string
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
     * @uses tag()
     */
    public function icon($icon)
    {
        if (func_num_args() > 1) {
            $icon = func_get_args();
        }
        
        //Prepends the string "fa-" to any other class
        $icon = preg_replace('/(?<![^ ])(?=[^ ])(?!fa)/', 'fa-', $icon);

        //Adds the "fa" class
        $options = optionDefaults(['class' => ['fa', $icon]]);

        return self::tag('i', ' ', $options);
    }

    /**
     * Create an `<iframe>` element
     * @param string $url Url for the iframe
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses tag()
     */
    public function iframe($url, array $options = [])
    {
        return self::tag('iframe', null, am($options, ['src' => $url]));
    }

    /**
     * Creates a formatted `<img>` element
     * @param string $path Path to the image file, relative to the
     *  `APP/webroot/img/` directory
     * @param array $options Array of options and HTML attributes
     * @return string
     */
    public function image($path, array $options = [])
    {
        $options = optionDefaults([
            'alt' => pathinfo($path, PATHINFO_BASENAME),
        ], $options);
        $options = optionValues(['class' => 'img-responsive'], $options);

        return parent::image($path, $options);
    }

    /**
     * Alias for `image()` method
     * @return string
     * @see image()
     */
    public function img()
    {
        return call_user_func_array([get_class(), 'image'], func_get_args());
    }

    /**
     * Alias for `script()` method
     * @return mixed String of `<script />` tags or null if `$inline` is false
     *  or if `$once` is true and the file has been included before
     * @see script()
     */
    public function js()
    {
        return call_user_func_array([get_class(), 'script'], func_get_args());
    }
    
    /**
     * Returns an element list (`<li>`).
     *
     * If `$element` is an array, the same `$options` will be applied to all
     *  elements
     * @param string|array $element Element or elements
     * @param array $options HTML attributes of the list tag
     * @return string
     * @uses tag()
     */
    public function li($element, array $options = [])
    {
        if (is_array($element)) {
            $element = array_map(function ($element) use ($options) {
                return self::tag('li', $element, $options);
            }, $element);
            
            return implode(PHP_EOL, $element);
        }
        
        return self::tag('li', $element, $options);
    }

    /**
     * Creates an HTML link
     * @param string $title The content to be wrapped by <a> tags
     * @param string|array $url Cake-relative URL or array of URL parameters
     *  or external URL
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses addIcon()
     */
    public function link($title, $url = null, array $options = [])
    {
        $options = optionDefaults([
            'escape' => false,
            'title' => $title,
        ], $options);
        
        $options['title'] = h(strip_tags($options['title']));

        $title = self::addIcon($title, $options);
        unset($options['icon'], $options['icon-align']);

        return parent::link($title, $url, $options);
    }

    /**
     * Creates a link to an external resource and handles basic meta tags
     * @param string|array $type The title of the external resource
     * @param string|array|null $content The address of the external resource
     *  or string for content attribute
     * @param array $options Other attributes for the generated tag. If the
     *  type attribute is html, rss, atom, or icon, the mime-type is returned
     * @return string A completed `<link />` element
     */
    public function meta($type, $content = null, array $options = [])
    {
        $options = optionDefaults(['block' => true], $options);

        return parent::meta($type, $content, $options);
    }

    /**
     * Returns a list (`<ol>` or `<ul>` tag)
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string
     * @uses addIcon()
     */
    public function nestedList(array $list, array $options = [], array $itemOptions = [])
    {
        if (!empty($options['icon'])) {
            $itemOptions['icon'] = $options['icon'];
        }
        
        if (!empty($itemOptions['icon'])) {
            $options = optionValues(['class' => 'fa-ul'], $options);
            $itemOptions = optionValues(['icon' => 'li'], $itemOptions);
            
            $list = array_map(function ($element) use ($itemOptions) {
                return self::addIcon($element, $itemOptions);
            }, $list);
        }
        
        unset(
            $options['icon'],
            $options['icon-align'],
            $itemOptions['icon'],
            $itemOptions['icon-align']
        );
        
        return parent::nestedList($list, $options, $itemOptions);
    }
    
    /**
     * Returns an unordered list (`<ol>` tag)
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string
     * @uses nestedList()
     */
    public function ol(array $list, array $options = [], array $itemOptions = [])
    {
        return self::nestedList($list, am($options, ['tag' => 'ol']), $itemOptions);
    }

    /**
     * Returns a formatted `<p>` tag.
     * @param string $class Class name
     * @param string $text Paragraph text
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses addIcon()
     */
    public function para($class = null, $text = null, array $options = [])
    {
        $text = self::addIcon($text, $options);
        unset($options['icon'], $options['icon-align']);

        return parent::para($class, is_null($text) ? '' : $text, $options);
    }

    /**
     * Adds a js file to the layout.
     *
     * If it's used in the layout, you should set the `inline` option to `true`.
     * @param mixed $url Javascript files as string or array
     * @param array $options Array of options and HTML attributes
     * @return mixed String of `<script />` tags or `null` if `$inline` is false
     *  or if `$once` is true and the file has been included before
     */
    public function script($url, array $options = [])
    {
        $options = optionDefaults(['block' => true], $options);

        return parent::script($url, $options);
    }

    /**
     * Returns a Javascript code block
     * @param string $code Javascript code
     * @param array $options Array of options and HTML attributes
     * @return mixed A script tag or `null`
     */
    public function scriptBlock($code, array $options = [])
    {
        $options = optionDefaults(['block' => true], $options);

        return parent::scriptBlock($code, $options);
    }

    /**
     * Starts capturing output for Javascript code.
     *
     * To end capturing output, you can use the `scriptEnd()` method.
     *
     * To capture output with a single method, you can also use the
     *  `scriptBlock()` method.
     * @param array $options Options for the code block
     * @return mixed A script tag or `null`
     * @see scriptBlock()
     */
    public function scriptStart(array $options = [])
    {
        $options = optionDefaults(['block' => 'script_bottom'], $options);

        return parent::scriptStart($options);
    }

    /**
     * Returns a formatted block tag
     * @param string $name Tag name
     * @param string $text Tag content. If `null`, only a start tag will be
     *  printed
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses addIcon()
     */
    public function tag($name, $text = null, array $options = [])
    {
        $text = self::addIcon($text, $options);
        unset($options['icon'], $options['icon-align']);

        return parent::tag($name, is_null($text) ? '' : $text, $options);
    }

    /**
     * Returns an unordered list (`<ul>` tag)
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string
     * @uses nestedList()
     */
    public function ul(array $list, array $options = [], array $itemOptions = [])
    {
        return self::nestedList($list, am($options, ['tag' => 'ul']), $itemOptions);
    }
}
