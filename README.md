# MeTools #
MeTools is a CakePHP plugin to improve applications development.

## Installation ##
Extract MeTools in **Plugin/**.

Load MeTools in **bootstrap.php**:

	CakePlugin::load('MeTools');

In your webroot directory, create (or copy) a link to the MeTools webroot:

	cd webroot
	ln -s ../Plugin/MeTools/webroot/ MeTools

### Libraries and script ###
**meCms** uses different libraries or scripts:
- JQuery 1.9.1 ([site](http://jquery.com));
- Bootstrap 2.3.1 ([site](http://twitter.github.com/bootstrap));