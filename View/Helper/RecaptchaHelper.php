<?php

/**
 * RecaptchaHelper
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
 * @package		MeTools\View\Helper
 * @see			http://developers.google.com/recaptcha/docs/php reCAPTCHA PHP library
 * @see			http://www.google.com/recaptcha/mailhide/apikey reCAPTCHA mail keys
 */
App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'MeTools.Recaptcha/recaptchalib');

/**
 * Provides several methods for reCAPTCHA.
 *
 * Before using this helper, you have to configure keys in `app/Config/recaptcha.php`.
 * You can use as example the file `app/Plugin/MeTools/Config/recaptcha.default.php`.
 */
class RecaptchaHelper extends AppHelper {
    /**
     * Mail keys
     * @var array
     */
    private $mail_keys = FALSE;

    /**
     * Helpers
     * @var array
     */
    public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));

	/**
	 * Obfuscates an email address
	 * @param string $mail Mail address
	 * @return string Mail address obfuscated
	 * @see http://stackoverflow.com/a/20545505/1480263
	 */
	private function obfuscate($mail) {
		$mail = explode("@", $mail);
		$name = implode(array_slice($mail, 0, count($mail) - 1), '@');
		$lenght  = floor(strlen($name) / 2);

		return substr($name, 0, $lenght).str_repeat('*', $lenght)."@".end($mail);
	}
	
    /**
     * Construct
     * @param View $View The View this helper is being attached to
     * @param array $settings Configuration settings for the helper
     * @uses mail_keys
     */
    public function __construct(View $View, $settings = array()) {
        Configure::load('recaptcha');

        $keys = $this->mail_keys = array(
           'pub'    => Configure::read('Recaptcha.Mail.Public_key'),
           'priv'   => Configure::read('Recaptcha.Mail.Private_key')
        );

        if(empty($keys['pub']) || empty($keys['priv']))
            throw new InternalErrorException(__d('me_tools', 'Mail keys are not configured'));

        //Checks if the private mail key is valid (hexadecimal digits)
        if(!ctype_xdigit($keys['priv']))
            throw new InternalErrorException(__d('me_tools', 'The private mail key is not valid'));

        parent::__construct($View, $settings);
    }

    /**
     * Alias for `mailLink()` method
     * @see mailLink()
     */
    public function mail() {
        return call_user_func_array(array(get_class(), 'mailLink'), func_get_args());
    }

    /**
     * Creates an HTML link for an hidden email. The link will be open in a popup.
     * @param string $title Link title
     * @param string $mail Email to hide
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @uses mailUrl()
	 * @uses obfuscate()
     * @uses MeHtmlHelper::link()
     */
    public function mailLink($title, $mail = NULL, $options = array()) {
		if(empty($mail))
			$title = $this->obfuscate($mail = $title);
		
        $link = self::mailUrl($mail);

        //Adds the "onclick" options, that allows to open the link in a popup
        $options['onclick'] = sprintf("window.open('%s', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;", $link);

        return $this->Html->link($title, $link, $options);
    }

    /**
     * Gets the url for an hidden email. 
     * 
     * This method will only return a url. If you want to create a link, you should use the `mailLink()` method
     * @param string $mail Email to hide
     * @return string Url
     * @see mailLink()
     * @uses mail_keys
     */
    public function mailUrl($mail) {
        return !empty($this->mail_keys) ? recaptcha_mailhide_url($this->mail_keys['pub'], $this->mail_keys['priv'], $mail) : NULL;
    }
}