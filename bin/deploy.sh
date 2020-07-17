#!/bin/bash
for repo in all bundled content-staging core graphql inventory; do
    cd magento2-replace-${repo}
    for version in 2.3.3 2.3.4 2.3.5; do
        branch=magento-$version
        git checkout $branch
        cp -R ../magento2-replace-tools/github/workflows/* .github/workflows/
        echo $version > .github/workflows/magento_version.txt
        if [ "$version" == "2.3.5" ]; then
            echo "2.3.5-p1" > .github/workflows/magento_version.txt
        fi
        git add .github/
        git commit .github -m 'New CI files'
        git push origin $branch
    done
    cd -
done
