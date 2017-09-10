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
namespace MeTools\Utility;

/**
 * An options parser for helpers that generate html code
 */
trait OptionsParserTrait
{
    /**
     * Internal method to set a value
     * @param string $key Key value
     * @param mixed $value Value
     * @param array $options Options
     * @return array Options
     * @uses toString()
     */
    protected function setValue($key, $value, array $options)
    {
        $options[$key] = $this->toString($value);

        return $options;
    }

    /**
     * Internal method to turn a string into an array
     * @param mixed $value String
     * @return array
     */
    protected function toArray($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return array_unique(preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Internal method to turn an array into a string
     * @param mixed $value Array
     * @return string
     */
    protected function toString($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return implode(' ', array_unique($value));
    }

    /**
     * Adds button classes.
     *
     * Classes can be passed as string or array, with or without the `btn-`
     *  prefix.
     *
     * Example:
     * <code>
     * $this->addButtonClasses('primary lg', $options);
     * </code>
     *
     * Or:
     * <code>
     * $this->addButtonClasses(['btn-primary', 'lg'], $options);
     * </code>
     * @param array $options Array of HTML attributes
     * @param string|array $classes Classes (eg. `default`, `primary`,
     *  `success`, etc), with or without the `btn-` prefix
     * @return array Options
     * @uses toArray()
     * @uses optionsValues()
     */
    public function addButtonClasses(array $options, $classes = 'btn-secondary')
    {
        $baseClasses = [
            'primary',
            'secondary',
            'success',
            'danger',
            'warning',
            'info',
            'light',
            'dark',
            'link',
        ];
        $allClasses = array_merge($baseClasses, [
            'outline-primary',
            'outline-secondary',
            'outline-success',
            'outline-danger',
            'outline-warning',
            'outline-info',
            'outline-light',
            'outline-dark',
            'lg',
            'sm',
            'block',
        ]);

        //If a base class already exists, it just adds the `btn` class
        if (!empty($options['class']) && preg_match(
            '/btn\-?(' . implode('|', $baseClasses) . ')/',
            $options['class']
        )) {
            return $this->optionsValues(['class' => 'btn'], $options);
        }

        $classes = collection($this->toArray($classes))
            ->filter(function ($class) use ($allClasses) {
                return preg_match('/^(btn\-)?(' . implode('|', $allClasses) . ')$/', $class);
            })
            ->map(function ($class) {
                //Adds the `btn-` prefix to each class
                if (substr($class, 0, 4) !== 'btn-') {
                    return sprintf('btn-%s', $class);
                }

                return $class;
            })
            ->toList();

        //Prepend the `btn` class
        array_unshift($classes, 'btn');

        return $this->optionsValues(['class' => $classes], $options);
    }

    /**
     * Adds icon to text
     * @param string $text Text
     * @param array $options Array of HTML attributes
     * @return array Text with icons as first value, options as second value
     * @uses icon()
     */
    public function addIconToText($text, array $options)
    {
        $align = empty($options['icon-align']) ? false : $options['icon-align'];
        unset($options['icon-align']);

        if (empty($options['icon'])) {
            return [$text, $options];
        }

        $icon = $this->icon($options['icon']);
        unset($options['icon']);

        if (empty($text)) {
            $text = $icon;
        } elseif ($align === 'right') {
            $text = sprintf('%s %s', $text, $icon);
        } else {
            $text = sprintf('%s %s', $icon, $text);
        }

        return [$text, $options];
    }

    /**
     * Adds tooltip options
     * @param array $options Array of HTML attributes
     * @return array
     * @uses optionsValues()
     */
    public function addTooltip($options)
    {
        if (!empty($options['tooltip'])) {
            $options = $this->optionsValues(['data-toggle' => 'tooltip'], $options);
            $options['title'] = trim(h(strip_tags($options['tooltip'])));

            if (!empty($options['tooltip-align'])) {
                $options = $this->optionsValues(['data-placement' => $options['tooltip-align']], $options);
            }
        }

        unset($options['tooltip'], $options['tooltip-align']);

        return $options;
    }

    /**
     * Returns icons.
     *
     * Icons can be passed as string, as array or as multiple arguments, with
     *  or without the `fa-` prefix.
     *
     * Examples:
     * <code>
     * echo $this->icon('home');
     * echo $this->icon(['hand-o-right', '2x']);
     * echo $this->icon('hand-o-right', '2x');
     * </code>
     * @param string|array $icons Icons
     * @return string
     * @uses toArray()
     * @uses toString()
     */
    public function icon($icons)
    {
        if (func_num_args() > 1) {
            $icons = func_get_args();
        }

        $icons = $this->toArray($icons);

        //Prepends the string "fa-" to any other class
        $icons = preg_replace('/(?<![^ ])(?=[^ ])(?!fa)/', 'fa-', $icons);

        //Prepends the `fa` class
        array_unshift($icons, 'fa');

        if (!$this->getTemplates('icon')) {
            $this->setTemplates(['icon' => '<i class="{{icons}}"> </i>']);
        }

        return $this->formatTemplate('icon', ['icons' => $this->toString($icons)]);
    }

    /**
     * Adds default values.
     *
     * Example:
     * <code>
     * $this->optionsDefaults([
     *  'class' => 'this-is-my-class',
     *  'data-value => ['first-value', 'second-value'],
     * ], $options);
     * </code>
     *
     * To provide backward compatibility, this function can accept three
     * arguments (value key, value and options). Example:
     * <code>
     * $this->optionsDefaults('class', 'this-is-my-class', $options);
     * $this->optionsDefaults('data-value, [
     *  'first-value',
     *  'second-value',
     * ], $options);
     * </code>
     * @param array $values Array of key values and values
     * @param array $options Array of HTML attributes
     * @return array Options
     * @uses setValue()
     */
    public function optionsDefaults($values, $options)
    {
        //If called with three arguments, the first is the key, the second is
        //  the value and the third are the options
        if (func_num_args() === 3 && is_array(func_get_arg(2))) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            if (!isset($options[$key])) {
                $options = $this->setValue($key, $value, $options);
            }
        }

        return $options;
    }

    /**
     * Adds values.
     *
     * Example:
     * <code>
     * $this->optionsValues([
     *  'class' => 'this-is-my-class',
     *  'data-value => ['first-value', 'second-value'],
     * ], $options);
     * </code>
     *
     * To provide backward compatibility, this function can accept two
     * arguments (value key and value). Example:
     * <code>
     * $this->optionsValues('class','this-is-my-class', $options);
     * $this->optionsValues('data-value, [
     *  'first-value',
     *  'second-value',
     * ], $options);
     * </code>
     * @param array $values Array of key values and values
     * @param array $options Array of HTML attributes
     * @return array Options
     * @uses setValue()
     * @uses toArray()
     * @uses toString()
     */
    public function optionsValues($values, $options)
    {
        //If called with three arguments, the first is the key, the second is
        //  the value and the third are the options
        if (func_num_args() === 3 && is_array(func_get_arg(2))) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            //Turns value into a string
            $value = $this->toString($value);

            if (isset($options[$key])) {
                //Chains new value to the existing value
                $value = $options[$key] . ' ' . $value;

                //Turns first into an array and finally into a string again.
                //Turning into array will also remove duplicates
                $options[$key] = $this->toString($this->toArray($value));
            } else {
                $options = $this->setValue($key, $value, $options);
            }
        }

        return $options;
    }
}
