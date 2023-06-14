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
     * @param string $class The class name being added
     * @return array<string, mixed> Array of options
     * @throws \Tools\Exception\NotInArrayException
     */
    protected function addButtonClasses(array $options, string $class): array
    {
        //Adds the `btn-` suffix to the class
        $btnClass = $class && !str_starts_with($class, 'btn-') ? 'btn-' . $class : $class;

        //Checks `$btnClass` is a valid and supported class
        $baseClasses = ['btn-primary', 'btn-secondary', 'btn-success', 'btn-danger', 'btn-warning', 'btn-info', 'btn-light', 'btn-dark', 'btn-link'];
        Exceptionist::inArray($btnClass, $baseClasses, 'Invalid `' . $class . '` class');

        $options += ['class' => ''];

        //Adds the `$btnClass` only if a valid and supported class doesn't already exist
        if (!preg_match('/(' . implode('|', $baseClasses) . ')/', $options['class'])) {
            $options = $this->addClass($options, $btnClass);
        }

        //Prepend the basic `btn` class
        if (!in_array('btn', explode(' ', $options['class']))) {
            $options['class'] = preg_replace('/(' . implode('|', $baseClasses) . ')/', 'btn \1', $options['class']);
        }

        return $options;
    }
}
