<?php
/**
 * Missing connection error. It extends the common error page.
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
	$error = __d('me_tools', 'a database connection using "%s" was missing or unable to connect', h($class));
	
	if(!empty($message)) 
		$error = sprintf('%s. %s', $error, __d('me_tools', 'The database server returned this error: "%s"', h($message)));
	
	$this->extend('/Common/error_page');
	$this->assign('title', __d('me_tools', 'Missing database connection'));
	$this->assign('error', $error);
?>