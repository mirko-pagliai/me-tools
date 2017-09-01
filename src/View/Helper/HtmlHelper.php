<?php
/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @see         http://api.cakephp.org/3.4/class-Cake.View.Helper.HtmlHelper.html HtmlHelper
 */
namespace MeTools\View\Helper;

use Cake\Core\Exception\Exception;
use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;
use MeTools\Utility\OptionsParserTrait;

/**
 * Provides functionalities for HTML code
 */
class HtmlHelper extends CakeHtmlHelper
{
    use OptionsParserTrait;

    /**
     * Missing method handler.
     *
     * If you pass no more than two parameters, it tries to generate a html
     *  tag with the name of the method and works as alias of `tag()`.
     * @param string $name Name of the tag
     * @param array $params Params for the method
     * @return string
     * @throws Exception
     * @uses tag()
     */
    public function __call($name, $params)
    {
        if (empty($params) || count($params) > 2) {
            throw new Exception(sprintf('Method HtmlHelper::%s does not exist', $name));
        }

        $text = !isset($params[0]) ? null : $params[0];
        $options = !isset($params[1]) ? [] : $params[1];

        return self::tag($name, $text, $options);
    }

    /**
     * Creates a badge, according to Bootstrap
     * @param string $text Badge text
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see http://getbootstrap.com/components/#badges Bootstrap documentation
     * @uses tag()
     */
    public function badge($text, array $options = [])
    {
        $options = $this->optionsValues(['class' => 'badge'], $options);

        return self::tag('span', $text, $options);
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
        $options = $this->optionsValues(['role' => 'button'], $options);
        $options = $this->addButtonClasses($options);

        if (!empty($url)) {
            return self::link($title, $url, $options);
        }

        $options = $this->optionsDefaults(['title' => $title], $options);
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
        $options = $this->optionsDefaults(['block' => true], $options);

        return parent::css($path, $options);
    }

    /**
     * Wrap `$css` in a style tag
     * @param string $css The css code to wrap
     * @param array $options The options to use. Options not listed above will
     *  be treated as HTML attributes
     * @return string|null String or `null`, depending on the value of
     *  $options['block']`
     */
    public function cssBlock($css, array $options = [])
    {
        $options = $this->optionsDefaults(['block' => true], $options);

        $out = $this->formatTemplate('style', [
            'attrs' => $this->templater()->formatAttributes($options, ['block']),
            'content' => $css,
        ]);

        if (empty($options['block'])) {
            return $out;
        }

        if ($options['block'] === true) {
            $options['block'] = 'css';
        }

        $this->_View->append($options['block'], $out);
    }

    /**
     * Begin a css block that captures output until `cssEnd()` is called. This
     *  capturing block will capture all output between the methods and create
     *  a cssBlock from it
     * @param array $options Options for the code block.
     * @return void
     */
    public function cssStart(array $options = [])
    {
        $options += ['block' => null];
        $this->_cssBlockOptions = $options;
        ob_start();
    }

