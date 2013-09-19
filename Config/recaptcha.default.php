<?php

/**
 * Before using Recaptcha, you have to get keys:
 * http://www.google.com/recaptcha
 * When you have keys, set them below and RENAME THIS FILE in "recaptcha.php".
 * Remember: keys for the site and keys form emails ARE DIFFERENT.
 */
$config = array(
	'Recaptcha' => array(
		/**
		 * Mail keys.
		 * You can get these keys here: http://www.google.com/recaptcha/mailhide/apikey
		 */
		'Mail' => array(
			//Mail public key
			'Public_key' => 'your-public-key-here',
			//Mail private key
			'Private_key' => 'your-private-key-here'
		)
	)
);