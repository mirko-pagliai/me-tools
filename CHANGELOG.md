# 2.x branch
## 2.6 branch
### 2.6.2
* fixed serious bug for fancybox; 
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
* log reports request URL, referer URL and client ip, both for exceptions and errors;
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
* `iframe()` and `youtube` methods from `HtmlHelper` now support `$ratio` to create responsive embed.

### 2.2.6
* fixed serious bug for CKEditor.

### 2.2.5
* support for some BBCode;
* support for Youtube videos;
* fixed bug for Shareaholic.

### 2.2.4
* added the ErrorHandler class. This allows to track the "request URL" also for errors and not only for exceptions;
* fixed bug with thumbs on remote files.

### 2.2.3
* improved the code for shells.

### 2.2.2
* the Install shield now copies configuration files;
* fixed a lot of strings and translations.

### 2.2.1
* fixed a serious bug.

### 2.2.0
* added the Asset helper. Removed the Layout helper and the Compress shell. Now assets are automatically generated when required;
* fixed little bugs.

## 2.1 branch
### 2.1.2
* fixed a bug with forms on Firefox.

### 2.1.1-RC3
* added the installer console.

### 2.1.0-RC2
* added support for Shareaholic;
* jQuery, Bootstrap, Bootstrap Date/Time Picker, Moment.js and Font Awesome are installed via Composer;
* improved the logs management;
* fixed little bugs.

## 2.0 branch
### 2.0.1-RC1
* fixed many little bugs;
* updated Bootstrap 3 Date/Time Picker to 4.17.37.

### 2.0.0-beta
* all the code has been completely rewritten for CakePHP 3.x. Several optimizations have been applied;
* for the actions of loading/adding files, error messages are more intelligible;
* added the `MeEmail` class to simplify sending emails;
* added the `MeRequest` class to handle HTTP requests;
* added the `ThumbHelper` to generate thumbnails;
* updated Moment.js to 2.10.6, Bootstrap 3 Date/Time Picker to 4.15.35, Font Awesome to 4.4.0, jQuery to 2.1.4, Bootstrap to 3.3.5 and Moment.js to 2.10.3.

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
* added some methods to `Plugin` utility. The `System` utility has been divided into several utilities;
* added the changelog file.