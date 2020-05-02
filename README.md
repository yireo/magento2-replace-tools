# Magento 2 removal of optional modules
This repository contains tools to maintain the following repositories:

- [yireo/magento2-replace-bundled](https://github.com/yireo/magento2-replace-bundled) removes third party bundled extensions
- [yireo/magento2-replace-core](https://github.com/yireo/magento2-replace-core) removes optional core modules
- [yireo/magento2-replace-graphql](https://github.com/yireo/magento2-replace-graphql) removes optional GraphQL modules
- [yireo/magento2-replace-inventory](https://github.com/yireo/magento2-replace-inventory) removes optional MSI modules
- [yireo/magento2-replace-content-staging](https://github.com/yireo/magento2-replace-content-staging) removes optional Content Staging modules
- [yireo/magento2-replace-all](https://github.com/yireo/magento2-replace-all) removes all packages listed in the other directories

Please note that the `replace` feature of composer as being used in these repositories is not well documentated and probably abused a bit. If you
are not willing to invest time to troubleshoot this yourself, please forget about this approach entirely so that we don't waste anyones time.

## Usage
The packages in the repositories above can be installed using simple composer commands (for instance using the `magento2-replace-bundled` package):

    composer require yireo/magento2-replace-bundled

Once you install a replacement, make sure to wipe out the `generated/` folder first and next, run `bin/magento setup:di:compile` to see if Magento still works. Please note that these steps are generic developer steps, not related to this repository.

    rm -r generated/
    bin/magento setup:di:compile

## Troubleshooting
### If a package can not be installed right away
If the `composer require` command does not work for you, try the following:

    composer require --no-update yireo/magento2-replace-bundled
    composer install

If this fails, try the following:

    rm -r vendor/
    composer require --no-update yireo/magento2-replace-bundled
    composer install

If this fails, try the following:

    rm -r vendor/ composer.lock
    composer require --no-update yireo/magento2-replace-bundled
    composer install

### What else could fail
The following things might fail with these replacements:

- A certain extension might have a dependency on Magento module X, documented via its `composer.json` or not. If so, skip
  our main package but copy the `replace` lines to your own project composer.
- After installing certain extensions, everything works fine on a composer level, but things fail when compiling DI
  (`setup:di:compile`). If this concerns a setup with only core packages, make sure to open an **Issue**. 

## FAQ
#### Installing a package leads to many errors
Intruiging, isn't it? Yes, this could happen. Perhaps some modules that you are replacing are in use with your own custom code. Or perhaps you are relying on other third party extensions that have yet an undocumented dependency that conflicts with this `replace` trick. If you are not willing to troubleshoot this, simply skip this trick and move on. If you are willing to troubleshoot this, copy the `replace` lines to your own `composer.json` and remove lines one-by-one until you have found the culprit.

#### Is a certain package compatible with Magento 2.x.y?
Theoretically, yes. Make sure to understand that these packages are not modules, not libraries, not Magento extensions. It is a gathering of
hacks. So, if you understand the benefit of the `replace` trick in composer, you can use these repository to ease the pain of upgrading.

One conceptual idea in these repositories is to try to keep track of the main Magento version by creating a branch `2.x.y` with a corresponding release `2.x.y`. Sometimes the actual work falls behind, which by no means indicates that the current bundling of tricks no longer works. Simply, install this package using `composer` and see if this works for you (see below).

#### How to test if this is working?
Take your test environment. Install the relevant packages. If this works, run `bin/magento setup:di:compile` (in both Developer Mode and Production Mode) to see if there are any errors. If this fails, feel free to report an issue here. If this works, you could assume that this works ok.

Remember this repository offers a smart hack, not a supported solution. You can also live with a slower Magento installation that fully complies with the Magento standards (and ships with modules you don't use and/or like).

## Testing
To test if all packages are valid, I have used the script `magento2-run-tests.sh` included in this repo. 
Copy this script to your system and run it. The scripts argument defaults to using the `@dev` versions of these
replace packages:

    ./magento2-run-tests.sh 2.3.4

This will create a new Magento environment. If you would like to use an existing environment, use a second argument for the directory of your environment:

    ./magento2-run-tests.sh 2.3.4 /var/www/html/my-magento

Do NOT use this on a live site, because it will break things.

In a generic environment, all tests (and therefore, all possible combinations of the replace packages) should work.

## Development
This repository is meant to be checked out together with the other repositories mentioned above, within the same parent folder.

### magento2-generate-replace-all.php
This tool will collect all `replace` entries and merge them together in the repository `yireo/magento2-replace-all`.

