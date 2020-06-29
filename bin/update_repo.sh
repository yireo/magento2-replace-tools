#!/bin/bash
git checkout master
git pull origin master
git push origin master

while read VERSION ; do
    git checkout magento-$VERSION || exit
    git pull origin magento-$VERSION || exit
    composer validate composer.json --ansi --no-check-all --no-check-publish || exit
    git push origin magento-$VERSION || exit
done <<EOF
2.3.1
2.3.2
2.3.3
2.3.4
2.3.5
EOF
