#!/bin/bash
# PlatformInfo Release Script
# Run: ./release.sh


if ! which gh 2>&1 > /dev/null; then
    echo "GitHub CLI not found - gh"
    exit
fi

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
    # Does the notes.txt file exist?
    if [ -f "notes.txt" ]; then
        # Does the tag you're trying to release exist as "Stable tag:"" in the readme.txt?
        if grep --quiet "Stable tag: $version" readme.txt; then
            # Does the tag you're trying to release have a version entry in the readme.txt?
            if grep --quiet "^#### $version ####" readme.txt; then
                # Does the tag you're trying to release have a version entry in platforminfo.php?
                if grep --quiet "* Version:           $version" platforminfo.php; then
                    # Does the tag you're trying to release have a version entry in the Changelog?
                    if grep --quiet "## $version ##" CHANGELOG.md; then
                        # Create release with specified version
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
else
    echo "Not confirmed."
fi