#!/bin/bash
# Ask for version
echo "-- MeTools build script --"
echo "Please, enter the version number: "
read version
version="metools-$version"

# Create build/last-version
rm -r -f build/last-version
mkdir -p build/last-version

# Copy in build/last-version
cp -R Config/ Console/ Controller/ Lib/ Locale/ Model/ Utility/ \
Vendor/ View/ webroot/ COPYNG README.md build/last-version

# Enter build/last-version
cd build/last-version

# Delete uncompressed files
rm -f webroot/css/datepicker.css webroot/css/default.css \
webroot/css/forms.css webroot/css/syntaxhighlighter.css \
webroot/js/bootstrap-datepicker.it.js webroot/js/bootstrap-datepicker.js \
webroot/js/default.js webroot/js/slugify.js

# Go back to build/
cd ../

# Create the tar archive
tar -czf $version.tar.gz last-version/ --strip-components 1
echo "The file $version.tar.gz was created"