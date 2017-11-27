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
use MeTools\View\OptionsParser;

/**
 * Provides functionalities for HTML code
 */
class HtmlHelper extends CakeHtmlHelper
{
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
     * Internal method to build icon classes
     * @param string|array $icon Icons
     * @return string
     * @since 2.16.2-beta
     */
    protected function buildIconClasses($icon)
    {
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
     * Adds icons to text
     * @param string $text Text
     * @param OptionsParser $options Instance of `OptionsParser`
     * @return array Text with icons as first value, instance of `OptionsParser`
     *  as second value
     * @since 2.16.2-beta
     * @uses icon()
     */
    public function addIconToText($text, OptionsParser $options)
    {
        $icon = $options->get('icon');
        $align = $options->get('icon-align');

        $options->delete('icon', 'icon-align');

        if (!$icon) {
            return [$text, $options];
        }

        $icon = $this->icon($icon);

        if (empty($text)) {
            $text = $icon;
        } elseif ($align === 'right') {
            $text = sprintf('%s %s', $text, $icon);
        } else {
            $text = sprintf('%s %s', $icon, $text);
        }

        return [$text, $options];
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
        $options = new OptionsParser($options);
        $options->append('class', 'badge');

        return self::tag('span', $text, $options->toArray());
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
        $options = new OptionsParser($options, ['role' => 'button']);
        $options->addButtonClasses();

        if (!empty($url)) {
            return self::link($title, $url, $options->toArray());
        }

        $options->Default->add('title', $title);
        $options->add('title', strip_tags($options->get('title')));

        return self::tag('button', $title, $options->toArray());
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
        $options = new OptionsParser($options, ['block' => true]);

        return parent::css($path, $options->toArray());
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
        $options = new OptionsParser($options, ['block' => true]);

        $out = $this->formatTemplate('style', [
            'attrs' => $this->templater()->formatAttributes($options->toArray(), ['block']),
            'content' => $css,
        ]);

        if (!$options->get('block')) {
            return $out;
        }

        if ($options->contains('block', true)) {
            $options->add('block', 'css');
        }

        $this->_View->append($options->get('block'), $out);
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
        $options = new OptionsParser($options);
        $type = !$options->exists('type') || !preg_match('/^h[1-6]$/', $options->get('type')) ? 'h2' : $options->get('type');
        $options->delete('type');

        if (!empty($small)) {
            $text = sprintf('%s %s', $text, self::small($small, $smallOptions));
        }

        return self::tag($type, $text, $options->toArray());
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
     * @param string|array $icon Icons. You can also pass multiple arguments
     * @return string
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
     * @uses buildIconClasses()
     * @uses tag()
     */
    public function icon($icon)
    {
        if (func_num_args() > 1) {
            $icon = func_get_args();
        }

        return self::tag('i', ' ', ['class' => $this->buildIconClasses($icon)]);
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
        $options = new OptionsParser($options);
        $options->add('src', $url);

        if ($options->exists('ratio')) {
            $ratio = $options->get('ratio');
            $options->delete('ratio');

            if (in_array($ratio, ['16by9', '4by3'])) {
                $divClass = sprintf('embed-responsive embed-responsive-%s', $ratio);
                $options->append('class', 'embed-responsive-item');

                return self::div($divClass, self::tag('iframe', null, $options->toArray()));
            }
        }

        return self::tag('iframe', null, $options->toArray());
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
        $options = new OptionsParser($options, ['alt' => pathinfo($path, PATHINFO_BASENAME)]);
        $options->append('class', 'img-fluid')->tooltip();

        return parent::image($path, $options->toArray());
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
        $options = new OptionsParser($options);
        $type = $options->get('type') ?: 'default';
        $options->append('class', sprintf('label label-%s', $type));
        $options->delete('type');

        return self::tag('span', $text, $options->toArray());
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
        $options = new OptionsParser($options, ['escape' => false, 'title' => $title]);
        $options->add('title', trim(h(strip_tags($options->get('title')))))->tooltip();
        list($title, $options) = $this->addIconToText($title, $options);

        return parent::link($title, $url, $options->toArray());
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
        $options = new OptionsParser($options, ['block' => true]);

        return parent::meta($type, $content, $options->toArray());
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
        $options = new OptionsParser($options);
        $itemOptions = new OptionsParser($itemOptions);

        if ($options->exists('icon')) {
            $itemOptions->add('icon', $options->get('icon'));
        }

        if ($itemOptions->exists('icon')) {
            $options->append('class', 'fa-ul');
            $itemOptions->append('icon', 'li');

            $list = collection($list)
                ->map(function ($element) use ($itemOptions) {
                    return collection($this->addIconToText($element, clone $itemOptions))->first();
                })
                ->toArray();
        }

        $options->delete('icon', 'icon-align');
        $itemOptions->delete('icon', 'icon-align');

        return parent::nestedList($list, $options->toArray(), $itemOptions->toArray());
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
        $options = new OptionsParser($options);
        $options->tooltip();
        list($text, $options) = $this->addIconToText($text, $options);

        return parent::para($class, is_null($text) ? '' : $text, $options->toArray());
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
        $options = new OptionsParser($options, ['block' => true]);

        return parent::script($url, $options->toArray());
    }

    /**
     * Returns a Javascript code block
     * @param string $code Javascript code
     * @param array $options Array of options and HTML attributes
     * @return mixed A script tag or `null`
     */
    public function scriptBlock($code, array $options = [])
    {
        $options = new OptionsParser($options, ['block' => true]);

        return parent::scriptBlock($code, $options->toArray());
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
        $options = new OptionsParser($options, ['block' => 'script_bottom']);

        return parent::scriptStart($options->toArray());
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
        $options = new OptionsParser($options);
        $options->tooltip();
        list($text, $options) = $this->addIconToText($text, $options);

        return parent::tag($name, is_null($text) ? '' : $text, $options->toArray());
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
            'shrink-to-fit' => 'no',
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

        $options = new OptionsParser($options, [
            'allowfullscreen' => 'allowfullscreen',
            'height' => 480,
            'ratio' => '16by9',
            'width' => 640,
        ]);

        return self::iframe($url, $options->toArray());
    }
}
