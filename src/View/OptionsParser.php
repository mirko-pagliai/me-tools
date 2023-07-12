<?php
/** @noinspection PhpMissingReturnTypeInspection */
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
 * @deprecated 2.25.0 It will be removed in a later release
 */
class OptionsParser
{
    /**
     * Default values
     * @var array
     */
    protected array $defaults = [];

    /**
     * Existing options
     * @var array
     */
    protected array $options = [];

    /**
     * Keys of options to be exploded
     */
    protected const TO_BE_EXPLODED = ['class', 'data-toggle'];

    /**
     * Constructor
     * @param array $options Existing options
     * @param array $defaults Default values
     */
    public function __construct(array $options = [], array $defaults = [])
    {
        deprecationWarning('Deprecated. It will be removed in a later release');

        $this->addDefault($defaults);
        $this->add($options);
    }

    /**
     * Internal method to build values
     * @param mixed $value Option value
     * @param string $key Option key
     * @return mixed
     */
    protected function buildValue(&$value, string $key)
    {
        //Collapses multi-dimensional arrays into a single dimension
        if (in_array($key, self::TO_BE_EXPLODED)) {
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
     * @param string|array<string, mixed> $key Key as string or array with keys and values
     * @param mixed|null $value Value
     * @return $this
     */
    public function add($key, $value = null)
    {
        if (is_array($key)) {
            array_map([$this, 'add'], array_keys($key), $key);

            return $this;
        }

        $this->options[$key] = $this->buildValue($value, $key);

        return $this;
    }

    /**
     * Adds button classes.
     *
     * Classes can be passed as string, array or multiple arguments, with or without the `btn-` prefix.
     *
     * Examples:
     * <code>
     * $options->addButtonClasses('primary lg');
     * $options->addButtonClasses('primary', 'lg');
     * </code>
     * @param string ...$classes Classes as string, or multiple arguments
     * @return $this
     */
    public function addButtonClasses(string ...$classes)
    {
        $baseClasses = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark', 'link'];
        $allClasses = [...$baseClasses, 'outline-primary', 'outline-secondary', 'outline-success', 'outline-danger', 'outline-warning', 'outline-info', 'outline-light', 'outline-dark', 'lg', 'sm', 'block'];

        //If a base class already exists, it just appends the `btn` class
        $existing = $this->get('class');
        if ($existing && preg_match('/btn-(' . implode('|', $baseClasses) . ')/', $existing)) {
            return $this->append('class', 'btn');
        }

        $classes = preg_split('/\s+/', implode(' ', $classes) ?: 'btn-light', -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $classes = array_map(fn(string $class): string => 'btn-' . ltrim($class, 'btn-'), $classes);

        return $this->append('class', ['btn', ...preg_grep('/^btn-(' . implode('|', $allClasses) . ')$/', $classes) ?: []]);
    }

    /**
     * Adds a default value.
     *
     * You can also pass an array with the keys and values as the only argument.
     * @param string|array<string, mixed> $key Key or array with keys and values
     * @param mixed|null $value Value
     * @return $this
     */
    public function addDefault($key, $value = null)
    {
        if (is_array($key)) {
            array_map([$this, 'addDefault'], array_keys($key), $key);

            return $this;
        }

        $this->defaults[$key] = $this->buildValue($value, $key);

        return $this;
    }

    /**
     * Appends a value.
     *
     * If the existing value and the value to append are both strings, the strings will be concatenated. In any other
     *  cases, an array of elements will be created.
     *
     * You can also pass an array with the keys and values as the only argument.
     * @param string|array<string, mixed> $key Key or array with keys and values
     * @param mixed|null $value Value
     * @return $this
     */
    public function append($key, $value = null)
    {
        if (is_array($key)) {
            array_map([$this, 'append'], array_keys($key), $key);

            return $this;
        }

        $existing = $this->get($key);

        if (in_array($key, self::TO_BE_EXPLODED)) {
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
     *  - otherwise, it will be checked if the value you have passed is contained in the array.
     *
     * In all other cases, the value you have passed and your existing value will be compared.
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
        $existing = in_array($key, self::TO_BE_EXPLODED) ? explode(' ', $existing) : $existing;

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
     * @param string ...$key Key as string, or multiple arguments
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
        return isset($this->options[$key]) || isset($this->defaults[$key]);
    }

    /**
     * Gets the value for a key
     * @param string $key Key
     * @return mixed
     */
    public function get(string $key)
    {
        $default = $this->defaults[$key] ?? null;

        return Hash::get($this->options, $key, $default);
    }

    /**
     * Returns options as array
     * @return array
     */
    public function toArray(): array
    {
        $options = $this->options + $this->defaults ?: [];

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
     * Gets `tooltip` and `tooltip-align` keys and builds `data-bs-toggle` and `data-bs-placement` keys.
     * @return $this
     * @see https://getbootstrap.com/docs/5.2/components/tooltips
     */
    public function tooltip()
    {
        $tooltip = $this->consume('tooltip');
        if (!$tooltip) {
            return $this;
        }

        $this->append('data-bs-toggle', 'tooltip');
        $this->add('data-bs-title', trim(h(strip_tags($tooltip))));

        if ($this->exists('tooltip-align')) {
            $this->add('data-bs-placement', $this->consume('tooltip-align'));
        }

        return $this;
    }
}
