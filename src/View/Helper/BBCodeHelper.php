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
     * Executes all parsers
     * @param string $text Text
     * @return string
     * @uses readMore()
     * @uses youtube()
     */
    public function parser($text)
    {
        $text = self::readMore($text);
        $text = self::youtube($text);

        return $text;
    }

    /**
     * Parses "read mode" code. Example:
     * <code>
     * [read-more /]
     * </code>
     * @param string $text Text
     * @return string
     */
    public function readMore($text)
    {
        return preg_replace('/(<p>)?\[read\-?more\s?\/\](<\/p>)?/', '<!-- read-more -->', $text);
    }

    /**
     * Removes all BBCode
     * @param string $text Text
     * @return string
     * @uses parser()
     */
    public function remove($text)
    {
        return trim(strip_tags(self::parser($text)));
    }

    /**
     * Parses Youtube code. Example:
     * <code>
     * [youtube]bL_CJKq9rIw[/youtube]
     * </code>
     * @param string $text Text
     * @return string
     * @uses MeTools\Utility\Youtube::getId()
     * @uses MeTools\View\Helper\HtmlHelper::youtube()
     */
    public function youtube($text)
    {
        return preg_replace_callback('/\[youtube](.+?)\[\/youtube]/', function ($matches) {
            if ($this->Html->youtube(isUrl($matches[1]))) {
                return Youtube::getId($matches[1]);
            } else {
                return $matches[1];
            }
        }, $text);
    }
}
