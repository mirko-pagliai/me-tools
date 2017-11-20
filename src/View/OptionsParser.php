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
     * Default values
     * @var array
     */
    protected $defaults;

    /**
     * Existing options
     * @var array
     */
    protected $options;

    /**
     * Keys of options to be exploded
     * @var array
     */
    protected $toBeExploded = ['class', 'data-toggle'];

    /**
     * Constructor
     * @param array $options Existing options
     * @param array $defaults Default values
     * @return $this
     * @uses buildValue()
     * @uses $defaults
     * @uses $options
     */
    public function __construct(array $options = [], array $defaults = [])
    {
        array_walk($defaults, [$this, 'buildValue']);
        array_walk($options, [$this, 'buildValue']);

        $this->defaults = $defaults;
        $this->options = $options;

        return $this;
    }

    /**
     * Internal method to build a value
     * @param mixed $value Option value
     * @param string $key Option key
     * @return mixed
     * @uses $toBeExploded
     */
    protected function buildValue(&$value, $key)
    {
        if (in_array($key, $this->toBeExploded)) {
            if (!is_array($value)) {
                $value = explode(' ', $value);
            } else {
                //Collapses multi-dimensional arrays into a single dimension
                $value = array_values(Hash::flatten($value));
            }
            $value = array_unique(array_filter($value));
            sort($value);
            $value = implode(' ', $value);
        }

        if (is_string($value)) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * Add a value.
     *
     * You can also pass an array with the keys and values as the only argument.
     * @param string|array $key Key or array with keys and values
     * @param mixed|null $value Value
     * @return $this
     * @uses buildValue()
     * @uses $options
     */
    public function add($key, $value = null)
    {
        if (is_array($key)) {
            return array_map([$this, __METHOD__], array_keys($key), $key);
        }

        $this->options[$key] = $this->buildValue($value, $key);

        return $this;
    }

    /**
     * Append a value.
     *
     * If the existing value and the value to append are both strings, the
     *  strings will be concatenated. In any other cases, an array of elements
     *  will be created.
     *
     * You can also pass an array with the keys and values as the only argument.
     * @param string|array $key Key or array with keys and values
     * @param mixed|null $value Value
     * @return $this
     * @uses add()
     */
    public function append($key, $value = null)
    {
        if (is_array($key)) {
            return array_map([$this, __METHOD__], array_keys($key), $key);
        }

        $existing = $this->get($key);

        if (in_array($key, $this->toBeExploded)) {
            $existing = explode(' ', $existing);
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
     * @uses get()
     * @uses $toBeExploded
     */
    public function contains($key, $value)
    {
        $existing = $this->get($key);

        if (in_array($key, $this->toBeExploded)) {
            $existing = explode(' ', $existing);
        }

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
     * @param string|array $key Key or array of keys
     * @return $this
     * @uses $options
     */
    public function delete($key)
    {
        if (is_array($key)) {
            return array_map([$this, __METHOD__], $key);
        }

        unset($this->options[$key]);

        return $this;
    }

    /**
     * Checks if a key exists
     * @param string $key Key
     * @return bool
     * @uses $defaults
     * @uses $options
     */
    public function exists($key)
    {
        return isset($this->options[$key]) || isset($this->defaults[$key]);
    }

    /**
     * Gets the value for a key
     * @param string $key Key
     * @return mixed
     * @uses $defaults
     * @uses $options
     */
    public function get($key)
    {
        $default = isset($this->defaults[$key]) ? $this->defaults[$key] : null;

        return Hash::get($this->options, $key, $default);
    }

    /**
     * Returns options as array
     * @return array
     * @uses $defaults
     * @uses $options
     */
    public function toArray()
    {
        $options = array_merge($this->defaults, $this->options);

        ksort($options);

        return $options;
    }

    /**
     * Returns options as string
     * @return string
     * @uses toArray()
     */
    public function toString()
    {
        $options = $this->toArray();

        //Transforms values as strings
        array_walk($options, function (&$value, $key) {
            if (is_array($value)) {
                $value = implode(' ', $value);
            } elseif (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $value = 'null';
            }

            $value = $key . '="' . $value . '"';
        });

        return implode(' ', $options);
    }
}