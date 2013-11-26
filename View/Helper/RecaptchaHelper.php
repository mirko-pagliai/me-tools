<?php
App::uses('AppHelper', 'View/Helper');
App::import('Vendor', 'MeTools.Recaptcha/recaptchalib');

/**
 * Recaptcha functions.
 *
 * Before using this helper, please configure Recaptcha keys in <i>Config/recaptcha.php</i>
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
	 * @param View $View The View this helper is being attached to.
	 * @param array $settings Configuration settings for the helper.
	 */
	 public function __construct(View $View, $settings = array()) {
		//Loads Config/recaptcha.php from MeTools
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
	 *
	 * It uses <i>MeHtml::link()</i> to create the link and <i>mailUrl()</i> to get the url of the email hidden.
	 * 
	 * You can use {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons}. For example:
	 * <code>
	 * echo $this->Recaptcha->mailLink('my mail', 'mymail@provider.com', array('icon' => 'icon-search'));
	 * </code>
	 * @param string $title Link title
	 * @param string $mail Email to hide
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function mailLink($title, $mail, $options=array()) {
		//Get the link
		$link = $this->mailUrl($mail);

		//Add the "onclick" options, that allows to open the link in a popup
		$options['onclick'] = "window.open('".$link."', '', 'toolbar=0,scrollbars=0,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=300'); return false;";

		//Return the link
		return $this->Html->link($title, $link, $options);
	}

	/**
	 * Gets the url for an hidden email.
	 *
	 * Note that this method will only return a url. To create a link is better to use <i>mailLink()</i>.
	 * @param string $mail Email to hide
	 * @return string Url
	 */
	public function mailUrl($mail) {
		return !empty($this->mail_keys) ? recaptcha_mailhide_url($this->mail_keys['pub'], $this->mail_keys['priv'], $mail) : null;
	}
}