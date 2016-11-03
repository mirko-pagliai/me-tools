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
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;
use MeTools\Utility\Youtube;

/**
 * BBCode Helper.
 *
 * This helper allows you to handle some BBCode.
 *
 * The `parser()` method executes all parsers.
 */
class BBCodeHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = ['Html' => ['className' => 'MeTools.Html']];

    /**
     * Pattern
     * @var array
     */
    protected $pattern = [
        'image' => '/\[img](.+?)\[\/img]/',
        'readmore' => '/(<p(>|.*?[^?]>))?\[read\-?more\s*\/?\s*\](<\/p>)?/',
        'youtube' => '/\[youtube](.+?)\[\/youtube]/',
    ];

    /**
     * Executes all parsers
     * @param string $text Text
     * @return string
     */
    public function parser($text)
    {
        //Gets all current class methods, except for `parser()` and `remove()`
        $methods = getChildMethods(get_class(), ['parser', 'remove']);

        //Calls dynamically each method
        foreach ($methods as $method) {
            $text = self::{$method}($text);
        }

        return $text;
    }

    /**
     * Removes all BBCode
     * @param string $text Text
     * @return string
     * @uses $pattern
     */
    public function remove($text)
    {
        return trim(preg_replace($this->pattern, null, $text));
    }

    /**
     * Parses image code.
     * <code>
     * [img]mypic.gif[/img]
     * </code>
     * @param string $text Text
     * @return string
     * @uses $pattern
     */
    public function image($text)
    {
        return preg_replace_callback($this->pattern['image'], function ($matches) {
            return $this->Html->image($matches[1]);
        }, $text);
    }

    /**
     * Parses "read mode" code. Example:
     * <code>
     * [read-more /]
     * </code>
     * @param string $text Text
     * @return string
     * @uses $pattern
     */
    public function readMore($text)
    {
        return preg_replace(
            $this->pattern['readmore'],
            '<!-- read-more -->',
            $text
        );
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
     * @uses MeTools\Utility\Youtube::getId()
     * @uses MeTools\View\Helper\HtmlHelper::youtube()
     * @uses $pattern
     */
    public function youtube($text)
    {
        return preg_replace_callback(
            $this->pattern['youtube'],
            function ($matches) {
                if (isUrl($matches[1])) {
                    return $this->Html->youtube(Youtube::getId($matches[1]));
                }

                return $this->Html->youtube($matches[1]);
            },
            $text
        );
    }
}
