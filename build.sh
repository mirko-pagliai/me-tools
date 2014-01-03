#!/bin/bash
# Ask for version
echo "Please, enter the version number: "
read version
version="metools-$version"

# Create build/$version
rm -r -f build/$version
mkdir -p build/$version

# Copy in build/$version
cp -R Config/ Console/ Controller/ Lib/ Locale/ Model/ Utility/ Vendor/ View/ webroot/ COPYNG README.md build/$version

# Enter build/
cd build/

# Delete uncompressed CSS files
rm -f $version/webroot/css/datepicker.css
rm -f $version/webroot/css/default.css
rm -f $version/webroot/css/forms.css
rm -f $version/webroot/css/syntaxhighlighter.css

# Delete uncompressed JS files
rm -f $version/webroot/js/bootstrap-datepicker.it.js
rm -f $version/webroot/js/bootstrap-datepicker.js
rm -f $version/webroot/js/default.js
rm -f $version/webroot/js/slugify.js

# Create archives
tar -czf $version.tar.gz $version
echo "The file $version.tar.gz was created"

# Delete build/$version
rm -r -f $version 