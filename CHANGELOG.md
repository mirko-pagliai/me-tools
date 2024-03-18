# 3.x branch
## 3.0 branch
### 3.0.4-RC5
* `HtmlHelper`: removed `badge()`, `meta()`, `ol()`, `ul()` and `viewport()` methods.

### 3.0.3-RC4
* `Html::__call()` methods (missing method handler) now supports named arguments;
* the `IconHelper::buildIconClasses()` no longer exists. We expect you to correctly specify all classes that make an icon.
* fixed a bug for `FormHelper::createInline()` method.

### 3.0.2-RC3
* deleted the `FlashComponent`;
* `TestCase::setUp()` method no longer exists. You have to call `enableCsrfToken()/enableRetainFlashMessages()` manually
  for controller tests.

### 3.0.1-RC2
* `getAlias()` and `getOriginClassName()` method have moved from `MockTrait` to the `TestCase` class. These methods no
  longer take a parameter, but return a valid result for the test class that is executing them;
* deleted `TestCase::__get()` magic method (and, consequently, also access to the `$alias` and `$originClassName`
  properties provided by this method). Use instead `getAlias()` and `getOriginClassName()` methods;
* deleted `ComponentTestCase` and `HelperTestCase` classes;
* deleted `BreadcrumbsHelper` class;
* deleted `getMockForHelper()`, `getTableClassNameFromAlias()` and `getPluginName()` methods provided by the `MockTrait`;
* deleted the `MockTrait` (because methods have been moved or deleted);
* deleted `is('ip')`, `is('matchingIp')`, `is('localhost')`, `is('prefix')` and `is('url')` request detector methods.

### 3.0.0-RC1
* requires at least CakePHP 5.0 and PHPUnit 10;
* added the `PaginatorHelper::hasPaginated()` method and fixed the `paginator.php` element;
* added all possible typehints;
* deleted `AppTable`class (so the `AppTable::findActive()` method no longer exist);
* deleted `BBCode` class;
* deleted `CreateDirectoriesCommand`, `CreateRobotsCommand`, `CreateVendorsLinksCommand`, `RunAllCommand` and
  `SetPermissionsCommand` classes. So me-tools no longer provides any command classes;
* deleted `Command` class;
* deleted `CommandTestCase` class;
* deleted the `Configure` and `Plugin` classes (simply use the ones provided by CakePHP);
* deleted the `LibraryHelper`;
* deleted the `Youtube` class;
* method `HtmlHelper::shareaholic()` no longer exist;
* `MeTools.WritableDirs` and `MeTools.VendorLinks` configuration keys and `VENDOR` and `WWW_VENDOR` constants are no
  longer defined by me-tools;
* deleted `bootstrap-datetimepicker.min.js`, `ckeditor_init.php` and `slugify.js` files from `webroot/js/`;
* deleted `ckeditor/build-config.js` and `i18n_constants.php` files from `config/`.

# 2.x branch
## 2.26 branch
### 2.26.0
* requires at least CakePHP 4.5 and PHP 8.1;
* the abstract `AppController` had been deprecated and was removed;
* `HtmlHelper::img()` had been deprecated and was removed;
* the `OptionsParser` had been deprecated and was removed (the same goes for the global `optionsParser()` function);
* `TestCase::getTable()` had been deprecated and was removed;
* `config/i18n_constants.php` file is deprecated and will be removed in a future release. Also, as of now it is no longer
  included automatically (by bootstrap). So, if still necessary, you will have to include it manually from your project;
* added tests for PHP 8.3.

## 2.25 branch
### 2.25.8
* updated `AppValidator::title()` rule, a title can now contain parenthesis and commas;
* updated for CakePHP 4.5;
* no longer uses the `Exceptionist` class (or exceptions provided by `php-tools` and deprecated);
* no longer uses `Tools\TestSuite\TestTrait::expectAssertionFailed()`;
* fixed `AppTable::findActive()` method, added `$options` parameter;
* fixed little bug for `PaginatorHelper` and icon on `asc` sorting;
* updated `phpstan` and `psalm` packages;
* updated for `php-tools` 1.8;
* added tests for PHP 8.2.

