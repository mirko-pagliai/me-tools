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
        //Collapses multi-dimensional arrays into a single dimension
        if (is_array($value)) {
            $value = array_values(Hash::flatten($value));
        }

        //Some options have to be exploded
        if (in_array($key, $this->toBeExploded)) {
            if (!is_array($value)) {
                $value = explode(' ', $value);
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

        array_walk($options, function (&$value, $key) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $value = 'null';
            }

            $value = $key . '="' . $value . '"';
        });

        return implode(' ', $options);
    }
}
