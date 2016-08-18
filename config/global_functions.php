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

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

if (!function_exists('af')) {
    /**
     * Cleans an array, removing values equal to `FALSE` (`array_filter()`)
     * @param array $array Array
     * @return array Array
     */
    function af($array)
    {
        return array_filter($array);
    }
}

if (!function_exists('am')) {
    /**
     * Merge a group of arrays.
     * Accepts variable arguments. Each argument will be converted into an
     *  array and then merged.
     * @return array All array parameters merged into one
     */
    function am()
    {
        foreach (func_get_args() as $arg) {
            $array = array_merge(empty($array) ? [] : $array, (array)$arg);
        }

        return $array;
    }
}

if (!function_exists('buttonClass')) {
    /**
     * Add button class
     * @param array $options Options
     * @param string $class Class (eg. `default`, `primary`, `success`, etc)
     * @return array
     * @see http://getbootstrap.com/css/#buttons-options
     */
    function buttonClass($options, $class = 'default')
    {
        //If "class" doesn't contain a button style, adds the "btn-default"
        //  classes
        if (empty($options['class']) || !preg_match('/btn-(default|primary|success|info|warning|danger)/', $options['class'])) {
            return optionValues([
                'class' => ['btn', sprintf('btn-%s', $class)],
            ], $options);
        }

        return optionValues(['class' => 'btn'], $options);
    }
}

if (!function_exists('clearDir')) {
    /**
     * Cleans a directory
     * @param string $directory Directory path
     * @return bool
     */
    function clearDir($directory)
    {
        if (!folderIsWriteable($directory)) {
            return false;
        }

        $success = true;

        //Gets files
        $files = (new Folder($directory))->read(false, ['empty'])[1];

        //Deletes each file
        foreach ($files as $file) {
            if (!(new File($directory . DS . $file))->delete()) {
                $success = false;
            }
        }

        return $success;
    }
}

if (!function_exists('fk')) {
    /**
     * Returns the first key of an array
     * @param array $array Array
     * @return string First key
     */
    function fk($array)
    {
        if (empty($array) || !is_array($array)) {
            return null;
        }

        return current(array_keys($array));
    }
}

if (!function_exists('folderIsWriteable')) {
    /**
     * Checks if a directory and its subdirectories are readable and writable
     * @param string $dir Directory path
     * @return bool
     */
    function folderIsWriteable($dir)
    {
        if (!is_readable($dir) || !is_writeable($dir)) {
            return false;
        }

        $subdirs = (new Folder())->tree($dir, false, 'dir');

        foreach ($subdirs as $subdir) {
            if (!is_readable($subdir) || !is_writeable($subdir)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('fv')) {
    /**
     * Returns the first value of an array
     * @param array $array Array
     * @return mixed First value
     */
    function fv($array)
    {
        if (empty($array) || !is_array($array)) {
            return null;
        }

        return array_values($array)[0];
    }
}

if (!function_exists('getClientIp')) {
    /**
     * Gets the client IP
     * @return string|bool Client IP or `FALSE`
     * @see http://stackoverflow.com/a/15699240/1480263
     */
    function getClientIp()
    {
        if (filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP')) {
            $ip = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR')) {
            $ip = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED')) {
            $ip = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR')) {
            $ip = filter_input(INPUT_SERVER, 'HTTP_FORWARDED_FOR');
        } elseif (filter_input(INPUT_SERVER, 'HTTP_FORWARDED')) {
            $ip = filter_input(INPUT_SERVER, 'HTTP_FORWARDED');
        } elseif (filter_input(INPUT_SERVER, 'REMOTE_ADDR')) {
            $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR');
        }

        if (empty($ip)) {
            return false;
        }

        if ($ip === '::1') {
            return '127.0.0.1';
        }

        return $ip;
    }
}

if (!function_exists('isJson')) {
    /**
     * Checks if a string is JSON
     * @param string $string String
     * @return bool
     */
    function isJson($string)
    {
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}

if (!function_exists('isLocalhost')) {
    /**
     * Checks if the host is the localhost
     * @return bool
     */
    function isLocalhost()
    {
        return getClientIp() === '127.0.0.1';
    }
}

if (!function_exists('isPositive')) {
    /**
     * Checks if a string is a positive number
     * @param string $string String
     * @return bool
     */
    function isPositive($string)
    {
        return is_numeric($string) && $string > 0 && $string == round($string);
    }
}

if (!function_exists('isUrl')) {
    /**
     * Checks whether a url is invalid
     * @param string $url Url
     * @return bool
     */
    function isUrl($url)
    {
        return (bool)preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url);
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
     *  'data-balue => 'example-value',
     * ], $options);
     * </code>
     *
     * To provide backward compatibility, this function can accept three
     * arguments (value name, value, options).
     * @param array $values Options values
     * @param array $options Options
     * @return array Options
     */
    function optionDefaults($values, $options)
    {
        if (func_num_args() === 3) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            if (empty($options[$key])) {
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
     * @param array $options Options
     * @return array Options
     */
    function optionValues($values, $options)
    {
        if (func_num_args() === 3) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            if (empty($options[$key])) {
                $options[$key] = $value;
            } else {
                //Turns into array, adds value and turns again into string
                $options[$key] = preg_split('/\s/', $options[$key]);
                $options[$key] = am($options[$key], (array)$value);
                $options[$key] = implode(' ', array_unique($options[$key]));
            }
        }

        return $options;
    }
}

if (!function_exists('rtr')) {
    /**
     * Returns the relative path (to the APP root) of an absolute path
     * @param string $path Absolute path
     * @return string Relativa path
     */
    function rtr($path)
    {
        return preg_replace(sprintf('/^%s/', preg_quote(ROOT . DS, DS)), null, $path);
    }
}

if (!function_exists('which')) {
    /**
     * Executes the `which` command.
     *
     * It shows the full path of (shell) commands.
     * @param string $command Command
     * @return string Full path of command
     */
    function which($command)
    {
        return exec(sprintf('which %s', $command));
    }
}
