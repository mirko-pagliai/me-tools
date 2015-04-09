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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://getbootstrap.com/components/#dropdowns Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\Event\Event;
use Cake\View\Helper;
use Cake\View\View;

/**
 * Library helper
 */
class LibraryHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.MeHtml']];
	
    /**
     * It will contain the output code
     * @var array 
     */
    protected $output = [];

    /**
     * Before layout callback. beforeLayout is called before the layout is rendered.
	 * @param Event $event An Event instance
     * @param string $layoutFile The layout about to be rendered
	 * @uses MeTools\View\Helper\MeHtmlHelper::scriptBlock()
	 * @uses output
     */
    public function beforeLayout(Event $event, $layoutFile) {
        //Writes the output
        if(!empty($this->output)) {
            $this->output = implode(PHP_EOL, array_map(function($v){ 
				return "\t".$v;
			}, $this->output));
			
			$this->Html->scriptBlock(sprintf('$(function() {%s});', PHP_EOL.$this->output.PHP_EOL), ['block' => 'script_bottom']);
        
			//Resets the output
			$this->output = [];
		}
    }
	
    /**
     * Through `slugify.js`, it provides the slug of a field. 
     * 
     * It reads the value of the `$sourceField` field and it sets its slug in the `$targetField`.
     * @param string $sourceField Source field
     * @param string $targetField Target field
	 * @uses MeTools\View\Helper\MeHtmlHelper::js()
	 * @uses output
     */
    public function slugify($sourceField = 'form #title', $targetField = 'form #slug') {
        $this->Html->js('MeTools.slugify.min.js', ['block' => 'script_bottom']);
		
        $this->output[] = sprintf('$().slugify("%s", "%s");', $sourceField, $targetField);
    }
}