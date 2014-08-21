<?php
/**
 * Paginator.
 * 
 * Before using this element, remember to load the MePaginator helper provided by MeTools.
 *
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\View\Elements
 */
?>

<?php
	//If the page "2" exists, so if there is more than one page
	if($this->Paginator->hasPage(NULL, 2)) {
		echo $this->Html->div('text-center hidden-xs', $this->Html->ul(array(
			$this->Paginator->prev(sprintf('« %s', __d('me_tools', 'Previous'))),
			$this->Paginator->numbers(),
			$this->Paginator->next(sprintf('%s »', __d('me_tools', 'Next')))
		), array('class' => 'pagination')));
		
		echo $this->Html->div('text-center visible-xs', $this->Html->ul(array(
            $this->Paginator->prev('«'),
            $this->Paginator->numbers(array('modulus' => '6')),
            $this->Paginator->next('»')
		), array('class' => 'pagination')));
	}
?>