### 2.25.7
* sorting links (and their icons) are now fully managed by `PaginatorHelper` using the `$_defaultConfig` property.
  Removed css rules;
* "next" and "prev" links (and their icons) are now fully managed by `PaginatorHelper` using the `$_defaultConfig` property;
* fixed `FormHelper::createInline()`.

### 2.25.6
* css rules for ascending/descending sort links no longer depend on tables;
* fixed margins for `paginator.php` element;
* little fixes.

### 2.25.5
* added `is('ip')` and `is('matchingIp')` request detectors;
* the abstract `AppController` provided by me-tools has been deprecated and will be removed in a later release. Your
  `AppController` should directly extend `Cake\Controller\Controller`;
* `AppController` no longer automatically loads any component (previously they were `RequestHandler` and `MeTools.Flash`); 
* removed the auto width css rules for `date`, `datetime-local` and `time` input, use the `w-auto` class instead.

### 2.25.4
* added `AppValidator::allowEmptyStringOnEmptyField()` rule method;
* added `AppValidator::firstLetterCapitalized()` rule method;
* fixed a serious bug with `FormHelper` which prevented the `radio` and "input group" (`append-text` and `prepend-text`
  options)template from being customized at run-time;
* removed the base `View` class (which was introduced with version 2.25.0. Backtracking, sorry);
* `AppValidator::validPassword()` method has been simplified;
* the `FormHelper` now makes extensive use of the `@inheritDoc` tag;
* removed old (ancient) bake templates.

### 2.25.3
* added `HtmlHelper::para()` method, so it can have icons;
* fixed `AppTable::findActive()` method, now  it always uses the table alias so as not to generate conflicts;
* `HtmlHelper::img()` is now deprecated and will be removed in a later release. Use instead `image()` method.

### 2.25.2
* added `AppValidator::title()` rule method.

### 2.25.1
* fixed little bug for `AppValidator::personName()` method, with accented chars;
* the `IconHelper` recognizes the `fa-brands` class as the base class for icons;
* the `flash.php` template element has the `border-0` class;
* the `paginator.php` template element has the `d-print-none` class.

### 2.25.0
#### Specific changes to `FormHelper`
`FormHelper` has been rewritten from scratch.
Generally the use of templates has been improved and simplified and the `setTemplates()` and `resetTemplates()` methods,
which prevented template changes at runtime, are no longer used.
All the code has been optimized overall and has been concentrated in fewer methods (especially `_getLabel()` and
`control()`), avoiding affecting methods involving single inputs without container).

* introduced support for `multiple` selects (with or without checkboxes), fully compatible with Bootstrap;
* the `button()` method no longer automatically adds the `success` class to submit buttons; the `label()` method no 
  longer automatically adds the `fw-bolder` class (`bold` can be set via css);
* the checkboxes template has been improved and made fully compatible with Bootstrap;
* icon generation (for buttons, labels) has been simplified and improved (the `{{icon}}` template variable is used);
* the `ckeditor()` method now returns only the input element and no longer the form control element complete with label
  and wrapper div. To do this, you should use the `control()` method with the `type` option as `ckeditor`;
* fixed the class for selects inputs and a small bug for the `empty` option when set automatically;
* for validated forms, the `is-invalid` class is now managed via the `$_defaultConfig` property (`errorClass` key) and
  no longer by methods. But now nothing happens for fields that are correct (the `is-valid` class is no longer used).
  For the same reason, the `$isPost` property no longer exists;
* fixed a little bug for `_inputType()` method in self-determining whether a text input is a password input;
* `div` wrapper class is now set via template (for example, for inline forms) and no longer have the `input` class by
  default (useless now).

