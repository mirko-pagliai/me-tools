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
use LogicException;

/**
 * Provides functionalities for HTML
 *
 * @method string abbr(?string $text = null, array $options = [])
 * @method string code(?string $text = null, array $options = [])
 * @method string h1(?string $text = null, array $options = [])
 * @method string h2(?string $text = null, array $options = [])
 * @method string h3(?string $text = null, array $options = [])
 * @method string h4(?string $text = null, array $options = [])
 * @method string h5(?string $text = null, array $options = [])
 * @method string h6(?string $text = null, array $options = [])
 * @method string legend(?string $text = null, array $options = [])
 * @method string small(?string $text = null, array $options = [])
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
     * @inheritDoc
     */
    protected array $helpers = ['MeTools.Icon', 'Url'];

    /**
     * Missing method handler.
     *
     * If you pass no more than two parameters, create a html tag with the name of the method and works as alias of `tag()`.
     * @param string $method Name of the tag
     * @param array $params Params for the method
     * @return string
     * @throws \LogicException
     */
    public function __call(string $method, array $params = []): string
    {
        if (count($params) > 2) {
            throw new LogicException(sprintf('Method `%s::%s()` does not exist', __CLASS__, $method));
        }

        return $this->tag($method, $params[0], $params[1] ?? []);
    }

    /**
     * Creates a "badge"
     * @param string $text Badge text
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see https://getbootstrap.com/docs/5.3/components/badge/
     */
    public function badge(string $text, array $options = []): string
    {
        $options = $this->addClass($options, 'badge');

        return $this->tag('span', $text, $options);
    }

    /**
     * Creates a link with the appearance of a button
     * @param string|array $title The content to be wrapped by `<a>` tags.
     *   Can be an array if $url is null. If $url is null, $title will be used as both the URL and title.
     * @param string|array|null $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
     * @param array $options Array of options and HTML attributes
     * @return string An `<a />` element
     * @see link() for all available options
     */
    public function button(string|array $title, string|array|null $url = null, array $options = []): string
    {
        $options += ['role' => 'button'];

        return $this->link($title, $url, $this->addButtonClasses($options));
    }

    /**
     * Create an `<iframe>` element.
     *
     * You can use the `$ratio` option (valid values: '1x1', '4x3', '16x9', '21x9') to create a responsive embed.
     * @param string|array $url Url for the iframe
     * @param array $options Array of options and HTML attributes
     * @return string
     * @see https://getbootstrap.com/docs/5.3/helpers/ratio/#aspect-ratios
     */
    public function iframe(string|array $url, array $options = []): string
    {
        $options['src'] = is_array($url) ? $this->Url->build($url, $options) : $url;

        $ratio = $options['ratio'] ?? null;
        unset($options['ratio']);

        $iframe = $this->tag('iframe', '', $options);

        return in_array($ratio, ['1x1', '4x3', '16x9', '21x9']) ? $this->div('ratio ratio-' . $ratio, $iframe) : $iframe;
    }

    /**
     * @inheritDoc
     */
    public function image(string|array $path, array $options = []): string
    {
        $options += ['alt' => pathinfo(is_array($path) ? $this->Url->build($path, $options) : $path, PATHINFO_BASENAME)];
        $options = $this->addClass($options, 'img-fluid');

        return parent::image($path, $options);
    }

    /**
     * Returns an element list (`<li>`).
     *
     * If `$element` is an array, the same `$options` will be applied to all elements.
     * @param string|string[] $element Element or elements
     * @param array $options HTML attributes of the list tag
     * @return string
     */
    public function li(string|array $element, array $options = []): string
    {
        return implode(PHP_EOL, array_map(fn(string $element): string => $this->tag('li', $element, $options), (array)$element));
    }

    /**
     * @inheritDoc
     */
    public function link(string|array $title, string|array|null $url = null, array $options = []): string
    {
        if (is_array($title)) {
            $url = $url ?: $title;
            $title = '';
        }

        $options += ['class' => null, 'escape' => false, 'icon' => null, 'role' => null];

        $titleAsOption = $options['title'] ?? $title;
        if ($titleAsOption) {
            $options['title'] = trim(strip_tags($titleAsOption));
        }

        if ($options['icon']) {
            if (!$options['role'] && !str_contains($options['class'] ?? '', 'text-decoration-')) {
                $options = $this->addClass($options, 'text-decoration-none');
            }

            [$title, $options] = $this->Icon->addIconToText($title, $options);
        }

        return parent::link($title, $url, $options);
    }

    /**
     * @inheritDoc
     */
    public function meta(string|array $type, string|array|null $content = null, array $options = []): ?string
    {
        return parent::meta($type, $content, $options + ['block' => true]);
    }

    /**
     * @inheritDoc
     */
    public function nestedList(array $list, array $options = [], array $itemOptions = []): string
    {
        $options += ['icon' => null];
        $itemOptions += ['icon' => $options['icon']];

        if ($itemOptions['icon']) {
            $options = $this->addClass($options, 'fa-ul');
            $itemOptions = $this->addClass($itemOptions, 'fa-li', 'icon');
            $list = array_map(fn(string $element): string => array_value_first($this->Icon->addIconToText($element, $itemOptions)), $list);
        }

        unset($options['icon'], $options['icon-align'], $itemOptions['icon'], $itemOptions['icon-align']);

        return parent::nestedList($list, $options, $itemOptions);
    }

    /**
     * Build an `<ol>` nested list out of an associative array
     * @param array $list Set of elements to list
     * @param array $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list
     * @see \Cake\View\Helper\HtmlHelper::nestedList() for all available options
     */
    public function ol(array $list, array $options = [], array $itemOptions = []): string
    {
        return $this->nestedList($list, ['tag' => 'ol'] + $options, $itemOptions);
    }

    /**
     * @inheritDoc
     */
    public function para(?string $class, ?string $text, array $options = []): string
    {
        [$text, $options] = $this->Icon->addIconToText($text, $options);

        return parent::para($class, $text, $options);
    }

    /**
     * @inheritDoc
     */
    public function tag(string $name, ?string $text = null, array $options = []): string
    {
        [$text, $options] = $this->Icon->addIconToText($text, $options);

        return parent::tag($name, $text, $options);
    }

    /**
     * Build an `<ul>` nested list out of an associative array
     * @param array $list Set of elements to list
     * @param array $options Options and additional HTML attributes of the list (ol/ul) tag.
     * @param array $itemOptions Options and additional HTML attributes of the list item (LI) tag.
     * @return string The nested list
     * @see \Cake\View\Helper\HtmlHelper::nestedList() for all available options
     */
    public function ul(array $list, array $options = [], array $itemOptions = []): string
    {
        return $this->nestedList($list, ['tag' => 'ul'] + $options, $itemOptions);
    }

    /**
     * Adds the `viewport` meta tag. By default, it uses options as required by Bootstrap
     * @param array $content Additional content values
     * @param array $options Other attributes for the generated tag. If the type attribute is html, rss, atom, or icon,
     *  the mime-type is returned.
     * @return string|null
     * @see https://getbootstrap.com/docs/5.3/getting-started/introduction/#viewport-meta
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
     * @see iframe() for all available options
     */
    public function youtube(string $id, array $options = []): string
    {
        $url = 'https://www.youtube.com/embed/' . str_replace('?t=', '?start=', $id);
        $options += ['allowfullscreen' => 'allowfullscreen', 'height' => 480, 'ratio' => '16x9', 'width' => 640];

        return $this->iframe($url, $options);
    }
}
