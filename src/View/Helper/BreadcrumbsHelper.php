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
 * @see         https://api.cakephp.org/3.4/class-Cake.View.Helper.BreadcrumbsHelper.html BreadcrumbsHelper
 * @see         http://getbootstrap.com/components/#breadcrumbs Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\BreadcrumbsHelper as CakeBreadcrumbsHelper;
use MeTools\Utility\OptionsParserTrait;

/**
 * Creates breadcrumbs, according to the Bootstrap component
 */
class BreadcrumbsHelper extends CakeBreadcrumbsHelper
{
    use OptionsParserTrait;

    /**
     * Renders the breadcrumbs trail
     * @param array $attributes Array of attributes applied to the `wrapper`
     *  template
     * @param array $separator Array of attributes for the `separator` template
     * @return string The breadcrumbs trail
     */
    public function render(array $attributes = [], array $separator = [])
    {
        if (empty($this->crumbs)) {
            return parent::render($attributes, $separator);
        }

        //Removes the url for the last crumb
        end($this->crumbs);
        $last = key($this->crumbs);
        $this->crumbs[$last]['url'] = null;

        $attributes = $this->optionsDefaults(['class' => 'breadcrumb'], $attributes);

        return parent::render($attributes, $separator);
    }
}