#### Other changes
* `HtmlHelper::link()` will automatically apply the `text-decoration-none` class when an icon is present, unless a
  `text-decoration-` class is already present;
* the `IconHelper` now uses the `fa` class by default;
* added other "action detectors": `is('add')`, `is('edit')`, `is('view')`, `is('index')`, `is('delete')`;
* added basic abstract `AppValidator` class with some general rules;
* added basic abstract `AppTable` class, with a default validator and a `findActive()` method;
* added basic `AppController` abstract class;
* added basic `View` class. This class loads the most common helpers, only if they haven't already been loaded;
* added the `AddButtonClassesTrait` with the `addButtonClasses()` method;
* the `TestCase` class no longer loads any plugins automatically (not even `MeTools`), but you have to do it manually.
  Instead, it automatically invokes `enableCsrfToken()` and `enableRetainFlashMessages()` methods, if they exist (i.e.
  if the `IntegrationTestTrait` is being used);
* the `OptionsParser` is now deprecated and will be removed in a later release. The same goes for the global
  `optionsParser()` function;
* the `components/jquery` package was marked as a conflicting dependency and can no longer be installed;
* the `FixComposerJsonCommand` no longer serves any purpose and has therefore been removed;
* the `CreatePluginsLinksCommand` was deprecated and has now been removed;
* `createDir()`, `createFile()`, `createLink()` and `folderChmod()` methods provided by the `Command` class was  
  deprecated and has now been removed;
* `TestCase::getTable()` is now deprecated and will be removed in a later release;
* `assertSqlEndsWith()`, `assertSqlEndsNotWith()`, `deleteLog()` and `getLogFullPath()` methods provided by the  
  `TestCase` class was deprecated and has now been removed.

## 2.24 branch
### 2.24.1
* fixed and perfected all command classes. Now they can catch any exceptions thrown by `Filesystem`;
* added `Command::isVerbose()` method;
* added `VENDOR` and `WWW_VENDOR` constants;
* `CreatePluginsLinksCommand` is now deprecated and will be removed in a later release. Use instead `PluginAssetsSymlinkCommand`;
* improved `TestCase::assertLogContains()` method;
* `Command::createDir()`, `Command::createFile()`, `Command::createLink()`, `Command::folderChmod()`, `TestCase::deleteLog()`  
  and `TestCase::getLogFullPath()` methods are now deprecated and will be removed in a later release;
* `assertSqlEndsWith()` and `assertSqlEndsNotWith()` methods provided by the `TestCase` class are now deprecated and  
  will be removed in a later release. Use instead `assertStringEndsWith()` and `assertStringEndsNotWith()`;
* `LibraryHelper::analytics()` method was deprecated and has now been removed;
* the `HiddenWidget` is useless and has now been removed;
* suggests `axllent/jquery` and no longer `components/jquery`. `axllent/jquery` added to `MeTools.VendorLinks` configuration;
* updated for php-tools 1.7.4.

### 2.24.0
* uses `Configure::readFromPlugins()` with `WritableDirs` and `VendorLinks` keys instead of the old `WRITABLE_DIRS` and  
  `VENDOR_LINKS` keys.

## 2.23 branch
### 2.23.3
* added `Configure` class, with `readFromPlugins()` method.

### 2.23.2
* fixed a little bug for `RunAllCommand` with the `--force` option.

### 2.23.1
* fixed a little bug for `FormHelper::control()` method with button on `append-text` and `prepend-text` options;
* `LibraryHelper::analytics()` is deprecated and will be removed in a later release;
* updated for php-tools 1.7.1.

### 2.23.0
* the `TestCase` class provides `$alias` and `$originClassName` properties through the `__get()` magic method;
* `ConsoleIntegrationTestTrait`, `ComponentTestCase` and `HelperTestCase` provide `$Command`, `$Component` and `$Helper`  
  properties through the `__get()` magic method, and no longer through the `setUp()` method;
