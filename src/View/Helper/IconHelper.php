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
 * @since       2.18.12
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;
use MeTools\View\OptionsParser;

/**
 * Provides functionalities for creating HTML icons
 */
class IconHelper extends CakeHtmlHelper
{
    /**
     * Adds icons to text
     * @param string|null $text Text
     * @param \MeTools\View\OptionsParser $options Instance of `OptionsParser`
     * @return array Text with icons and instance of `OptionsParser`
     * @since 2.16.2-beta
     */
    public function addIconToText(?string $text, OptionsParser $options): array
    {
        $icon = $options->consume('icon');
        $align = $options->consume('icon-align');
        if (!$icon) {
            return [$text, $options];
        }

        $icon = $this->icon($icon);
        $result = $icon . ' ' . $text;
        if (empty($text)) {
            $result = $icon;
        } elseif ($align === 'right') {
            $result = $text . ' ' . $icon;
        }

        return [$result, $options];
    }

    /**
     * Internal method to build icon classes
     * @param string|array<string> $icon Icons
     * @return string
     * @since 2.16.2-beta
     */
    protected function buildIconClasses($icon): string
    {
        //Prepends the string "fa-" to any other class
        $icon = preg_replace('/(?<![^ ])(?=[^ ])(?!fa)/', 'fa-', $icon);
        $icon = is_array($icon) ? $icon : (preg_split('/\s+/', $icon ?: '', -1, PREG_SPLIT_NO_EMPTY) ?: []);

        //Adds the "fa" class, if no other "basic" class is present
        if (!count(array_intersect(['fa', 'fab', 'fal', 'far', 'fas'], $icon))) {
            array_unshift($icon, 'fas');
        }

        return implode(' ', array_unique($icon));
    }

    /**
     * Returns icons tag.
     *
     * Example:
     * <code>
     * echo $this->Icon->icon('home');
     * </code>
     * Returns:
     * <code>
     * <i class="fas fa-home"> </i>
     * </code>
     *
     * Example:
     * <code>
     * echo $this->Icon->icon(['hand-o-right', '2x']);
     * </code>
     * Returns:
     * <code>
     * <i class="fas fa-hand-o-right fa-2x"> </i>
     * </code>
     * @param string|array<string> $icons Icons. You can also pass multiple arguments
     * @return string
     * @see http://fontawesome.com Font Awesome icons
     */
    public function icon(...$icons): string
    {
        $toString = function ($value): string {
            return implode(' ', (array)$value);
        };

        $class = $this->buildIconClasses($toString(array_map($toString, $icons)));

        return $this->formatTemplate('tag', [
            'attrs' => $this->templater()->formatAttributes(compact('class')),
            'content' => ' ',
            'tag' => 'i',
        ]);
    }
}
