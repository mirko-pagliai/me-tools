<?php

/**
 * Before using Recaptcha, get keys from Recaptcha site:
 *
 * http://www.google.com/recaptcha
 *
 * When you have keys, set them in this file and RENAME THIS FILE in "recaptcha.php"
 *
 * Remember that Recaptcha keys for the site and keys to hide emails ARE DIFFERENT.
 */
$config = array(
	/**
	 * Recaptcha configuration
	 */
	'Recaptcha' => array(
		/**
		 * Mail keys.
		 *
		 * You can get these keys here: http://www.google.com/recaptcha/mailhide/apikey
		 */
		'Mail' => array(
			/**
			 * Mail public key
			 */
			'Public_key'	=> 'your-public-key-here',
			/**
			 * Mail private key
			 */
			'Private_key'	=> 'your-private-key-here'
		)
	)
);