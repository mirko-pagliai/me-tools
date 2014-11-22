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

Otherwise you can use the `MeSession` component to generate flash messages, which will be displayed directly using 
the views provided by MeTools. To do this, you should use this component as an alias, as follows:
	
	public $components = array('Session' => array('className' => 'MeTools.MeSession'));

Now you can generate flash messages like this:
	
	$this->Session->flash('This is an error message', 'error');

## Paginator
You can use the `MePaginator` helper to generate pagination controls such as page numbers and next/previous links. To do this,
you should use this component as an alias, as follows:

	public $helpers = array('Paginator' => array('className' => 'MeTools.MePaginator'));

Then, inside of your views, you can use the appropriate element:

	echo $this->element('MeTools.paginator');

## SQL Dump
To show the SQL dump, you can use the appropriate element:

	echo $this->element('MeTools.sql_dump');

This will display the SQL dump only when available and only if the user is not using a mobile device.

## Compress Shell
The `CompressShell` allows you to combine and compress css and js files.

To use the `CompressShell`, you have to first install on your system `Clean-css` and `UglifyJS`. As root user:

	npm install clean-css -g
	npm install uglify-js -g

The shell has three methods, the two main methods are `css()` and `js`.  
With `css()` or `js()`, simply pass as arguments the input file (or files) and the output file as the last argument.

For example:
	
	cake MeTools.Compress css webroot/css/default.css webroot/css/default.min.css

This will compress `default.css` and will create `default.min.css` as result.

For example:
	
	cake MeTools.Compress css webroot/css/first.css webroot/css/second.css webroot/css/result.min.css

This will combine and compress `first.css` and `second.css` and will create `result.min.css` as result.

If you use the `--force` (or `-f`) option, the output file will be overwritten without prompting.

### Using a configuration file
Rather than indicating files as arguments, you can use a configuration file. Each file should be on a single line and the last 
line will be used as the output file. 

For example, create the `Config/assets_css/default.ini` file:

	webroot/css/first.css
	webroot/css/second.css
	webroot/css/result.min.css

Then, use the `--config` (or `-c`) option:

	cake MeTools.Compress css -c Config/assets_css/default.ini

This will combine and compress `first.css` and `second.css` and will create `result.min.css` as result.

Remember: if you use the configuration file, you must not specify the file extension.

### Using the automatic method
The shell has a third method, `auto()`. This will automatically search all the `ini` configuration files, if they are located
into `Config/assets_css` or `Config/assets_js`.

It will search into: 

	- the main application; 
	- themes of the main application;
	- plugins; 
	- themes of the plugins.

For example, let's assume that your application has a theme called `MyTheme` and a plugin called `MyPlugin`. It will search 
all `ini` files that are located into:

	APP/Config/assets_css
	APP/Config/assets_js
	APP/Plugin/MyPlugin/Config/assets_css
	APP/Plugin/MyPlugin/Config/assets_js
	APP/View/Themed/MyTheme/Config/assets_css
	APP/View/Themed/MyTheme/Config/assets_js

## Libraries and script
MeTools uses different libraries or scripts:

- jQuery 2.1.1 ([site](http://jquery.com));
- Bootstrap 3.3.1 ([site](http://getbootstrap.com)), without Glyphicons;
- Font Awesome 4.2.0 ([site](http://fortawesome.github.com/Font-Awesome));
- PHP Markdown 1.4.1 ([site](http://michelf.ca/projects/php-markdown));
- reCAPTCHA PHP library 1.11 ([site](https://developers.google.com/recaptcha));
- Bootstrap v3 datetimepicker widget 3.1.3 ([site](https://github.com/Eonasdan/bootstrap-datetimepicker));
- Moment.js 2.8.4 ([site](http://momentjs.com/)), with locales.

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
