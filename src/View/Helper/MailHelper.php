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
namespace MeTools\View\Helper;

use Cake\View\Helper;

/**
 * Mail helper
 */
class MailHelper extends Helper
{
    /**
     * Method to obfuscate an email address.
     * @param string $mail Mail address
     * @return string
     */
    public function obfuscate($mail)
    {
        return preg_replace_callback('/^([^@]+)(.*)$/', function ($matches) {
            $lenght = floor(strlen($matches[1]) / 2);

            $name = substr($matches[1], 0, $lenght) . str_repeat('*', $lenght);

            return $name . $matches[2];
        }, $mail);
    }
}