* `RunAllCommand::$questions` requires a boolean for the `default` value (and no longer `Y` or `N` strings), while the  
  `command` value requires an instantiated command (and no longer a class-string). This simplifies coding and testing;
* `Command` class has been moved from `MeTools\Console` to `MeTools\Command`;
* `MockTrait::getOriginClassNameOrFail()` become `getOriginClassName()`, replacing the latter;
* `MockTrait::getAlias()` now only takes instances of `TestCase` as argument;
* added `CommandTestCase` to test commands. It provides `$Command` property through the `__get()` magic method;
* `TestCase` no longer always uses the `ReflectionTrait`;
* `getMockForComponent()` and `getMockForController()` methods provided by `MockTrait` and `assertExitWithError()` and  
  `assertExitWithSuccess()` methods provided by `ConsoleIntegrationTestTrait` had been deprecated and have been removed;
* `ConsoleIntegrationTestTrait` (and its `assertOutputNotEmpty()` method) no longer exists.

## 2.22 branch
### 2.22.4
* added `LibraryHelper::getOutput()` method;
* updated for CakePHP 4.4.

### 2.22.3
* fixed a regression for `FormHelper::createInline()` introduced with the `2.22.2` version;
* added `FormHelper::create()` method, with the `validation` option to disable Bootstrap's validation;
* fixed the class of submit buttons in inline forms.

### 2.22.2
* `assertExitWithError()` and `assertExitWithSuccess()` methods provided by `ConsoleIntegrationTestTrait` are now  
  deprecated. Use instead `assertExitError()` and `assertExitSuccess()`;
* `MockTrait::getMockForComponent()` is deprecated. Use instead `createPartialMock()`;
* `MockTrait::getMockForController()` is deprecated. Create instead a new instance of `Controller`;
* many, small code improvements.

### 2.22.1
* `Html::youtube()` and `BBCode::youtube()` methods can handle the starting second (`start` get parameter);
* `BBCode::youtube()` method removes unnecessary `<p>` tags around the code;
* added `BBCode::hr()` method;
* (re)added `HtmlHelper::shareaholic()` method;
* fixed code for `FormHelper::createInline()` method.

### 2.22.0
* `BootstrapDropdownHelper`, `BootstrapFormHelper` and `BootstraptHtmlHelper` become the new `DropdownHelper`,  
  `FormHelper` and `HtmlHelper`. The old helpers had been deprecated and completely replaced by the new ones;
* fixed `OptionsParser::tooltip()` for the new bootstrap;
* `datepicker()`, `datetimepicker()` and `timepicker()` methods provided by `LibraryHelper` were deprecated and have  
  now been removed.

## 2.21 branch
### 2.21.6
* improved and simplified plugin loading;
* improved `@method` tags for `BootstrapHtmlHelper`.

### 2.21.5
* added `iframe()` and `youtube()` methods to the `BootstrapHtmlHelper`;
* the `HtmlHelper` is now totally deprecated, use `BootstrapHtmlHelper` instead. In a later version, the latter  
  will take the place of the former, assuming its name;
* the `DropdownHelper` is now totally deprecated, use `BootstrapDropdownHelper` instead. In a later version, the latter  
  will take the place of the former, assuming its name;
* the `FormHelper` is now totally deprecated, use `BootstrapFormHelper` instead. In a later version, the latter will  
  take the place of the former, assuming its name;
