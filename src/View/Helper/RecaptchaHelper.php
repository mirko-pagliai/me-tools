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
 * @see			https://www.google.com/recaptcha reCAPTCHA site
 * @see			http://www.google.com/recaptcha/mailhide/apikey reCAPTCHA for mails
 */
namespace MeTools\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\View\View;

require_once \MeTools\Utility\Plugin::path('MeTools', 'vendor'.DS.'Recaptcha'.DS.'recaptchalib.php');

/**
 * Provides several methods for reCAPTCHA.
 *
 * Before using this helper, you have to configure keys in `app/Config/recaptcha.php`.
 * You can use as example the file `app/Plugin/MeTools/Config/recaptcha.default.php`.
 */
class RecaptchaHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['MeTools.Html'];
	
	/**
	 * Construct the widgets and binds the default context providers.
	 * 
	 * This method only ewrites the default configuration (`$_defaultConfig`).
	 * @param Cake\View\View $view The View this helper is being attached to
	 * @param array $config Configuration settings for the helper
	 */
	public function __construct(View $view, $config = []) {
        parent::__construct($view, $config);
		
		//Loads the configuration file
		Configure::load('recaptcha');
	}

	/**
	 * Internal function to obfuscate an email address.
	 * @param string $mail Mail address
	 * @return string Mail address obfuscated
	 * @see http://stackoverflow.com/a/20545505/1480263
	 */
	protected function _obfuscate($mail) {
		$name = implode(array_slice($mail = explode("@", $mail), 0, count($mail) - 1), '@');
		$lenght  = floor(strlen($name) / 2);

		return substr($name, 0, $lenght).str_repeat('*', $lenght)."@".end($mail);
	}
	
	/**
	 * Displays the reCAPTCHA widget
	 * @param array $options reCAPTCHA widget options
	 * @param array $optionsScript Script option
	 * @return string Html
	 * @throws \Cake\Network\Exception\InternalErrorException
	 * @see https://developers.google.com/recaptcha/docs/display#config reCAPTCHA widget options
	 * @uses MeTools\View\Helper\HtmlHelper::div()
	 * @uses MeTools\View\Helper\HtmlHelper::js()
	 */
	public function display(array $options = [], array $optionsScript = []) {
		//Gets form keys
		$keys = Configure::read('Recaptcha.Form');
		
		//Checks for form keys
		if(empty($keys['public']) || empty($keys['private']))
            throw new \Cake\Network\Exception\InternalErrorException(__d('me_tools', 'Form keys are not configured'));
		
		$optionsScript = addDefault('block', 'script_bottom', $optionsScript);
		
		$this->Html->js('https://www.google.com/recaptcha/api.js', am($optionsScript, ['async' => TRUE, 'defer' => TRUE]));
		
		return $this->Html->div('g-recaptcha', ' ', am($options, ['data-sitekey' => $keys['public']]));
	}
	
    /**
     * Alias for `mailLink()` method
     * @see mailLink()
     */
    public function mail() {
        return call_user_func_array([get_class(), 'mailLink'], func_get_args());
    }

    /**
     * Creates an HTML link for an hidden email. The link will be open in a popup
     * @param string $title Link title
     * @param string $mail Email to hide
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses MeTools\View\Helper\HtmlHelper::link()
	 * @uses _obfuscate()
     * @uses mailUrl()
	 */
    public function mailLink($title, $mail = NULL, array $options = []) {
		$title = empty($mail) ? $this->_obfuscate($mail = $title) : $title;
        $link = self::mailUrl($mail);

		$options = addValue('onclick', sprintf("window.open('%s', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;", $link), $options);
		
        return $this->Html->link($title, $link, $options);
    }

	/**
     * Gets the url for an hidden email. 
     * 
     * This method will only return a url. If you want to create a link, you should use the `mailLink()` method
     * @param string $mail Email to hide
     * @return string Url
	 * @throws \Cake\Network\Exception\InternalErrorException
	 */
    public function mailUrl($mail) {
		//Gets mail keys
		$keys = Configure::read('Recaptcha.Mail');
		
		//Checks for mail keys
        if(empty($keys['public']) || empty($keys['private']))
            throw new \Cake\Network\Exception\InternalErrorException(__d('me_tools', 'Mail keys are not configured'));

        //Checks if the private mail key is valid (hexadecimal digits)
        if(!ctype_xdigit($keys['private']))
            throw new \Cake\Network\Exception\InternalErrorException(__d('me_tools', 'The private mail key is not valid'));
		
        return recaptcha_mailhide_url($keys['public'], $keys['private'], $mail);
    }
	
    /**
     * Alias for `display()` method
     * @see display()
     */
	public function recaptcha() {
        return call_user_func_array(array(get_class(), 'display'), func_get_args());
	}
}