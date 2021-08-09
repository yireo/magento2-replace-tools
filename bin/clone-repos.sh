#!/bin/bash
cd ../
for repo in all bundled content-staging core graphql inventory sample-data; do
    git clone git@github.com:yireo/magento2-replace-${repo}.git
done