* improved \MeTools\Core\Plugin::path()` method;
* updated for php-tools 1.6.5 and 1.7.0.

### 2.21.4
* `append-text` and `prepend-text` options for `BootstrapFormHelper::control()`  
  method can handle buttons;
* added `li()` and `meta()` methods for `BootstrapHtmlHelper`;
* improved `Command::createDir()` method and message in case of `mkdir` error;
* small and numerous improvements of descriptions, tags and code suggested by  
  PhpStorm.

### 2.21.3
* added `checkbox()`, `radio()` and `select()` methods for `BootstrapFormHelper`;
* fixed templates for `BootstrapFormHelper` with input groups;
* fixed little bug for `BootstrapFormHelper::submit()` method.

### 2.21.2
* added `BootstrapHtmlHelper`. For now this class is temporary, but in the future  
  it will replace `HtmlHelper`, assuming the same name. Some methods of the  
  `HtmlHelperTest` test class have been marked as deprecated, to indicate that  
  code is already covered by the new code;
* added `BootstrapDropdownHelper`. For now this class is temporary, but in the future  
  it will replace `DropdownHelper`, assuming the same name;
* `cssBlock()`, `cssStart()`, `cssEnd()`, `heading()` and `hr()` methods provided  
  by the `HtmlHelper` are now deprecated and will be removed in a later  
  version. No replacement will be provided;
* `scriptBlock()` and `scriptStart()` provided by the `HtmlHelper` are now deprecated  
  and will be removed in a later version. Use instead the parent method, with  
  the `block` option. These methods do not generate a deprecation message for now;
* small improvements for some `MockTrait` methods.

### 2.21.1
* added a theme for Bake. See the `README` file;
* added `IntegrationTestTrait::getStatusCode()` method;
* fixed a little bug for `HtmlHelper::link()`;
* added `BootstrapFormHelper`. For now this class is temporary, but in the future  
  it will replace `FormHelper`, assuming the same name. Some methods of the  
  `FormHelperTest` test class have been marked as deprecated, to indicate that  
  code is already covered by the new code;
* `datepicker()`, `datetimepicker()` and `timepicker()` methods provided by  
  `FormHelper` are now deprecated and will be removed in a later version.  
  Use instead the normal `control()` method, which will generate  
  `date`/`datetime-local`/`time` inputs, recognized by the browser;
* `datepicker()`, `datetimepicker()` and `timepicker()` methods provided by  
  LibraryHelper` are now deprecated and will be removed in a later version;
* little fixes for `FormHelper`, to make the output more consistent with what  
  Bootstrap requires;
* `MockTrait::getMockForHelper()` method has `$view` argument;
* updated some css file, to make the output more consistent with what Bootstrap  
  requires;
* added some i18n constants (`config/i18n_constants.php`), in order to be used universally;
* fixed the `flash` template element;
* requires at least CakePHP 4.2.

### 2.21.0
* numerous code adjustments for improvement and adaptation to PHP 7.4 new features;
* `OptionsParser` now uses the `$defaults` array property to store default values.  
  Added `addDefault()` method;
* fixed a wrong reference to the `ReflectionTrait`;
* `UploaderComponent::set()` method was deprecated and has now been removed;
* requires at least PHP 7.4 and CakePHP 4.1.

## 2.20 branch
### 2.20.9
* added tests for PHP 8.1;
* little fixes.

### 2.20.8
* removed useless method `IntegrationTestTrait::assertFlashMessage()`.

### 2.20.7
* added `TestCase::assertSqlEndsNotWith()` and `TestCase::assertSqlEndsWith()` methods.

### 2.20.6
* ready for `cakephp` 4.3.

### 2.20.5
* `CreateDirectoriesCommand`, `CreateVendorsLinksCommand` and `SetPermissionsCommand`  
  now ignore duplicate values.

### 2.20.4
* fixed for `phpunit` 9.5.10;
* migration to github actions.

### 2.20.3
* fixed a serious bug in URL generation for `HtmlHelper::iframe()`,  
  `HtmlHelper::image()` and `HtmlHelper::link()` methods, introduced since  
  2.19.11 version.

### 2.20.2
* `ComponentTestCase`, `ConsoleIntegrationTestTrait` and `HelperTestCase` create  
  real instances of the objects to be tested, and no more mock objects;
* `CreatePluginsLinksCommand` has been simplified;
* updated for php-tools 1.5.2;
* further improvement of function descriptions and tags.

### 2.20.1
* added `UploaderComponent::getFile()` and `UploaderComponent::setFile()` methods.  
  `UploaderComponent::set()` method is deprecated, use instead `setFile()`;
