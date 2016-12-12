<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 * @see         http://getbootstrap.com/components/#breadcrumbs Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\BreadcrumbsHelper as CakeBreadcrumbsHelper;
use MeTools\Utility\OptionsParserTrait;

/**
 * Creates breadcrumbs, according to the Bootstrap component.
 *
 * This class override `Cake\View\Helper\BreadcrumbsHelper` and improve its
 *  methods.
 *
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = ['Breadcrumbs' => ['className' => 'MeTools.Breadcrumbs']];
 * </code>
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
