<?php
App::uses('MeToolsAppHelper', 'MeTools.View/Helper');

/**
 * Quickly include modules and libraries.
 *
 * Bootstrap ({@link http://twitter.github.com/bootstrap link}):
 * <code>
 * echo $this->Module->bootstrap();
 * </code>
 *
 * jQuery ({@link http://jquery.com link}):
 * <code>
 * echo $this->Module->jquery();
 * </code>
 *
 * By default, the helper doesn't include "inline". To include "inline":
 * <code>
 * echo $this->Module->jquery(true);
 * </code>
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		AGPL License (http://www.gnu.org/licenses/agpl.txt)
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools.View.Helper
 */
class ModuleHelper extends MeToolsAppHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('MeTools.MeHtml');

	/**
	 * Add BootStrap
	 *
	 * {@link http://twitter.github.com/bootstrap}
	 * @param bool $inline Inline option
	 * @return mixed
	 */
	public function bootstrap($inline = false) {
		return $this->MeHtml->script('/MeTools/js/bootstrap.min', array('inline' => $inline)).
				$this->MeHtml->css('/MeTools/css/bootstrap.min', null, array('inline' => $inline));
	}

	/**
	 * Add jQuery
	 *
	 * {@link http://jquery.com}
	 * @param bool $inline Inline option
	 * @return mixed
	 */
	public function jquery($inline = false) {
		return $this->MeHtml->script('/MeTools/js/jquery.min', array('inline' => $inline));
	}
}