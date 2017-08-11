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
 * @see         http://api.cakephp.org/3.4/class-Cake.View.Helper.PaginatorHelper.html PaginatorHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\PaginatorHelper as CakePaginatorHelper;
use MeTools\Utility\OptionsParserTrait;

/**
 * Provides functionalities to the generation of pagers
 */
class PaginatorHelper extends CakePaginatorHelper
{
    use OptionsParserTrait;

    /**
     * Generates a "next" link for a set of paged records
     * @param string $title Title for the link
     * @param array $options Options for pagination link
     * @return string A "next" link or a disabled link
     */
    public function next($title = 'Next >>', array $options = [])
    {
        $options = $this->optionsDefaults(['escape' => false, 'icon-align' => 'right'], $options);
        list($title, $options) = $this->addIconToText($title, $options);

        return parent::next($title, $options);
    }

    /**
     * Generates a "previous" link for a set of paged records
     * @param string $title Title for the link
     * @param array $options Options for pagination link
     * @return string A "previous" link or a disabled link
     */
    public function prev($title = '<< Previous', array $options = [])
    {
        $options = $this->optionsDefaults(['escape' => false], $options);
        list($title, $options) = $this->addIconToText($title, $options);

        return parent::prev($title, $options);
    }
}
