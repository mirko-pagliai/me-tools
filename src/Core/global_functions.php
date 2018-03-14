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
 */
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use MeTools\View\OptionsParser;

if (!function_exists('clearDir')) {
    /**
     * Cleans a directory, deleting all the files, even in sub-directories
     * @param string $directory Directory path
     * @return bool
     */
    function clearDir($directory)
    {
        $success = true;

        //Gets files
        $files = (new Folder($directory))->tree(false, ['empty'])[1];

        //Deletes each file
        foreach ($files as $file) {
            if (!(new File($file))->delete()) {
                $success = false;
            }
        }

        return $success;
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
        if (!is_readable($dir) || !is_writable($dir)) {
            return false;
        }

        //Checks each sub-directory
        foreach ((new Folder())->tree($dir, false, 'dir') as $subdir) {
            if (!is_readable($subdir) || !is_writable($subdir)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('getChildMethods')) {
    /**
     * Gets the class methods' names, but unlike the `get_class_methods()`
     *  function, this function excludes the methods of the parent class
     * @param string $class Class name
     * @param string|array $exclude Methods to be excluded
     * @return array|null
     */
    function getChildMethods($class, $exclude = [])
    {
        $methods = get_class_methods($class);
        $parent = get_parent_class($class);

        if ($parent) {
            $methods = array_diff($methods, get_class_methods($parent));
        }

        if ($exclude) {
            $methods = array_diff($methods, (array)$exclude);
        }

        return is_array($methods) ? array_values($methods) : null;
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
        if (!is_string($string)) {
            return false;
        }

        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
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

if (!function_exists('isWin')) {
    /**
     * Returns `true` if the environment is Windows
     * @return bool
     */
    function isWin()
    {
        return DS === '\\';
    }
}

if (!function_exists('optionsParser')) {
    /**
     * Returns and instance of `OptionsParser`
     * @param array $options Existing options
     * @param array|null $defaults Default values
     * @return OptionsParser
     */
    function optionsParser(array $options = [], $defaults = [])
    {
        return new OptionsParser($options, $defaults);
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
        return preg_replace(sprintf('/^%s/', preg_quote(Folder::slashTerm(ROOT), DS)), null, $path);
    }
}

if (!function_exists('which')) {
    /**
     * Executes the `which` command and shows the full path of (shell) commands
     * @param string $command Command
     * @return string|null
     */
    function which($command)
    {
        $executable = isWin() ? 'where' : 'which';

        exec(sprintf('%s %s 2>&1', $executable, $command), $path, $exitCode);

        $path = isWin() && !empty($path) ? array_map('escapeshellarg', $path) : $path;

        return $exitCode === 0 && !empty($path[0]) ? $path[0] : null;
    }
}
