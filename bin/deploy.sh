#!/bin/bash
for repo in all bundled content-staging core graphql inventory sample-data; do
    cd magento2-replace-${repo}
    git checkout master
    git pull origin master
    cat ../README.md | sed s/REPONAME/$repo/ > README.md
    git commit README.md -m 'Updated README'
    git push origin master

    for version in 2.3.1 2.3.2 2.3.3 2.3.4 2.3.5 2.3.6 2.4.0; do
        branch=magento-$version
        git checkout $branch
        git pull origin $branch
        cat ../README.md | sed s/REPONAME/$repo/ > README.md
        git commit README.md -m 'Updated README'
        git push origin $branch
    done
    cd -
done
