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

if (!function_exists('buttonClass')) {
    /**
     * Add button class
     * @param array $options Options
     * @param string $class Class (eg. `default`, `primary`, `success`, etc)
     * @return array
     * @see http://getbootstrap.com/css/#buttons-options
     */
    function buttonClass(array $options = [], $class = 'default')
    {
        //If "class" doesn't contain a button style, adds the "btn-default"
        //  classes
        if (empty($options['class']) || !preg_match(
            '/btn-(default|primary|success|info|warning|danger)/',
            $options['class']
        )) {
            return optionValues([
                'class' => ['btn', sprintf('btn-%s', $class)],
            ], $options);
        }

        return optionValues(['class' => 'btn'], $options);
    }
}

if (!function_exists('optionDefaults')) {
    /**
     * Adds a default values to html options.
     *
     * Example:
     * <code>
     * $options = optionDefaults([
     *  'class' => 'this-is-my-class',
     *  'data-value => 'example-value',
     * ], $options);
     * </code>
     *
     * To provide backward compatibility, this function can accept three
     * arguments (value name, value, options).
     * @param array $values Options values
     * @param array $options Existing options
     * @return array
     */
    function optionDefaults($values, $options = [])
    {
        ///Backward compatibility with three arguments
        if (func_num_args() === 3 &&
            is_string(func_get_arg(0)) &&
            is_string(func_get_arg(1)) &&
            is_array(func_get_arg(2))
        ) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            if (!isset($options[$key])) {
                if (is_array($value)) {
                    $value = implodeRecursive(' ', $value);
                    $value = implode(' ', array_unique(explode(' ', $value)));
                }

                $options[$key] = $value;
            }
        }

        return $options;
    }
}

if (!function_exists('optionValues')) {
    /**
     * Adds values to html options.
     *
     * Example:
     * <code>
     * $options = optionValues([
     *  'class' => 'this-is-my-class',
     *  'data-balue => 'example-value',
     * ], $options);
     * </code>
     *
     * To provide backward compatibility, this function can accept three
     * arguments (value name, value, options).
     * @param array $values Options values
     * @param array $options Existing options
     * @return array
     */
    function optionValues($values, $options = [])
    {
        ///Backward compatibility with three arguments
        if (func_num_args() === 3 &&
            is_string(func_get_arg(0)) &&
            is_string(func_get_arg(1)) &&
            is_array(func_get_arg(2))
        ) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            //Turns new value into string
            if (is_array($value)) {
                $value = implodeRecursive(' ', $value);
            }

            //Turns new value into array
            $value = explode(' ', $value);

            if (!empty($options[$key])) {
                //Merges existing value as array with new value
                $value = am(explode(' ', $options[$key]), $value);
            }

            //Turns final value as string
            $options[$key] = implode(' ', array_unique($value));
        }

        return $options;
    }
}
