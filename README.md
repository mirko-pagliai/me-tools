# MeTools
MeTools is a CakePHP plugin to improve applications development.  
It provides some useful tools, such as components, helpers and javascript libraries.

You can found MeTools APIs [here](http://repository.novatlantis.it/metools/API) and 
the sandbox repository [here](http://github.com/mirko-pagliai/MeToolsSandbox). 
You can try the sandbox [here](http://repository.novatlantis.it/metools-sandbox).

## Versioning
For transparency and insight into our release cycle, and for striving to maintain backward compatibility, 
MeTools will be maintained under the [Semantic Versioning guidelines](http://semver.org).

## Installation
Extract MeTools in `app/Plugin` and load it in `app/Config/bootstrap.php`:

	CakePlugin::load(array('MeTools' => array('routes' => TRUE)));

In your webroot directory, create (or copy) a link to the MeTools webroot:

	cd app/webroot
	ln -s ../Plugin/MeTools/webroot/ MeTools

## Configuration
Copy and rename `app/Plugin/Config/recaptcha.default.php` in `app/Config/recaptcha.php`, so configure Recaptha keys.

## Error views
You can use the error views provided by MeTools copying them from `app/Plugin/MeTools/View/Errors` to `app/View/Errors`.

Otherwise you can use the `MeExceptionRenderer` class to handle the errors, which will be displayed directly using 
the views provided by MeTools. To do this, in the `app/Config/core.php` file, you have to change the configuration 
of the exceptions as follows:

	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'MeTools.MeExceptionRenderer',
		'log' => true
	));

## Flash messages
You can use the flash messages views provided by MeTools copying them from `app/Plugin/MeTools/View/Elements` to `app/View/Elements`.

Otherwise you can use the `MeSessionComponent` class to generate flash messages, which will be displayed directly using 
the views provided by MeTools. To do this, you should use this component as an alias, as follows:
	
	public $component = array('Session' => array('className' => 'MeTools.MeSession'));

### Libraries and script
MeTools uses different libraries or scripts:

- jQuery 2.1.0 ([site](http://jquery.com));
- Bootstrap 3.1.1, ([site](http://getbootstrap.com)), without Glyphicons;
- Font Awesome 4.0.3 ([site](http://fortawesome.github.com/Font-Awesome));
- PHP Markdown 1.4.0 ([site](http://michelf.ca/projects/php-markdown));
- reCAPTCHA PHP library 1.11 ([site](https://developers.google.com/recaptcha/docs/php));
- Datepicker for Bootstrap 1.3.0 by Andrew Rowls ([site](http://eternicode.github.io/bootstrap-datepicker));
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

If you don't want to use the jQuery adapter for CKEditor, pass `FALSE` as the first argument:

	$this->Library->ckeditor(FALSE);

Then, within a view, you can create a CKEditor textarea using the MeFormHelper:

	echo $this->Form->ckeditor('text');

Note that the `ckeditor()` method of the MeFormHelper takes the same arguments of the `input()` method, 
including its options. For example:

	echo $this->Form->ckeditor('text', array(
		'class'	=> 'my_textarea',
		'label' => 'Body',
	));