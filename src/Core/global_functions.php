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
