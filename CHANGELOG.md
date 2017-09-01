# 2.x branch
## 2.15 branch
### 2.15.0
* `InstallShell::fixComposerJson()` now has the `path` option;
* added `ConsoleIntegrationTestCase` class. Console tests have been simplified;
* updated for CakePHP 3.5;
* removed `IntegrationTestCase::loadAllFixtures()` method. Use `loadFixtures()`
    with no arguments;
* removed `am()` global function.

## 2.14 branch
### 2.14.0
* added `IntegrationTestCase`, `TestCase` and `TestCaseTrait` classes. Removed
    `LoadAllFixturesTrait` and `LogsMethodsTrait` classes;
* significantly improved all tests.

## 2.13 branch
### 2.13.1
* added `LoadAllFixturesTrait` and `LogsMethodsTrait` trait for TestSuite;
* removed `MailHelper::obfuscate()` method;
* the MIT license has been applied.

### 2.13.0
* fixed bug for `isUrl()` detector; 
* removed all reCAPTCHA classes and libraries. Use instead
    `crabstudio/Recaptcha` and `mirko-pagliai/cakephp-recaptcha-mailhide`
    plugins;
* removed `af()` global function.

## 2.12 branch
### 2.12.5
* updated for Assets 1.1.4;
* updated for CkEditor 4.7.

### 2.12.4
* fixed little bug on `UploaderComponent::set()` method;
* fixed bug on `UploaderComponent::mimetype()` method. A wrong error was set;
* removed `firstKey()` global function. Use instead
    `\Cake\Collection\CollectionInterface::first()` with `array_keys()`.

### 2.12.3
* by default, the `is('url')` detector removes the query string from the current
    url. You can pass `false` as second parameter to keep the query string.

### 2.12.2
* added `HtmlHelper::iconClass()` method;
* `Youtube::getId()` method takes short url with the duration (eg.
    `http://youtu.be/bL_CJKq9rIw?t=5s`);
* tests that require a network connection have been marked with the
    `requireNetwork` group.

### 2.12.1
* methods that have been deprecated with CakePHP 3.4 have been replaced.

### 2.12.0
* removed global `firstValue()`. Use instead `CollectionInterface::first()`;
* removed global `isLocalhost()`. Use instead the `is('localhost')` detector;
* removed global `getClientIp()`. Use instead `ServerRequest::clientIp()`;
* updated for CakePHP 3.4.

## 2.11 branch
### 2.11.4
* fixed a little bug for labels on inline forms.

### 2.11.3
* added `createPluginsLinks` subcommand to the `InstallShell`.

### 2.11.2
* fixed CKEditor init script to work with jQuery 3.x.

### 2.11.1
* `UploaderComponent` has been rewritten and improved;
* fixed `composer.json` with suggested packages;
* subcommand `installPackages` provided by `InstallShell` is no longer
    available. Instead, use suggested packages by Composer;
* some improvements for `Shell` and `InstallShell` classes;
* fixed little bugs for `InstallShell`;
* added tests for `UploaderComponent` and `RecaptchaComponent` classes.

### 2.11.0
* removed the `BreadcrumbHelper`. Instead, use the `BreadcrumbsHelper` that
    extends the new helper provided by CakePHP;
* html global functions are now `OptionsParserTrait`. This class also provides
    `addIconToText()` and `addTooltip()` methods;
* removed tokens (component, entity and table). Instead, use `cakephp-tokens`.

## 2.10 branch
### 2.10.5
* fixed bug for `FormHelper`. Templates are correctly reset when you call the
    `input()` method;
* updated configuration file for CKEditor 4.6.

### 2.10.4
* added `BBCodeHelper::image()` and `BBCodeHelper::url()`;
* fixed bug for loading the `Assets` plugin;
* fixed bug for `PaginatorHelper::next()`;
* little fixes for `Shell` class;
* added tests for `PaginatorHelper` class;
* added tests for `Shell` class;
* added some tests for `LibraryHelper` class.

### 2.10.3
* little fixes for Fancybox's titles.
* removed `MarkdownHelper`. Instead, you can use `gourmet/common-mark`.

### 2.10.2
* added `MailHelper` class with `obfuscate()` method;
* removed `RecaptchaHelper::mailLink()` method;
* updated for Assets 1.1.0;
* added tests for `RecaptchaHelper` class;
* added tests for `FlashComponent` class.

### 2.10.1
* fixed serious bug for `BBCodeHelper::remove()` method: now it doesn't remove
    all the HTML code, but only the BBCode code;
* added `getChildMethods()` global function;
* added support for tooltips alignment;
* fixed bug for tooltips with quotes or code;
* removed "bold" style for shells.

### 2.10.0
* `HtmlHelper` class has been improved. The `tip` option has been replaced
    with `help`. Checkboxes are displayed according to the browser;
* `DropdownHelper` class has been completely rewritten and now provides
    `menu()`, `start()` and `end()` methods;
* added tests for `DropdownHelper` class;
* added tests for `HtmlHelper` class.

## 2.9 branch
### 2.9.1
* fixed bug for icons of Bootstrap Datepicker;
* downgrade jQuery to 2.2.4.
 
### 2.9.0
* added `HtmlHelper::cssBlock()` method;
* `HtmlHelper::cssStart()` and `HtmlHelper::cssEnd()` methods have been
    completely rewritten and they no longer need you add the `<style>` tag;
* added `implodeRecursive()` global function;
* `FlashComponent` renders all `alert()`, `error()`, `notice()` and `success()`
    class using the `src/Template/Element/Flash/flash.ctp` template;
* global functions splitted into `config/functions/global.php` and
    `config/functions/html.php`;