* fixed little bug for the `ConsoleIntegrationTestTrait`;
* extensive improvement of function descriptions and tags. The level of `phpstan`  
  has been raised.

### 2.20.0
* `MockTrait::getControllerAlias()` was deprecated and has been removed;
* updated for php-tools 1.5;
* ready for `php` 8.

## 2.19 branch
### 2.19.11
* fixed bug for `HtmlHelper::iframe()`, now the `$url` can be an array of parameters;
* fixed little bug for `HtmlHelper::image()` method with `$path` as array;
* fixed little bug for `HtmlHelper::link()` method with `$title` as array;
* updated for `php-tools` 1.4.7;
* extensive improvement of function descriptions and tags. The level of `phpstan`  
  has been raised.

### 2.19.10
* `UploaderComponent::save()` method no longer throws an exception if the  
  destination directory is not writable, but sets an error;
* fixed bug for `WRITABLE_DIRS` and `VENDOR_LINKS` configuration values. They no  
  longer override the values set by other plugins.

### 2.19.9
* added `getAlias()`, `getPluginName()` and `getTableClassNameFromAlias()`  
  methods for the `MockTrait`. Fixed `getOriginClassName()` and  
  `getOriginClassNameOrFail()` methods;
* `MockTrait::getControllerAlias()` method is now deprecated. Use instead `getAlias()`.

### 2.19.8
* `addButtonClasses()` and `delete()` methods provided by `OptionsParser` take  
  now a variable-length argument lists;
* updated for `php-tools` 1.4.6;
* ready for `phpunit` 10;
* added `phpstan`, so fixed some code.

### 2.19.7
* updated for `php-tools` 1.4.1.

### 2.19.6
* come back. Fixed little bug for `TestCase` class.

### 2.19.5
* updated for `cakephp` 4.1.
* fixed little bug for `TestCase` class.

