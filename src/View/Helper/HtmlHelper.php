<?php
declare(strict_types=1);

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
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\HtmlHelper as BaseHtmlHelper;
use Tools\Exceptionist;

/**
 * Provides functionalities for HTML
 *
 * @method string code(?string $text = null, array $options = [])
 * @method string h1(?string $text = null, array $options = [])
 * @method string h2(?string $text = null, array $options = [])
 * @method string h3(?string $text = null, array $options = [])
 * @method string h4(?string $text = null, array $options = [])
 * @method string h5(?string $text = null, array $options = [])
 * @method string h6(?string $text = null, array $options = [])
 * @method string legend(?string $text = null, array $options = [])
 * @method string span(?string $text = null, array $options = [])
 * @method string strong(?string $text = null, array $options = [])
 * @method string time(?string $text = null, array $options = [])
 * @method string title(?string $text = null, array $options = [])
 * @property \MeTools\View\Helper\IconHelper $Icon
 * @property \Cake\View\Helper\UrlHelper $Url
 */
class HtmlHelper extends BaseHtmlHelper
{
    use AddButtonClassesTrait;

    /**
     * Helpers
     * @var array
     */
    public $helpers = ['MeTools.Icon', 'Url'];

    /**
     * Missing method handler.
     *
     * If you pass no more than two parameters, it tries to generate a html
     *  tag with the name of the method and works as alias of `tag()`.
     * @param string $method Name of the tag
     * @param array $params Params for the method
     * @return string
     * @throws \ErrorException
     */
    public function __call(string $method, array $params = []): string
    {
        Exceptionist::isTrue(count($params) < 3, sprintf('Method `%s::%s()` does not exist', __CLASS__, $method));

        return $this->tag($method, $params[0], $params[1] ?? []);
    }

    /**
     * Creates a "badge"
     * @param string $text Badge text
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return string
     * @see https://getbootstrap.com/docs/5.2/components/badge/
     */
    public function badge(string $text, array $options = []): string
    {
        $options = $this->addClass($options, 'badge');

        return $this->tag('span', $text, $options);
    }

    /**
     * Creates a link with the appearance of a button.
     *
     * See the parent method for all available options.
     * @param array|string $title The content to be wrapped by `<a>` tags
     *   Can be an array if $url is null. If $url is null, $title will be used as both the URL and title.
     * @param array|string|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return string An `<a />` element
     * @throws \ErrorException
     */
    public function button($title, $url = null, array $options = []): string
    {
        $options += ['role' => 'button'];

        return $this->link($title, $url, $this->addButtonClasses($options));
    }

    /**
     * Create a "flush" list group
     * @param array $list Set of elements to list
     * @param array<string, mixed> $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array<string, mixed> $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The "flush" list
     * @since 2.25.0
     * @see https://getbootstrap.com/docs/5.3/components/list-group/#flush
     * @see \Cake\View\Helper\HtmlHelper::nestedList() for all available options
     */
    public function flush(array $list, array $options = [], array $itemOptions = []): string
    {
        $options = $this->addClass($options, 'list-group list-group-flush');
        $itemOptions = $this->addClass($itemOptions, 'list-group-item');

        return $this->ul($list, $options, $itemOptions);
    }

    /**
     * Create a `<hr>` element
     * @param array $options Array of options and HTML attributes
     * @return string
     * @since 2.25.0
     */
    public function hr(array $options = []): string
    {
        return $this->tag('hr', '', $options);
    }

    /**
     * Create an `<iframe>` element.
     *
     * You can use the `$ratio` option (valid values: '1x1', '4x3', '16x9', '21x9') to create a responsive embed.
     * @param string|array $url Url for the iframe
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see https://getbootstrap.com/docs/5.2/helpers/ratio/#aspect-ratios
     */
    public function iframe($url, array $options = []): string
    {
        $options['src'] = is_array($url) ? $this->Url->build($url, $options) : $url;

        $ratio = $options['ratio'] ?? null;
        unset($options['ratio']);

        $iframe = $this->tag('iframe', '', $options);

        return in_array($ratio, ['1x1', '4x3', '16x9', '21x9']) ? $this->div('ratio ratio-' . $ratio, $iframe) : $iframe;
    }

    /**
     * Creates a formatted IMG element.
     *
     * See the parent method for all available options.
     * @param array|string $path Path to the image file, relative to the webroot/img/ directory
     * @param array<string, mixed> $options Array of HTML attributes. See above for special options
     * @return string completed img tag
     */
    public function image($path, array $options = []): string
    {
        $options += ['alt' => pathinfo(is_array($path) ? $this->Url->build($path, $options) : $path, PATHINFO_BASENAME)];
        $options = $this->addClass($options, 'img-fluid');

        return parent::image($path, $options);
    }

    /**
     * Alias for `image()` method
     * @return string
     * @see image()
     */
    public function img(): string
    {
        return call_user_func_array([get_class(), 'image'], func_get_args());
    }

    /**
     * Returns an element list (`<li>`).
     *
     * If `$element` is an array, the same `$options` will be applied to all
     *  elements.
     * @param string|array<string> $element Element or elements
     * @param array $options HTML attributes of the list tag
     * @return string
     */
    public function li($element, array $options = []): string
    {
        return implode(PHP_EOL, array_map(fn(string $element): string => $this->tag('li', $element, $options), (array)$element));
    }

