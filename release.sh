#!/bin/bash
# PlatformInfo Release Script
# Run: ./release.sh

echo 'PlatformInfo Release'
echo
echo 'Enter the version number (e.g. 1.2.3): '
read version
echo "You entered '$version', is that correct? [Yn]"
read confirm1
if [ "$confirm1" == "Y" ]; then
    echo "Thanks for your confirmation."
    echo "Generating notes file..."
    echo "Release $version" > notes.txt
    echo "See: https://github.com/newtovaux/platforminfo/blob/main/CHANGELOG.md" >> notes.txt
    if [ -f "notes.txt" ]; then
        gh release create "$version" -F notes.txt -t "$version"
    fi
fi