### 2.19.4
* uses and suggests `npm-asset/fancyapps-fancybox` [GitHub](https://github.com/fancyapps/fancybox)  
  instead of `newerton/fancy-box`.

### 2.19.3
* the `UploaderComponent` can now handle files as `Laminas\Diactoros\UploadedFile`  
  instance. This allowed to simplify the component;
* prevents the plugins bootstrap from loading multiple times;
* the `ckeditor` init file automatically integrates `elfinder` as long as  
  [the `elfinder-cke.html` file exists](httè://github.com/Studio-42/elFinder/wiki/Integration-with-CKEditor-4);
* updated `Command` tests for `cakephp` 4.0.5.

### 2.19.2
* added `MockTrait::getOriginClassNameOrFail()` method;
* fixed little bug for `MockTrait::getOriginClassName()` method;
* changed `npm-asset/fortawesome--fontawesome-free` package with  
  `fortawesome/font-awesome`.

### 2.19.1
* fixed I18n translations.

### 2.19.0
* updated for `cakephp` 4 and `phpunit` 8;
* the `BBCodeHelper` have been removed.

## 2.18 branch
### 2.18.16
* little code fixes;
* the `BBCodeHelper` was deprecated and was removed.
* APIs are now generated by `phpDocumentor` and no longer by` apigen`.

### 2.18.15
* fixed little bug for `MockTrait::getOriginClassName()` method.

### 2.18.14
* added `\MeTools\Utility\BBCode` utility. This utility allows you to parse  
  BBCode also outside the view. The `BBCodeHelper` is now deprecated and will  
  be removed in a future release.

### 2.18.13
* for classes automatically created during tests, the `initialize()` method for  
  components, consoles and helpers is automatically called.

### 2.18.12
* added the `IconHelper`. `addIconToText()` and `icon()` methods have been moved  
  from the `HtmlHelper`;
* `HtmlHelper::js()` alias method has been removed;
* javascript `send_form()` function becomes `sendForm()`.

### 2.18.11
* added `TestCase::getTable()` method;
* added tests for lower dependencies.

### 2.18.10
* fixed little bug for `LibraryHelper::ckeditor()`.

### 2.18.9
* added `IntegrationTestTrait::assertSessionEmpty()` method;
* uses the Symfony's `Filesystem` class;
* fixed bug for `TestCase::tearDown()` method;
* updated for `php-tools` 1.2.8.

### 2.18.8
* `button()`, `postButton()` and `postLink()` methods provided by `FormHelper`  
  can be called with the first argument as `null`;
* `button()` and `link()` methods provided by `HtmlHelper` can be called with  
  the first argument as `null`;
* fixed `CreatePluginsLinksCommand`, now it works without the `AssetsTask`;
* updated for `php-tools` `1.2.6`.

### 2.18.7
* `TestCase::tearDown()` method no longer empties temporary files. This should  
  be done as appropriate;
* `UploaderComponent::getError()` method returns `null` with no errors;
* `Youtube::getId()` method returns `null` on failure;
* `Plugin::path()` takes only a string or `null` as first argument, and no more  
  arrays. It always returns a string and if you ask for the path of a file  
  that does not exist, it throws an exception;
* removed useless `HtmlHelper::div()` method.

### 2.18.6
* updated for `php-tools` 1.2.

### 2.18.5
* `MockTrait::getOriginClassName()` is now public and can take a string as arg;
* fixed a small bug in deleting logs during tests;
* fixed a small bug for the `MockTrait`.

### 2.18.4
* fixed bug for `ConsoleIntegrationTestTrait`;
* added [API](http://mirko-pagliai.github.io/me-tools).

### 2.18.3
* updated for `php-tools` 1.1.12.

### 2.18.2
* fixed bug, the `Assets` bootstrap was not loaded automatically;
* fixed bug in the output of some errors in the console.

### 2.18.1
* improved the `MockTrait`.
* you can load test plugins writing the `pluginsToLoad` variable in Configure;
* little code and test fixes;
* updated for `php-tools` 1.1.9.

### 2.18.0
* `InstallShell` has been replaced with console commands. Every method of the  
  previous class is now a `MeTools\Command\Install` class;
* `Shell` class has been removed, use instead the `Command` class;
* `MockTrait` moved from `MeTools\TestSuite\Traits` to `MeTools\TestSuite`.  
  `assertIsMock()` and `getMockForShell()` methods no longer exist; added  
  `getOriginClassName()`. `getMockForTable()` has been removed, use instead  
  `getMockForModel()` provided by CakePHP;
* `ConsoleIntegrationTestCase` and `IntegrationTestCase` classes have been  
  replaced with `ConsoleIntegrationTestTrait` and `IntegrationTestTrait, as  
  for CakePHP 3.7;
* removed `TestCaseTrait`, methods have been moved to the `TestCase` class;
* `cakephp-assets`, `jquery`, `bootstrap`, `bootstrap-datetimepicker`, `fancy-box`  
  and `fortawesome--fontawesome-free` are no longer required packages  
  (`cakephp-assets` is required for developing), but only suggested packages;
* removed `clearDir()` global function. Use instead `safe_unlink_recursive()`  
  provided by `php-tools`;
* removed `ME_TOOLS` constants. It no longer uses also the `ASSETS` constant;
* updated for CakePHP 3.7.

## 2.17 branch
### 2.17.6
* added `Command` class. It can be used instead of the `Shell` class;
* added `ConsoleIntegrationTestCase::assertOutputNotEmpty()` method.

### 2.17.5
* added `MockTrait`, `ComponentTestCase` and `HelperTestCase` classes for test  
  suite;
* the `ConsoleIntegrationTestCase` class automatically creates an instance of  
  the shell class. Added `getShellMethods()` method;
* added `TestCaseTrait::assertIsMock()` assert method.

### 2.17.4
* fixed bug in the integration with CKEditor when uploading images (see  
  [this issue](https://github.com/sunhater/kcfinder/issues/171)).

### 2.17.3
* updated for CakePHP 3.6.

### 2.17.2
* added `Shell::hasParam()` method.

### 2.17.1
* updated for cakephp-assets 1.3, php-tools 1.1 and Bootstrap 4.1;
* some fixes for Font Awesome icons.

### 2.17.0
* updated Font Awesome to 5.1. So the `InstallShell::copyFonts()` method has  
  been removed and the `webroot/fonts` directory no longer exists;
* by default, buttons generated by helpers will have the `btn-light` class.

## 2.16 branch
### 2.16.10
* `UploaderComponent::error()` renamed as `getError()`;
* `Shell::folderChmod()` has a default value for the `$chmod` argument;
* added `OptionsParser::consume()` method;
* added `TestCaseTrait::getLogFullPath()` method. So now `assertLogContains()`  
  and `deleteLog()` methods can take absolute paths and filenames with or  
  without extension;
* fixed code and output for the `Shell` class;
* removed `TestCaseTrait::deleteAllLogs()` method;
* removed `folderIsWriteable()` global function. Use instead  
  `is_writable_resursive()` provided by `php-tools`.

### 2.16.9
* updated for CakePHP 3.6 and cakephp-assets 1.2.

### 2.16.8
* removed `assertArrayKeysEqual()`, `assertFileExists()`, `assertFileNotExists()`,  
  `assertInstanceOf()`, `assertIsArray()`, `assertIsObject()`,  
  `assertIsString()`, `assertObjectPropertiesEqual()` methods from  
  `\MeTools\TestSuite\Traits\TestCaseTrait` and the `Apache` utility.  
  Now they are provided by the `mirko-pagliai/php-tools` package.

### 2.16.7
* full compatibility with Windows;
* now the `UploaderComponent::save()` method takes the `$filename` parameter  
  instead of the `$basename` parameter;
* updated for jQuery 3.3;
* now it uses the `mirko-pagliai/php-tools` package. This also replaces  
  `mirko-pagliai/reflection`.

### 2.16.6
* updated for Bootstrap 4.0.0.

### 2.16.5-RC3
* `UploaderComponent::save()` method now accepts the optional `basename` argument;
* updated for Bootstrap 4 beta 3.

### 2.16.4-RC2
* improved the `InstallShell` class;
* `InstallShell::all()`, if called with the `force` parameter, executes only  
  the default methods;
* Css files and KCFinder files to be loaded into the CKEditor editor are  
  automatically set;
* added some plugins for CKEditor and set some default values for the tables and iframes.

### 2.16.3-RC1
* added `optionsParser()` global function, that returns an instance of  
  `OptionsParser`;
* the CKEditor can now show a style similar to that of the article preview.

### 2.16.2-beta
* `OptionsParserTrait` has been replaced with the `OptionsParser` class. The  
  `HtmlHelper` class now provides `buildIconClasses()` and `addIconToText()`  
  methods, while the `iconClass()` method has been deleted;
* `toAttribute()` global function has been removed. Use instead  
  `OptionsParser::toString()` method;
* updated for Bootstrap 4 beta 2.

### 2.16.1-beta
* little fixes on css rules.

### 2.16.0-beta
* updated all the code for Bootstrap 4;
* added the `HiddenWidget`, to properly render a hidden field;
* improved forms templates generated by the `FormHelper`;
* added a custom `bootstrap-datetimepicker` js file for Bootstrap 4;
* moved `MeTools\Utility\OptionsParserTrait` to namespace  
  `MeTools\View\OptionsParserTrait`;
* the `METOOLS` constant has become `ME_TOOLS`.

## 2.15 branch
### 2.15.1
* the fancybox no longer uses the "thumb" plugin to ensure greater compatibility  
  with jQuery 3.x;
* added `toAttributes()` global function.

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
* global functions have been divided into `config/functions/global.php` and  
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
* the `Install` shield now copies configuration files;
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
* improved the log management;
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