    /**
     * End a buffered section of css capturing.
     * Generates a style tag inline or appends to specified view block
     *  depending on the settings used when the cssBlock was started.
     * @return string|null Depending on the settings of `cssStart()`, either a
     *  style tag or null
     */
    public function cssEnd()
    {
        $buffer = ob_get_clean();
        $options = $this->_cssBlockOptions;
        $this->_cssBlockOptions = [];

        return $this->cssBlock($buffer, $options);
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
     * Creates an heading, according to Bootstrap.
     *
     * This method is useful if you want to create an heading with a secondary
     *  text. In this case you have to use the `small` option.
     *
     * By default, this method creates an `<h2>` tag. To create a different
     *  tag, you have to use the `type` option.
     * @param string $text Heading text
     * @param array $options Array of options and HTML attributes
     * @param string $small Small text
     * @param array $smallOptions Array of options and HTML attributes
     * @return string
     * @see http://getbootstrap.com/css/#type-headings Bootstrap documentation
     * @uses small()
     * @uses tag()
     */
    public function heading($text, array $options = [], $small = null, array $smallOptions = [])
    {
        $type = empty($options['type']) || !preg_match('/^h[1-6]$/', $options['type']) ? 'h2' : $options['type'];
        unset($options['type']);

        if (!empty($small)) {
            $text = sprintf('%s %s', $text, self::small($small, $smallOptions));
        }

        return self::tag($type, $text, $options);
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
     * Returns icons classes.
     *
     * Example:
     * <code>
     * echo $this->Html->iconClass('home');
     * </code>
     * Returns:
     * <code>
     * fa fa-home
     * </code>
     *
     * Example:
     * <code>
     * echo $this->Html->iconClass(['hand-o-right', '2x']);
     * </code>
     * Returns:
     * <code>
     * fa fa-hand-o-right fa-2x
     * </code>
     * @param string|array $icon Icons
     * @return string
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
     * @since 2.12.3
     */
    public function iconClass($icon)
    {
        if (func_num_args() > 1) {
            $icon = func_get_args();
        }

        //Prepends the string "fa-" to any other class
        $icon = preg_replace('/(?<![^ ])(?=[^ ])(?!fa)/', 'fa-', $icon);

        if (!is_array($icon)) {
            $icon = preg_split('/\s+/', $icon, -1, PREG_SPLIT_NO_EMPTY);
        }

        //Adds the "fa" class
        array_unshift($icon, 'fa');

        return implode(' ', array_unique($icon));
    }

    /**
     * Returns icons tag.
     *
     * Example:
     * <code>
     * echo $this->Html->icon('home');
     * </code>
     * Returns:
     * <code>
     * <i class="fa fa-home"> </i>
     * </code>
     *
     * Example:
     * <code>
     * echo $this->Html->icon(['hand-o-right', '2x']);
     * </code>
     * Returns:
     * <code>
     * <i class="fa fa-hand-o-right fa-2x"> </i>
     * </code>
     * @param string|array $icon Icons
     * @return string
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
     * @uses iconClass()
     * @uses tag()
     */
    public function icon($icon)
    {
        if (func_num_args() > 1) {
            $icon = func_get_args();
        }

        $options = $this->optionsDefaults(['class' => $this->iconClass($icon)], []);

        return self::tag('i', ' ', $options);
    }

    /**
     * Create an `<iframe>` element.
     *
     * You can use the `$ratio` option (valid values: `16by9` or `4by3`) to
     *  create a responsive embed.
     * @param string $url Url for the iframe
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see http://getbootstrap.com/components/#responsive-embed Responsive embed
     * @uses div()
     * @uses tag()
     */
    public function iframe($url, array $options = [])
    {
        $options['src'] = $url;

        if (!empty($options['ratio'])) {
            $ratio = $options['ratio'];
            unset($options['ratio']);

            if (in_array($ratio, ['16by9', '4by3'])) {
                $divClass = sprintf('embed-responsive embed-responsive-%s', $ratio);
                $options = $this->optionsValues(['class' => 'embed-responsive-item'], $options);

                return self::div($divClass, self::tag('iframe', null, $options));
            }
        }

        return self::tag('iframe', null, $options);
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
        $options = $this->optionsDefaults(['alt' => pathinfo($path, PATHINFO_BASENAME)], $options);
        $options = $this->optionsValues(['class' => 'img-responsive'], $options);
        $options = $this->addTooltip($options);

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
     * Create a label, according to the Bootstrap component.
     *
     * This method creates only a label element. Not to be confused with the
     *  `label()` method provided by `Formhelper`, which creates a label
     *  for a form input.
     *
     * You can set the type of label using the `type` option.
     * The values supported by Bootstrap are: `default`, `primary`, `success`,
     *  `info`, `warning` and `danger`.
     * @param string $text Label text
     * @param array $options HTML attributes of the list tag
     * @return string
     * @see http://getbootstrap.com/components/#labels Bootstrap documentation
     * @uses tag()
     */
    public function label($text, array $options = [])
    {
        $options = $this->optionsDefaults(['type' => 'default'], $options);
        $options = $this->optionsValues(['class' => sprintf('label label-%s', $options['type'])], $options);
        unset($options['type']);

        return self::tag('span', $text, $options);
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
        if (!is_array($element)) {
            return self::tag('li', $element, $options);
        }

        $element = collection($element)
            ->map(function ($element) use ($options) {
                return self::tag('li', $element, $options);
            });

        return implode(PHP_EOL, $element->toArray());
    }

    /**
     * Creates an HTML link
     * @param string $title The content to be wrapped by <a> tags
     * @param string|array|null $url Cake-relative URL or array of URL
     *  parameters or external URL
     * @param array $options Array of options and HTML attributes
     * @return string
     */
    public function link($title, $url = null, array $options = [])
    {
        $options = $this->optionsDefaults(['escape' => false, 'title' => $title], $options);
        $options['title'] = trim(h(strip_tags($options['title'])));
        list($title, $options) = $this->addIconToText($title, $options);
        $options = $this->addTooltip($options);

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
        $options = $this->optionsDefaults(['block' => true], $options);

        return parent::meta($type, $content, $options);
    }

    /**
     * Returns a list (`<ol>` or `<ul>` tag)
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string
     */
    public function nestedList(array $list, array $options = [], array $itemOptions = [])
    {
        if (!empty($options['icon'])) {
            $itemOptions['icon'] = $options['icon'];
        }

        if (!empty($itemOptions['icon'])) {
            $options = $this->optionsValues(['class' => 'fa-ul'], $options);
            $itemOptions = $this->optionsValues(['icon' => 'li'], $itemOptions);

            $list = collection($list)
                ->map(function ($element) use ($itemOptions) {
                    return collection($this->addIconToText($element, $itemOptions))->first();
                })
                ->toArray();
        }

        unset($options['icon'], $options['icon-align'], $itemOptions['icon'], $itemOptions['icon-align']);

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
        return self::nestedList($list, array_merge($options, ['tag' => 'ol']), $itemOptions);
    }

    /**
     * Returns a formatted `<p>` tag.
     * @param string $class Class name
     * @param string $text Paragraph text
     * @param array $options Array of options and HTML attributes
     * @return string
     */
    public function para($class = null, $text = null, array $options = [])
    {
        list($text, $options) = $this->addIconToText($text, $options);
        $options = $this->addTooltip($options);

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
        $options = $this->optionsDefaults(['block' => true], $options);

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
        $options = $this->optionsDefaults(['block' => true], $options);

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
        $options = $this->optionsDefaults(['block' => 'script_bottom'], $options);

        return parent::scriptStart($options);
    }

    /**
     * Returns the Shareaholic "share buttons".
     *
     * Note that this code only renders the Shareaholic "share button".
     * To add the "setup code", you have to use the `LayoutHelper`.
     * @param string $appId Shareaholic app ID
     * @return string
     * @see MeTools\View\Helper\LayoutHelper::shareaholic()
     * @uses div()
     */
    public function shareaholic($appId)
    {
        return self::div('shareaholic-canvas', null, [
            'data-app' => 'share_buttons',
            'data-app-id' => $appId,
        ]);
    }

    /**
     * Returns a formatted block tag
     * @param string $name Tag name
     * @param string $text Tag content. If `null`, only a start tag will be
     *  printed
     * @param array $options Array of options and HTML attributes
     * @return string
     */
    public function tag($name, $text = null, array $options = [])
    {
        list($text, $options) = $this->addIconToText($text, $options);
        $options = $this->addTooltip($options);

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
        return self::nestedList($list, array_merge($options, ['tag' => 'ul']), $itemOptions);
    }

    /**
     * Adds the `viewport` meta tag. By default, it uses options as required
     *  by Bootstrap
     * @param array $options Attributes for the generated tag. If the type
     *  attribute is html, rss, atom, or icon, the mime-type is returned
     * @return string
     * @see http://getbootstrap.com/css/#overview-mobile Bootstrap documentation
     * @uses meta()
     */
    public function viewport(array $options = [])
    {
        $content = http_build_query([
            'initial-scale' => '1',
            'maximum-scale' => '1',
            'user-scalable' => 'no',
            'width' => 'device-width',
        ], null, ', ');

        return self::meta(array_merge(['name' => 'viewport'], compact('content')), null, $options);
    }

    /**
     * Adds a YouTube video.
     *
     * You can use the `$ratio` option (valid values: `16by9` or `4by3`) to
     *  create a responsive embed.
     * @param string $id YouTube video ID
     * @param array $options Array of options and HTML attributes
     * @return string
     * @uses iframe()
     */
    public function youtube($id, array $options = [])
    {
        $url = sprintf('https://www.youtube.com/embed/%s', $id);

        $options = $this->optionsDefaults([
            'allowfullscreen' => 'allowfullscreen',
            'height' => 480,
            'ratio' => '16by9',
            'width' => 640,
        ], $options);

        return self::iframe($url, $options);
    }
}
