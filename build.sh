#!/bin/bash

# Generates the plugin in zip file, for testing

ZIPFILE="platforminfo.zip"

# Remove the old zip file, if one exists
if [ -f "$ZIPFILE" ]; then
    echo "Removing old zip file: $ZIPFILE"
    rm "$ZIPFILE"
fi

# Move up a directory
pushd .. 2>&1 > /dev/null

# Zip the plugin
zip -q -9 "platforminfo/$ZIPFILE" -r platform/ \
    -x 'platforminfo/*.git*' \
    -x 'platforminfo/*.zip' \
    -x 'platforminfo/build.sh' \
    -x 'platforminfo/vendor/*' \
    -x 'platforminfo/composer.*' \
    -x 'platforminfo/*.xml'

popd 2>&1 > /dev/null

if [ -f "$ZIPFILE" ]; then
    echo "Plugin zip file created: $ZIPFILE"
    ls -ltrh "$ZIPFILE"
fi