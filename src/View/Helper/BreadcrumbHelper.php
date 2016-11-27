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

use MeTools\View\Helper\HtmlHelper;

/**
 * Creates breadcrumbs, according to the Bootstrap component.
 */
class BreadcrumbHelper extends HtmlHelper
{
    use \MeTools\Utility\OptionsParserTrait;

    /**
     * Internal property to add elements
     * @var array
     */
    protected $elements = [];

    /**
     * Adds a link to the breadcrumbs array
     * @param string $name Text for link
     * @param string|array|null $link URL for link (if empty it won't be a link)
     * @param string|array $options Link attributes e.g. ['id' => 'selected']
     * @return void
     * @uses $elements
     */
    public function add($name, $link = null, array $options = [])
    {
        $this->elements[] = compact('name', 'link', 'options');
    }

    /**
     * Returns breadcrumbs.
     *
     * By default, it doesn't return items if no item has been added. In other
     *  words, it doesn't returns only the "home" item (`$startText`).
     * If you want that the method returns the "home" item (`$startText`),
     *  even if other item have been added, you have to set the `onlyStartText`
     *  option as `true`.
     * @param array $options HTML attributes
     * @param string|array|bool $startText This will be the first crumb, if
     * `false` it defaults to first crumb in array. Can also be an array
     * @return string|void Html code
     * @uses $_crumbs
     * @uses $elements
     */
    public function get(array $options = [], $startText = 'Homepage')
    {
        //Returns, if there are no elements.
        //This prevent it from being displayed only on the home link
        if (empty($this->elements) &&
            (!isset($options['onlyStartText']) || $options['onlyStartText'] !== true)
        ) {
            return;
        }

        unset($options['onlyStartText']);

        //Fetch last array key
        $keys = array_keys($this->elements);
        $last = array_pop($keys);

        $this->_crumbs = [];

        foreach ($this->elements as $key => $element) {
            //If it's the last element, no link
            if ($key === $last) {
                $element['link'] = null;
            }

            self::addCrumb($element['name'], $element['link'], $element['options']);
        }

        $options = $this->optionsDefaults([
            'class' => 'breadcrumb',
            'firstClass' => false,
            'lastClass' => 'active',
        ], $options);

        return self::getCrumbList($options, $startText);
    }

    /**
     * Resets the crumb list
     * @return void
     * @uses $_crumbs
     * @uses $elements
     */
    public function reset()
    {
        $this->elements = $this->_crumbs = [];
    }
}
