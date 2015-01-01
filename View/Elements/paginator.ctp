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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\View\Elements
 */
?>

<?php if($this->Paginator->hasPage(NULL, 2)): ?>
	<div class="text-center">
		<div class="hidden-xs">
			<ul class="pagination">
				<?php
					echo $this->Paginator->prev($this->Html->icon('caret-left'));
					echo $this->Paginator->numbers();
					echo $this->Paginator->next($this->Html->icon('caret-right'));
				?>
			</ul>
		</div>
		<div class="visible-xs">
			<ul class="pagination">
				<?php
					if($this->Paginator->hasPrev() && $this->Paginator->hasNext()) {
						echo $this->Paginator->prev(NULL, array('icon' => 'caret-left'));
						echo $this->Html->li($this->Html->span(__d('me_tools', 'Page %d', $this->Paginator->current())));
						echo $this->Paginator->next(NULL, array('icon' => 'caret-right'));
					}
					elseif(!$this->Paginator->hasPrev())
						echo $this->Paginator->next(sprintf('%s %s', __d('me_tools', 'Next'), $this->Html->icon('caret-right')));
					else
						echo $this->Paginator->prev(sprintf('%s %s', $this->Html->icon('caret-left'), __d('me_tools', 'Previous')));
				?>
			</ul>
		</div>
	</div>
<?php endif; ?>