<?php
/**
 * Common error page.
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
 * @package		MeTools\View\Errors
 */
?>

<div class="error-page">
	<?php
		echo $this->Html->tag('h2', __d('me_tools', 'Oops...').' '.$this->Html->tag('small', __d('me_tools', 'This is really embarrassing')));
		echo $this->Html->tag('h4', __d('me_tools', 'Houston, we have an error!'));
		
		if($this->fetch('error'))
			echo $this->Html->para('error-text text-danger bg-danger',
				sprintf('<strong>%s:</strong> %s', __d('me_tools', 'Error'), $this->fetch('error')));
		
		if($this->fetch('error_info'))
			echo $this->fetch('error_info');
		
		echo $this->Html->para(NULL, __d('me_tools', 'Have found a bug? Consider the possibility report it').'.');

		if($this->element('exception_stack_trace'))
			echo $this->Html->div('stack-trace', $this->element('exception_stack_trace'));
	?>
</div>