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

/**
 * Provides functionalities for creating HTML icons
 */
class IconHelper extends CakeHtmlHelper
{
    /**
     * Adds icons to text
     * @param string|null $text Text
     * @param array $options Array options/attributes to add a class to
     * @return array Array with text with icons and the array of options
     * @since 2.16.2-beta
     */
    public function addIconToText(?string $text, array $options): array
    {
        $icon = $options['icon'] ?? null;
        $align = $options['icon-align'] ?? null;
        unset($options['icon'], $options['icon-align']);

        if (!$icon) {
            return [$text, $options];
        }

        $icon = $this->icon($icon);
        $result = $icon . ' ' . $text;
        if (!$text) {
            $result = $icon;
        } elseif ($align === 'right') {
            $result = $text . ' ' . $icon;
        }

        return [$result, $options];
    }

    /**
     * Returns icons tag.
     *
     * Example:
     * <code>
     * echo $this->Icon->icon('fas fa-home');
     * </code>
     * Returns:
     * <code>
     * <i class="fas fa-home"> </i>
     * </code>
     *
     * Example:
     * <code>
     * echo $this->Icon->icon(['fas', 'fa-hand-o-right', 'fa-2x']);
     * </code>
     * Returns:
     * <code>
     * <i class="fas fa-hand-o-right fa-2x"> </i>
     * </code>
     * @param string|array ...$icons Icons. You can also pass multiple arguments
     * @return string
     * @see http://fontawesome.com Font Awesome icons
     */
    public function icon(string|array ...$icons): string
    {
        if (count($icons) === 1 && is_array($icons[0])) {
            $icons = $icons[0];
        }
        /** @var string[] $icons */
        $class = implode(' ', $icons);

        return $this->formatTemplate('tag', [
            'attrs' => $this->templater()->formatAttributes(compact('class')),
            'content' => ' ',
            'tag' => 'i',
        ]);
    }
}