    /**
     * Creates an HTML link.
     *
     * See the parent method for all available options.
     * @param array|string $title The content to be wrapped by `<a>` tags
     *   Can be an array if $url is null. If $url is null, $title will be used as both the URL and title.
     * @param array|string|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return string An `<a />` element
     */
    public function link($title, $url = null, array $options = []): string
    {
        if (is_array($title)) {
            if (!$url) {
                $url = $title;
            }
            $title = '';
        }

        $options += ['escape' => false];

        $titleAsOption = $options['title'] ?? $title;
        if ($titleAsOption) {
            $options['title'] = trim(strip_tags($titleAsOption));
        }

        [$title, $options] = $this->Icon->addIconToText($title, $options);

        return parent::link($title, $url, $options);
    }

    /**
     * Creates a link to an external resource and handles basic meta tags
     * @param array<string, mixed>|string $type The title of the external resource,
     *  or an array of attributes for a custom meta tag
     * @param array|string|null $content The address of the external resource or string
     *  for content attribute
     * @param array<string, mixed> $options Other attributes for the generated tag. If
     *  the type attribute is html, rss, atom, or icon, the mime-type is returned
     * @return string|null A completed `<link />` element, or null if the element was
     *  sent to a block
     */
    public function meta($type, $content = null, array $options = []): ?string
    {
        return parent::meta($type, $content, $options + ['block' => true]);
    }

    /**
     * Build a nested list (UL/OL) out of an associative array.
     *
     * See the parent method for all available options.
     * @param array $list Set of elements to list
     * @param array<string, mixed> $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array<string, mixed> $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list
     */
    public function nestedList(array $list, array $options = [], array $itemOptions = []): string
    {
        $options += ['icon' => null];
        $itemOptions += ['icon' => $options['icon']];

        if ($itemOptions['icon']) {
            $options = $this->addClass($options, 'fa-ul');
            $itemOptions = $this->addClass($itemOptions, 'li', 'icon');
            $list = array_map(fn(string $element): string => array_value_first($this->Icon->addIconToText($element, $itemOptions)), $list);
        }

        unset($options['icon'], $options['icon-align'], $itemOptions['icon'], $itemOptions['icon-align']);

        return parent::nestedList($list, $options, $itemOptions);
    }

    /**
     * Build an `<ol>` nested list out of an associative array.
     *
     * See the parent method for all available options.
     * @param array $list Set of elements to list
     * @param array<string, mixed> $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array<string, mixed> $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list
     */
    public function ol(array $list, array $options = [], array $itemOptions = []): string
    {
        return $this->nestedList($list, ['tag' => 'ol'] + $options, $itemOptions);
    }

    /**
     * Returns the Shareaholic "share buttons".
     *
     * Note that this code only renders the Shareaholic "share button".
     * @param string $appId Shareaholic app ID
     * @return string
     */
    public function shareaholic(string $appId): string
    {
        return $this->div('shareaholic-canvas', '', [
            'data-app' => 'share_buttons',
            'data-app-id' => $appId,
        ]);
    }

    /**
     * Returns a formatted block tag, i.e DIV, SPAN, P.
     *
     * See the parent method for all available options.
     * @param string $name Tag name
     * @param string|null $text String content that will appear inside the div element.
     *   If null, only a start tag will be printed
     * @param array<string, mixed> $options Additional HTML attributes of the DIV tag, see above
     * @return string The formatted tag element
     */
    public function tag(string $name, ?string $text = null, array $options = []): string
    {
        [$text, $options] = $this->Icon->addIconToText($text, $options);

        return parent::tag($name, $text, $options);
    }

    /**
     * Build an `<ul>` nested list out of an associative array.
     *
     * See the parent method for all available options.
     * @param array $list Set of elements to list
     * @param array<string, mixed> $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array<string, mixed> $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list
     */
    public function ul(array $list, array $options = [], array $itemOptions = []): string
    {
        return $this->nestedList($list, ['tag' => 'ul'] + $options, $itemOptions);
    }

    /**
     * Adds the `viewport` meta tag. By default, it uses options as required by Bootstrap
     * @param array $content Additional content values
     * @param array<string, mixed> $options Other attributes for the generated tag. If the type attribute is html,
     *    rss, atom, or icon, the mime-type is returned.
     * @return string|null
     * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Viewport_meta_tag
     */
    public function viewport(array $content = [], array $options = []): ?string
    {
        $content = http_build_query($content + [
            'initial-scale' => '1',
            'width' => 'device-width',
        ], '', ', ');

        return $this->meta('viewport', $content, $options);
    }

    /**
     * Returns a YouTube video code.
     *
     * You can use the `$ratio` option (valid values: '1x1', '4x3', '16x9', '21x9') to create a responsive embed.
     * @param string $id YouTube video ID
     * @param array $options Array of options and HTML attributes
     * @return string
     */
    public function youtube(string $id, array $options = []): string
    {
        $url = 'https://www.youtube.com/embed/' . str_replace('?t=', '?start=', $id);
        $options += ['allowfullscreen' => 'allowfullscreen', 'height' => 480, 'ratio' => '16x9', 'width' => 640];

        return $this->iframe($url, $options);
    }
}
