<?php
/**
 * Error 400.
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
		echo $this->Html->tag('h2', __('Oops...').' '.$this->Html->tag('small', __('This is really embarrassing')));
		echo $this->Html->tag('h4', __('Houston, we have an error!'));
		
		echo $this->Html->para('error-text text-danger bg-danger', sprintf('<strong>%s:</strong> %s', __('Error'), $name));
		
		$html = $this->Html->para(NULL, __('The requested address was not found on this server:'));
		$html .= $this->Html->tag('pre', Router::url($url, true));
		echo $this->Html->div('error-info', $html);
		
		echo $this->Html->para(NULL, __('Have found a bug? Consider the possibility report it.'));

		if(Configure::read('debug'))
			echo $this->Html->div('stack-trace', $this->element('exception_stack_trace'));
	?>
</div>