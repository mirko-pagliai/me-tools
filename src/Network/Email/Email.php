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
 */
namespace MeTools\Network\Email;

use Cake\Network\Email\Email as CakeEmail;

/**
 * Email class.
 * 
 * Note that the view variables (`$_viewVars`) are set by `MeTools\View\EmailView`.
 * 
 * Rewrites {@link http://api.cakephp.org/3.0/class-Cake.Network.Email.Email.html Email}.
 * 
 * Example:
 * <code>
 * use MeTools\Network\Email\Email;
 * 
 * $email = new Email('default');
 * $email->from(['me@example.com' => 'My Site'])
 *		->to('you@example.com')
 *		->subject('About')
 *		->send('My message');
 * </code> 
 */
class Email extends CakeEmail {
	/**
	 * Helpers to be used in the render
	 * @var array
	 */
	protected $_helpers = ['Html' => ['className' => 'MeTools.Html']];
	
	/**
	 * View for render
	 * @var string 
	 */
	protected $_viewRender = 'MeTools.Email';
	
	/**
	 * Reset all the internal variables to be able to send out a new email.
	 * @return \MeTools\Network\Email\Email
	 * @uses Cake\Network\Email\Email::reset()
	 * @uses $_helpers
	 * @uses $_viewRender
	 */
	public function reset() {
		parent::reset();
		
		$this->_helpers = ['Html' => ['className' => 'MeTools.Html']];
		$this->_viewRender = 'MeTools.Email';
		
		return $this;
	}
	
	/**
	 * Wrapper for `CakeEmail::viewVars()` method.
	 * @param string|array $one A string or an array of data.
	 * @param string|array $two Value in case $one is a string (which then works as the key). 
	 *	Unused if $one is an associative array, otherwise serves as the values to $one's keys.
	 * @return void
	 */
	public function set($one, $two = NULL) {
		if(is_array($one))
			return is_array($two) ? $this->viewVars(array_combine($one, $two)) : $this->viewVars($one);
		else
			return $this->viewVars([$one => $two]);
	}
}