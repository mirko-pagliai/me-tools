# MeTools
MeTools is a CakePHP plugin to improve applications development.  
It provides some useful tools, such as components, helpers and javascript libraries.

## Versioning
For transparency and insight into our release cycle, and for striving to maintain backward compatibility, 
MeTools will be maintained under the [Semantic Versioning guidelines](http://semver.org).

## Installation
Extract MeTools in `app/Plugin` and load it in `app/Config/bootstrap.php`:

	CakePlugin::load('MeTools');

In your webroot directory, create (or copy) a link to the MeTools webroot:

	cd app/webroot
	ln -s ../Plugin/MeTools/webroot/ MeTools

Then, create (or copy) a link to *thumber.php*:

	ln -s ../Plugin/MeTools/webroot/thumber.php .

## Configuration
Rename `app/Plugin/Config/recaptcha.default.php` in `app/Plugin/Config/recaptcha.php`, so configure Recaptha keys.

### Libraries and script
MeTools uses different libraries or scripts:

- JQuery 1.10.2 and 2.0.3 ([site](http://jquery.com));
- Bootstrap 3.0.0 ([site](http://getbootstrap.com));
- Thumber 0.5.6 ([site](https://code.google.com/p/phpthumbmaker));
- Font Awesome 3.2.1 ([site](http://fortawesome.github.com/Font-Awesome));
- PHP Markdown 1.3 ([site](http://michelf.ca/projects/php-markdown));
- reCAPTCHA PHP library 1.11 ([site](https://developers.google.com/recaptcha/docs/php));
- Datepicker for Bootstrap 1.2.0 by Andrew Rowls ([site](http://eternicode.github.io/bootstrap-datepicker));
- Bootstrap Timepicker ([site](http://jdewit.github.io/bootstrap-timepicker)).

## CKEditor
MeTools doesn't contain a copy of CKEditor, because it would be too heavy, because it's highly configurable (you 
can customize the package and choose which plugins to download) and because it's not necessary for all projects.

So you need to download CKEditor from its [site](http://ckeditor.com/download), preferably by 
[configuring plugins](http://ckeditor.com/builder).  
If you like, you can upload `build-config.js` 
that is located in `app/Plugin/MeTools/webroot/ckeditor`. This contain a valid configuration in most cases.

Once you have downloaded CKEditor, you must extract it in `app/webroot/ckeditor` or `app/webroot/js/ckeditor`.  
Finally, you can edit `ckeditor_init.js` located in `app/Plugin/MeTools/webroot/ckeditor`, that MeTools uses to 
instantiate CKEditor. For ease, you can copy it in `app/webroot/js`, `app/webroot/ckeditor` or `app/webroot/js/ckeditor`.  
If MeTools doesn't find `ckeditor_init.js` in the webroot of your app, it will use its own file in the plugin webroot.

### How to user CKEditor with MeTools
You should use the LibraryHelper to load CKEditor scripts, within a view or layout of your app:

	$this->Library->ckeditor();

If you don't want to use the jQuery adapter for CKEditor, pass `false` as the first argument:

	$this->Library->ckeditor(false);

Then, within a view, you can create a CKEditor textarea using the MeForm helper:

	echo $this->MeForm->ckeditor('text');

Note that the `ckeditor()` method of the MeForm helper takes the same arguments of the `input()` method, 
including its options. For example:

	echo $this->MeForm->ckeditor('text', array(
		'class'	=> 'my_textarea',
		'label' => 'Body',
	));
