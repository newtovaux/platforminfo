#!/bin/bash

# Generates the plugin in zip file, for testing

ZIPFILE="platform.zip"

# Remove the old zip file, if one exists
if [ -f "$ZIPFILE" ]; then
    echo "Removing old zip file: $ZIPFILE"
    rm "$ZIPFILE"
fi

# Move up a directory
pushd .. 2>&1 > /dev/null

# Zip the plugin
zip -q -9 "platform/$ZIPFILE" -r platform/ \
    -x 'platform/*.git*' \
    -x 'platform/*.zip' \
    -x 'platform/build.sh' \
    -x 'platform/vendor/*' \
    -x 'platform/composer.*' \
    -x 'platform/*.xml'

popd 2>&1 > /dev/null

if [ -f "$ZIPFILE" ]; then
    echo "Plugin zip file created: $ZIPFILE"
    ls -ltrh "$ZIPFILE"
fi