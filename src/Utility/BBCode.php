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
 * @since       2.18.13
 */
namespace MeTools\Utility;

use Cake\View\View;
use MeTools\View\Helper\HtmlHelper;

/**
 * This utility allows you to handle some BBCode.
 * The `parser()` method executes all parsers.
 */
class BBCode
{
    /**
     * An `HtmlHelper` instance
     * @var \MeTools\View\Helper\HtmlHelper
     */
    public $Html;

    /**
     * Pattern
     * @var array
     */
    protected $pattern = [
        'image' => '/\[img](.+?)\[\/img]/',
        'readmore' => '/(<p(>|.*?[^?]>))?\[read\-?more\s*\/?\s*\](<\/p>)?/',
        'url' => '/\[url=[\'"](.+?)[\'"]](.+?)\[\/url]/',
        'youtube' => '/\[youtube](.+?)\[\/youtube]/',
    ];

    /**
     * Constructor
     * @param \MeTools\View\Helper\HtmlHelper|null $HtmlHelper An `HtmlHelper` instance
     */
    public function __construct(?HtmlHelper $HtmlHelper = null)
    {
        $this->Html = $HtmlHelper ?: new HtmlHelper(new View());
    }

    /**
     * Executes all parsers
     * @param string $text Text
     * @return string
     */
    public function parser(string $text): string
    {
        //Gets all class methods, except for `parser()` and `remove()`
        $methods = array_diff(get_class_methods(__CLASS__), ['__construct', 'parser', 'remove']);

        //Calls dynamically each method
        foreach ($methods as $method) {
            $callable = [$this, $method];
            if (is_callable($callable)) {
                $text = call_user_func($callable, $text);
            }
        }

        return $text;
    }

    /**
     * Removes all BBCode
     * @param string $text Text
     * @return string
     */
    public function remove(string $text): string
    {
        return trim(preg_replace($this->pattern, '', $text) ?: '');
    }

    /**
     * Parses image code.
     * <code>
     * [img]mypic.gif[/img]
     * </code>
     * @param string $text Text
     * @return string
     */
    public function image(string $text): string
    {
        return preg_replace_callback($this->pattern['image'], function ($matches): string {
            return $this->Html->image($matches[1]);
        }, $text) ?: '';
    }

    /**
     * Parses "read mode" code. Example:
     * <code>
     * [read-more /]
     * </code>
     * @param string $text Text
     * @return string
     */
    public function readMore(string $text): string
    {
        return preg_replace($this->pattern['readmore'], '<!-- read-more -->', $text) ?: '';
    }

    /**
     * Parses url code.
     * <code>
     * [url="http://example"]my link[/url]
     * </code>
     * @param string $text Text
     * @return string
     */
    public function url(string $text): string
    {
        return preg_replace_callback($this->pattern['url'], function ($matches): string {
            return $this->Html->link($matches[2], $matches[1]);
        }, $text) ?: '';
    }

    /**
     * Parses Youtube code.
     * You can use video ID or video url.
     *
     * Examples:
     * <code>
     * [youtube]bL_CJKq9rIw[/youtube]
     * </code>
     *
     * <code>
     * [youtube]http://youtube.com/watch?v=bL_CJKq9rIw[/youtube]
     * </code>
     * @param string $text Text
     * @return string
     */
    public function youtube(string $text): string
    {
        return preg_replace_callback($this->pattern['youtube'], function ($matches): string {
            $id = is_url($matches[1]) ? Youtube::getId($matches[1]) : $matches[1];

            return $this->Html->youtube($id);
        }, $text) ?: '';
    }
}
