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
namespace MeTools\Utility;

/**
 * An options parser
 */
class OptionsParser
{
    /**
     * Options
     * @var array
     */
    protected $options;

    /**
     * Construct
     * @param array $options Options to parse
     * @return \MeTools\Utility\OptionsParser
     * @uses $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Method called by `var_dump()` when dumping an object to get the
     *  properties that should be shown
     * @return array
     * @uses $options
     */
    public function __debugInfo()
    {
        return $this->options;
    }

    /**
     * Turns a string into an array
     * @param mixed $value String
     * @return array
     */
    protected function _toArray($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return array_unique(preg_split('/\s/', $value, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Turns an array into a string
     * @param mixed $value Array
     * @return string
     */
    protected function _toString($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return implode(' ', array_unique($value));
    }

    /**
     * Gets a value from options
     * @param string $key Key value
     * @return string
     */
    public function get($key)
    {
        if (!isset($this->options[$key])) {
            return null;
        }

        return $this->options[$key];
    }

    /**
     * Retunrs options as array
     * @return array
     * @uses $options
     */
    public function toArray()
    {
        return $this->options;
    }
}
