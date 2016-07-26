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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see         http://getbootstrap.com/components/#breadcrumbs Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;

/**
 * Creates breadcrumbs, according to the Bootstrap component.
 */
class BreadcrumbHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['MeTools.Html'];
    
    /**
     * Internal property to add elements
     * @var array
     */
    protected $elements;

    /**
     * Adds a link to the breadcrumbs array
     * @param string $name Text for link
     * @param string|array|null $link URL for link (if empty it won't be a link)
     * @param string|array $options Link attributes e.g. ['id' => 'selected']
     * @uses $elements
     */
    public function add($name, $link = NULL, array $options = []) {
        $this->elements[] = compact('name', 'link', 'options');
    }
    
    /**
     * Returns breadcrumbs
     * @param array $options HTML attributes
     * @param string|array|bool $startText This will be the first crumb, if 
     * `FALSE` it defaults to first crumb in array. Can also be an array, 
     * see `HtmlHelper::getCrumbs` for details
     * @return string Html code
     * @uses $elements
     */
    public function get(array $options = [], $startText = FALSE) {
        //Returns, if there are no elements.
        //This prevent it from being displayed only on the home link
        if(empty($this->elements)) {
            return;
        }
        
        //Fetch last array key
        $keys = array_keys($this->elements);
        $last = array_pop($keys);
        
        foreach($this->elements as $k => $element) {
            //If it's the last element, no link
            if($k == $last) {
                $element['link'] = NULL;
            }
            
            $this->Html->addCrumb($element['name'], $element['link'], $element['options']);
        }
        
        $options = optionDefaults([
            'class' => 'breadcrumb',
            'firstClass' => FALSE,
            'lastClass' => 'active',
        ], $options);
        
        if(empty($startText)) {
            $startText = 'Homepage';
        }
        
        return $this->Html->getCrumbList($options, $startText);
    }
}