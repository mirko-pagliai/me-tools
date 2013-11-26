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

# Delete private config files
rm -f $version/Config/recaptcha.php

# Create archives
tar -czf $version.tar.gz $version
echo "The file $version.tar.gz was created"

# Delete build/$version
rm -r -f $version 