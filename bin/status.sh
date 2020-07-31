#!/bin/bash
for repo in all bundled content-staging core graphql inventory sample-data; do
    cd magento2-replace-${repo}
    echo "Switching to magento2-replace-${repo}/master"
    git checkout -q master
    git status -s || exit

    for version in 2.3.1 2.3.2 2.3.3 2.3.4 2.3.5 2.3.6 2.4.0; do
        branch=magento-$version
        echo "Switching to magento2-replace-${repo}/${branch}"
        git checkout -q $branch
        git status -s || exit
        composer validate composer.json --ansi --no-check-all --no-check-publish || exit
    done
    cd - >/dev/null
done
