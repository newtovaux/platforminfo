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
        if grep --quiet "Stable tag: $version" readme.txt; then
            if grep --quiet "^#### $version ####" readme.txt; then
                if grep --quiet "* Version:           $version" platforminfo.php; then
                    if grep --quiet "## $version ##" CHANGELOG.md; then
                        gh release create "$version" -F notes.txt -t "$version"
                    else
                        echo "Unable to find ## $version ## in CHANGELOG.md"
                    fi
                else
                    echo "Unable to find * Version: $version in platforminfo.php"
                fi
            else
                echo "Unable to find #### $version #### in readme.txt"
            fi
        else
            echo "Unable to find Stable tag $version in readme.txt"
        fi
    else
        echo "Unable to find notes.txt"
    fi
fi