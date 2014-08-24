<?php
/**
 * Missing plugin error. It extends the common error page.
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
	
<?php
	$plugin = $this->Html->em(h($plugin));
	$error = __d('me_tools', 'The application is trying to load a file from the %s plugin', $plugin);

	$this->extend('/Common/error_page');
	$this->assign('title', __d('me_tools', 'Missing plugin'));
	$this->assign('error', $error);
	
	echo $this->Html->para(NULL, 
		__d('me_tools', 'Make sure your plugin %s is in the %s directory and was loaded', $plugin, APP_DIR.DS.'Plugin')
	);
?>