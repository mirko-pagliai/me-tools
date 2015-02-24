# MeTools
MeTools is a CakePHP plugin to improve applications development.  
It provides some useful tools, such as components, helpers and javascript libraries.

You can found the MeTools API [here](http://repository.novatlantis.it/metools/API) and 
the sandbox repository [here](http://github.com/mirko-pagliai/MeToolsSandbox). 
You can try the sandbox [here](http://repository.novatlantis.it/metools-sandbox).

## Versioning
For transparency and insight into our release cycle and to maintain backward compatibility, 
MeTools will be maintained under the [Semantic Versioning guidelines](http://semver.org).

## Installation
Extract MeTools in `APP/Plugin` and load it in `APP/Config/bootstrap.php`:

	CakePlugin::load(array('MeTools' => array('routes' => TRUE)));

In the webroot directory, create (or copy) a link to the MeTools webroot:

	cd APP/webroot
	ln -s ../Plugin/MeTools/webroot/ MeTools

Some `js` and `css` files are added to the end of the layout. Edit your layout by adding before 
the `</body>` tag:

		<?php
			echo $this->fetch('css_bottom');
			echo $this->fetch('script_bottom');
		?>

## Error views
You can use the error views provided by MeTools copying them from `APP/Plugin/MeTools/View/Errors`
to `APP/View/Errors`.

Otherwise you can use the `MeExceptionRenderer` class to handle the errors,
which will be displayed directly using the views provided by MeTools.  
To do this, in the `APP/Config/core.php` file, you have to change the configuration of the exceptions as follows:

	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'MeTools.MeExceptionRenderer',
		'log' => true
	));

## Flash messages
You can use the flash messages views provided by MeTools copying them from `APP/Plugin/MeTools/View/Elements`
to `APP/View/Elements`.

Otherwise you can use the `MeSession` component to generate flash messages, 
which will be displayed directly using the views provided by MeTools.  
To do this, you should use this component as an alias, as follows:
	
	public $components = array('Session' => array('className' => 'MeTools.MeSession'));

Now you can generate flash messages like this:
	
	$this->Session->flash('This is an error message', 'error');

## Paginator
You can use the `MePaginator` helper to generate pagination controls,
such as page numbers and next/previous links.  
To do this, you should use this helper as an alias, as follows:

	public $helpers = array('Paginator' => array('className' => 'MeTools.MePaginator'));

Then, inside of your views, you can render the appropriate element:

	echo $this->element('MeTools.paginator');

## SQL Dump
To show the SQL dump, you can use the appropriate element:

	echo $this->element('MeTools.sql_dump');

This will display the SQL dump only when available and only if the user is not using a mobile device.

## Compress Shell
The `CompressShell` allows you to combine and compress css and js files.  
See the [page](//github.com/mirko-pagliai/MeTools/wiki/Compress-Shell) on our wiki.

## reCAPTCHA
To use reCAPTCHA, see the [page](//github.com/mirko-pagliai/MeTools/wiki/reCAPTCHA) on our wiki.

## Libraries and script
MeTools includes different libraries and scripts:

- jQuery 2.1.3 ([site](http://jquery.com));
- Bootstrap 3.3.2 ([site](http://getbootstrap.com)), without Glyphicons;
- Font Awesome 4.3.0 ([site](http://fortawesome.github.com/Font-Awesome));
- PHP Markdown 1.4.1 ([site](http://michelf.ca/projects/php-markdown));
- reCAPTCHA PHP library 1.11 ([site](https://developers.google.com/recaptcha));
- Bootstrap 3 Date/Time Picker 4.4.0 ([site](https://github.com/Eonasdan/bootstrap-datetimepicker));
- Moment.js 2.9.0 ([site](http://momentjs.com/)), with locales.

## CKEditor
MeTools doesn't contain a copy of CKEditor.

So you need to download CKEditor from its [site](http://ckeditor.com/download), preferably by 
[configuring plugins](http://ckeditor.com/builder).  
If you like, you can upload the `build-config.js` file, that is located in `APP/Plugin/MeTools/webroot/ckeditor`.
This contain a valid configuration in most cases.

Once you have downloaded CKEditor, you must extract it in `APP/webroot/ckeditor` or `APP/webroot/js/ckeditor`.  
Finally, you can edit `ckeditor_init.js` located in `APP/Plugin/MeTools/webroot/ckeditor`, that MeTools uses to 
instantiate CKEditor. For ease, you can copy it in `APP/webroot/js`.  
If MeTools doesn't find the `ckeditor_init.js` file in your app webroot,
it will use its own file in the plugin webroot.

### How to user CKEditor with MeTools
You should use the `LibraryHelper` to load CKEditor scripts. Within a view or the app layout:

	$this->Library->ckeditor();

If you don't want to use the jQuery adapter, pass `FALSE` as the first argument:

	$this->Library->ckeditor(FALSE);

Then, within a view, you can create a CKEditor textarea using the `MeFormHelper`:

	echo $this->Form->ckeditor('text');

Note that the `ckeditor()` method provided by `MeFormHelper` takes the same arguments of the `input()` method, 
including its options. For example:

	echo $this->Form->ckeditor('text', array(
		'class'	=> 'my_textarea',
		'label' => 'Body',
	));

## FancyBox.
MeTools doesn't contain a copy of FancyBox.

So you need to download FancyBox from its [site](http://fancyapps.com/fancybox).

Once you have downloaded FancyBox, you must extract it in `APP/webroot/fancybox`.  
Finally, you can edit `fancybox_init.js` located in `APP/Plugin/MeTools/webroot/fancybox`, that MeTools uses to 
instantiate FancyBox. For ease, you can copy it in `APP/webroot/js`.  
If MeTools doesn't find the `fancybox_init.js` file in your app webroot,
it will use its own file in the plugin webroot.