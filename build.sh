#!/bin/bash

rm platform.zip

pushd ..
zip -9 platform/platform.zip -r platform/ -x 'platform/*.git*' -x 'platform/*.zip' -x 'platform/build.sh' -x 'platform/vendor/*' -x 'platform/composer.*' -x 'platform/*.xml'
popd
