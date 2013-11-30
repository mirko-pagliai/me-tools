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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\View\Helper
 * @see			http://developers.google.com/recaptcha/docs/php reCAPTCHA PHP library
 */
App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'MeTools.Recaptcha/recaptchalib');

/**
 * Provides several methods for Recaptcha.
 *
 * Before using this helper, you have to configure keys in `Config/recaptcha.php`.
 */
class RecaptchaHelper extends AppHelper {
	/**
	 * Mail keys
	 * @var array
	 */
	private $mail_keys = false;

	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));

	/**
	 * Construct
	 * @param View $View The View this helper is being attached to
	 * @param array $settings Configuration settings for the helper
	 * @uses mail_keys to set mail keys
	 */
	 public function __construct(View $View, $settings = array()) {
		Configure::load('MeTools.recaptcha');

		//Sets mail keys, if they exist
		if(Configure::read('Recaptcha.Mail.Public_key') && Configure::read('Recaptcha.Mail.Private_key'))
			$this->mail_keys = array(
				'pub'	=> Configure::read('Recaptcha.Mail.Public_key'),
				'priv'	=> Configure::read('Recaptcha.Mail.Private_key')
			);

		parent::__construct($View, $settings);
	}

	/**
	 * Creates an HTML link for an hidden email. The link will be open in a popup.
	 * @param string $title Link title
	 * @param string $mail Email to hide
	 * @param array $options HTML attributes
	 * @return string Html
	 * @uses mailUrl to get the url
	 */
	public function mailLink($title, $mail, $options=array()) {
		$link = $this->mailUrl($mail);

		//Adds the "onclick" options, that allows to open the link in a popup
		$options['onclick'] = sprintf("window.open('%s', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;", $link);
		
		return $this->Html->link($title, $link, $options);
	}

	/**
	 * Gets the url for an hidden email. This method will only return a url.
	 * @param string $mail Email to hide
	 * @return string Url
	 */
	public function mailUrl($mail) {
		return !empty($this->mail_keys) ? recaptcha_mailhide_url($this->mail_keys['pub'], $this->mail_keys['priv'], $mail) : null;
	}
}