* added `HtmlHelper::addTooltip()` method;
* added support for tooltips for some methods;
* added `BreadcrumbHelper::reset()` method;
* `Youtube::getPreview()` method can also accept url as argument;
* fixed several bug for `clearDir()`;
* fixed bug for `optionDefaults()` and `optionsValues()`;
* fixed many bugs for `BBCodeHelper`;
* fixed many small bugs for `HtmlHelper`;
* fixed bug for `BreadcrumbHelper::get()` method. Added `onlyStartText` option;
* fixed small bugs for global functions;
* added tests for global functions;
* added tests for request detectors;
* added tests for `BBCodeHelper` class;
* added tests for `BreadcrumbHelper` class;
* added tests for `HtmlHelper` class;
* added tests for `Plugin` class;
* added tests for `Youtube` class.

## 2.8 branch
### 2.8.0
* `MeTools\Network\Request` has been removed. All old methods are now 
	request detectors (see `config/detectors.php`);
* method `MeTools\View\Helper::_addButtonClass()` is now global function 
    `buttonClass()`;
* `fv()` is now `firstValue()` and `fk()` is now `firstKey()`;
* fixed code for CakePHP Code Sniffer;
* updated for CakePHP 3.3.

## 2.7 branch
### 2.7.2
* fixed bug for CkEditor.

### 2.7.1
* updated jQuery to 3.1 branch.

### 2.7.0
* `addDefault()` renamed as `optionDefaults()` and `addOptionValue()` renamed 
	as `optionValues()`. These functions now accept values as array.

## 2.6 branch
### 2.6.7
* fixed bug for mail links of reCAPTCHA.

### 2.6.6
* added `Breadcrumb` helper.

### 2.6.5
* fixed Composer's packages.

### 2.6.4
* added `Youtube` utility class;
* fixed some code for Composer updates;
* fixed bug for `Plugin` class.

### 2.6.3
* added `Uploader` component.

### 2.6.2
* fixed serious bug for fancybox;
* datepicker uses the `AssetHelper`;
* removed the `Cache` class.

### 2.6.1
* fixed bug for form inputs;
* removed some useless methods.

### 2.6.0
* improved the `Plugin` class;
* removed bake templates;
* removed the `Xml` class;
* removed the `SecurityComponent` class.

## 2.5 branch
### 2.5.2
* fixed shell output style;
* removed useless methods.

### 2.5.1
* fixed little bugs.

### 2.5.0
* log reports request URL, referer URL and client ip, both for exceptions and 
	errors;
* removed useless classes.

## 2.4 branch
### 2.4.0
* removed the code for assets. In its place, use the `Assets` plugin;
* removed the `Unix` utility. Added the `which` global function.

## 2.3 branch
### 2.3.0
* removed the code for thumbnails. In its place, use the `Thumbs` plugin;
* fixed bug on log parser.

## 2.2 branch
### 2.2.9
* fixed bug on some console methods for CakePHP 3.2.

### 2.2.8
* fixed bug for thumbnails for urls with query string;
* updated to CakePHP 3.2.

### 2.2.7
* rewritten the FileLog class. Added a log parser;
* `iframe()` and `youtube` methods from `HtmlHelper` now support `$ratio` to 
	create responsive embed.

### 2.2.6
* fixed serious bug for CKEditor.

### 2.2.5
* support for some BBCode;
* support for Youtube videos;
* fixed bug for Shareaholic.

### 2.2.4
* added the ErrorHandler class. This allows to track the "request URL" also 
	for errors and not only for exceptions;
* fixed bug with thumbs on remote files.

### 2.2.3
* improved the code for shells.

### 2.2.2
* the Install shield now copies configuration files;
* fixed a lot of strings and translations.

### 2.2.1
* fixed a serious bug.

### 2.2.0
* added the Asset helper. Removed the Layout helper and the Compress shell. 
	Now assets are automatically generated when required;
* fixed little bugs.

## 2.1 branch
### 2.1.2
* fixed a bug with forms on Firefox.

### 2.1.1-RC3
* added the installer console.

### 2.1.0-RC2
* added support for Shareaholic;
* jQuery, Bootstrap, Bootstrap Date/Time Picker, Moment.js and Font Awesome 
	are installed via Composer;
* improved the logs management;
* fixed little bugs.

## 2.0 branch
### 2.0.1-RC1
* fixed many little bugs;
* updated Bootstrap 3 Date/Time Picker to 4.17.37.

### 2.0.0-beta
* all the code has been completely rewritten for CakePHP 3.x. Several 
	optimizations have been applied;
* for the actions of loading/adding files, error messages are more 
	intelligible;
* added the `MeEmail` class to simplify sending emails;
* added the `MeRequest` class to handle HTTP requests;
* added the `ThumbHelper` to generate thumbnails;
* updated Moment.js to 2.10.6, Bootstrap 3 Date/Time Picker to 4.15.35, Font 
	Awesome to 4.4.0, jQuery to 2.1.4, Bootstrap to 3.3.5 and Moment.js to 
	2.10.3.

# 1.x branch
## 1.2 branch
### 1.2.2
* full support for reCAPTCHA. Rewritten the `RecaptchaComponent`;
* fixed bug for the Datepicker;
* updated Bootstrap to 3.3.4 version.
* updated Bootstrap 3 Date/Time Picker to 4.7.14;
* updated PHP Markdown to 1.5.0.

### 1.2.1
* full support for reCAPTCHA;
* the `Token` component and model have been entirely rewritten;
* fixed bug for the `analytics()` method.

### 1.2.0
* updated Font Awesome to 4.3.0 version;
* support for FancyBox;
* fixed a bug in errors views;
* added some methods to `Plugin` utility. The `System` utility has been 
	divided into several utilities;
* added the changelog file.
