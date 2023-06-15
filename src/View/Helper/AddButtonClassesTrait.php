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
 * @since       2.25.0
 */

namespace MeTools\View\Helper;

use Tools\Exceptionist;

/**
 * AddButtonClassesTrait
 */
trait AddButtonClassesTrait
{
    /**
     * Adds the given button class to the element options
     * @param array<string, mixed> $options Array options/attributes to add a class to
     * @param string ...$class The button class name being added.
     * @return array<string, mixed> Array of options
     * @throws \ErrorException
     * @see https://getbootstrap.com/docs/5.3/components/buttons/#variants for valid classes
     */
    protected function addButtonClasses(array $options, string ...$class)
    {
        $class = $class ?: ['btn-primary'];

        $options += ['class' => ''];
        $validClasses = ['btn-primary', 'btn-secondary', 'btn-success', 'btn-danger', 'btn-warning', 'btn-info', 'btn-light',
            'btn-dark', 'btn-link', 'btn-outline-primary', 'btn-outline-secondary', 'btn-outline-success', 'btn-outline-danger',
            'btn-outline-warning', 'btn-outline-info', 'btn-outline-light', 'btn-outline-dark', 'btn-lg', 'btn-sm', 'btn-block'];
        $btnAlreadyExists = preg_match('/btn(?!-)/', $options['class']);

        //If a valid class already exists, just checks that the base class `btn` exists and returns
        if (preg_match('/(' . implode('|', $validClasses) . ')/', $options['class'])) {
            if (!$btnAlreadyExists) {
                $options['class'] = preg_replace('/btn-/', 'btn btn-', $options['class'], 1);
            }

            return $options;
        }

        //Checks you are not trying to add an invalid class
        $wrongClass = array_value_first(array_diff($class, $validClasses));
        Exceptionist::isFalse($wrongClass, 'Invalid `'. $wrongClass . '` button class');

        $options['class'] = ltrim($options['class'] . ' ') . implode(' ', $btnAlreadyExists ? $class : ['btn', ...$class]);

        return $options;
    }
}
