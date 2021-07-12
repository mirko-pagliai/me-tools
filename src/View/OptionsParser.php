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
 * @since       2.16.2-beta
 */
namespace MeTools\View;

use Cake\Utility\Hash;

/**
 * An options parser
 */
class OptionsParser
{
    /**
     * Instance of `OptionsParser` for default values
     * @var \MeTools\View\OptionsParser
     */
    public $Default;

    /**
     * Existing options
     * @var array
     */
    protected $options = [];

    /**
     * Keys of options to be exploded
     * @var array<string>
     */
    protected $toBeExploded = ['class', 'data-toggle'];

    /**
     * Constructor
     * @param array $options Existing options
     * @param array|null $defaults Default values
     */
    public function __construct(array $options = [], ?array $defaults = [])
    {
        array_walk($options, [$this, 'buildValue']);
        $this->options = $options;

        if (!is_null($defaults)) {
            $this->Default = new OptionsParser($defaults, null);
        }
    }

    /**
     * Internal method to build a value
     * @param mixed $value Option value
     * @param string $key Option key
     * @return mixed
     */
    protected function buildValue(&$value, string $key)
    {
        if (in_array($key, $this->toBeExploded)) {
            //Collapses multi-dimensional arrays into a single dimension
            $value = array_clean(is_array($value) ? Hash::flatten($value) : explode(' ', $value));
            sort($value);
            $value = implode(' ', $value);
        }

        return is_string($value) ? trim($value) : $value;
    }

    /**
     * Adds a value.
     *
     * You can also pass an array with the keys and values as the only argument.
     * @param string|array<string, mixed> $key Key or array with keys and values
     * @param mixed|null $value Value
     * @return $this
     */
    public function add($key, $value = null)
    {
        if (is_array($key)) {
            $callable = [$this, __METHOD__];
            if (is_callable($callable)) {
                array_map($callable, array_keys($key), $key);
            }

            return $this;
        }

        $this->options[$key] = $this->buildValue($value, $key);

        return $this;
    }

    /**
     * Adds button classes.
     *
     * Classes can be passed as string, array or multiple arguments, with or
     *  without the `btn-` prefix.
     *
     * Examples:
     * <code>
     * $options->addButtonClasses('primary lg');
     * $options->addButtonClasses('primary', 'lg');
     * </code>
     * @param string $classes Classes string, array or multiple arguments
     * @return $this
     */
    public function addButtonClasses(string ...$classes)
    {
        $baseClasses = ['primary', 'secondary', 'success', 'danger', 'warning',
            'info', 'light', 'dark', 'link'];
        $allClasses = array_merge($baseClasses, ['outline-primary',
            'outline-secondary', 'outline-success', 'outline-danger',
            'outline-warning', 'outline-info', 'outline-light', 'outline-dark',
            'lg', 'sm', 'block']);

        //If a base class already exists, it just appends the `btn` class
        $existing = $this->get('class');
        if ($existing && preg_match('/btn\-(' . implode('|', $baseClasses) . ')/', $existing)) {
            return $this->append('class', 'btn');
        }

        $classes = preg_split('/\s+/', $classes ? implode(' ', $classes) : 'btn-light', -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $classes = collection($classes)
            ->map(function (string $class): string {
                return string_starts_with($class, 'btn-') ? $class : 'btn-' . $class;
            })
            ->filter(function (string $class) use ($allClasses): bool {
                return preg_match('/^btn\-(' . implode('|', $allClasses) . ')$/', $class) !== 0;
            });

        return $this->append('class', array_merge(['btn'], $classes->toList()));
    }

    /**
     * Appends a value.
     *
     * If the existing value and the value to append are both strings, the
     *  strings will be concatenated. In any other cases, an array of elements
     *  will be created.
     *
     * You can also pass an array with the keys and values as the only argument.
     * @param string|array<string, mixed> $key Key or array with keys and values
     * @param mixed|null $value Value
     * @return $this
     */
    public function append($key, $value = null)
    {
        if (is_array($key)) {
            $callable = [$this, __METHOD__];
            if (is_callable($callable)) {
                array_map($callable, array_keys($key), $key);
            }

            return $this;
        }

        $existing = $this->get($key);

        if (in_array($key, $this->toBeExploded)) {
            $existing = is_string($existing) ? explode(' ', $existing) : $existing;
            $value = is_array($value) ? $value : explode(' ', $value);
        }

        if (is_string($existing) && is_string($value)) {
            $value = $existing . ' ' . trim($value);
        } elseif (!is_null($existing)) {
            $value = array_merge((array)$existing, (array)$value);
        }

        $this->add($key, $value);

        return $this;
    }

    /**
     * Used to read and delete a value from a key
     * @param string $key Key
     * @return mixed
     * @since 2.16.10
     */
    public function consume(string $key)
    {
        $value = $this->get($key);
        $this->delete($key);

        return $value;
    }

    /**
     * Checks if a key contains a value.
     *
     * If the existing value is an array:
     *  - if you pass an array, the elements of the two arrays will be compared;
     *  - otherwise, it will be checked if the value you have passed is
     *      contained in the array.
     *
     * In all other cases, the value you have passed and your existing value
     *  will be compared.
     * @param string $key Key
     * @param mixed $value Value
     * @return bool
     */
    public function contains(string $key, $value): bool
    {
        if (!$this->exists($key)) {
            return false;
        }

        $existing = $this->get($key);
        $existing = in_array($key, $this->toBeExploded) ? explode(' ', $existing) : $existing;

        if (is_array($existing)) {
            if (is_array($value)) {
                return empty(array_diff($existing, $value)) && empty(array_diff($value, $existing));
            }

            return in_array($value, $existing, true);
        }

        return $existing === $value;
    }

    /**
     * Delete a key
     * @param string $key Key
     * @return $this
     */
    public function delete(string ...$key)
    {
        foreach ($key as $k) {
            unset($this->options[$k]);
        }

        return $this;
    }

    /**
     * Checks if a key exists
     * @param string $key Key
     * @return bool
     */
    public function exists(string $key): bool
    {
        return isset($this->options[$key]) || isset($this->Default->options[$key]);
    }

    /**
     * Gets the value for a key
     * @param string $key Key
     * @return mixed
     */
    public function get(string $key)
    {
        $default = $this->Default ? $this->Default->get($key) : null;

        return Hash::get($this->options, $key, $default);
    }

    /**
     * Returns options as array
     * @return array
     */
    public function toArray(): array
    {
        $options = $this->options;
        if ($this->Default) {
            $options = array_merge($this->Default->options, $options);
        }

        ksort($options);

        return $options;
    }

    /**
     * Returns options as string
     * @return string
     */
    public function toString(): string
    {
        $options = $this->toArray();

        return implode(' ', array_map(function ($value, string $key): string {
            if (is_array($value)) {
                $value = implode(' ', $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $value = 'null';
            }

            return $key . '="' . $value . '"';
        }, $options, array_keys($options)));
    }

    /**
     * Builds keys for tooltip.
     *
     * Gets `tooltip` and `tooltip-align` keys and builds `data-tootle` and
     *  `data-placement` keys, as required by Bootstrap tooltips.
     * @return $this
     * @see http://getbootstrap.com/docs/4.0/components/tooltips
     */
    public function tooltip()
    {
        $tooltip = $this->consume('tooltip');
        if (!$tooltip) {
            return $this;
        }

        $this->append('data-toggle', 'tooltip');
        $this->add('title', trim(h(strip_tags($tooltip))));

        if ($this->exists('tooltip-align')) {
            $this->add('data-placement', $this->consume('tooltip-align'));
        }

        return $this;
    }
}
