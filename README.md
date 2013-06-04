# MeTools
MeTools is a CakePHP plugin to improve applications development.

## Installation
Extract MeTools in **Plugin/**.

Load MeTools in **bootstrap.php**:

	CakePlugin::load('MeTools');

In your webroot directory, create (or copy) a link to the MeTools webroot:

	cd webroot
	ln -s ../Plugin/MeTools/webroot/ MeTools

Then, create (or copy) a link to *thumber.php*:

	ln -s ../Plugin/MeTools/webroot/thumber.php .

## Configuration
Rename **Config/recaptcha.default.php** in **Config/recaptcha.php** and configure Recaptha keys.

### Libraries and script
**MeTools** uses different libraries or scripts:

- JQuery 1.10.1 and 2.0.2 ([site](http://jquery.com));
- Bootstrap 2.3.2 ([site](http://twitter.github.com/bootstrap));
- Thumber 0.5.6 ([site](https://code.google.com/p/phpthumbmaker));
- Font Awesome 3.1.1 ([site](http://fortawesome.github.com/Font-Awesome));
- PHP Markdown 1.3 ([site](http://michelf.ca/projects/php-markdown));
- reCAPTCHA PHP library 1.11 ([site](https://developers.google.com/recaptcha/docs/php));
- Datepicker for Bootstrap by Andrew Rowls ([site](http://eternicode.github.io/bootstrap-datepicker)).