# Magento Composer Replacement Tool
This repository offers 

## Installation of this plugin
```bash
composer require yireo/magento2-replace-tools
```

## Replacing packages
List all current composer replacements:
```bash
composer replace:list
```

Replace a specific package:
```bash
composer replace:add foo/bar '2.0'
```

Remove a specific replacement:
```bash
composer replace:remove foo/bar
```

Remove a specific package (by using a version set to `*`):
```bash
composer replace:add foo/bar
```

## Replacing packages by bylk
Replace all Magento Multi Source Inventory packages:
```bash
composer replace:bulk:add yireo/magento2-replace-inventory
```

This adds all replacements from this meta-package `yireo/magento2-replace-inventory` to your own `replace` section. And it also adds an additional section like the following:
```json
    "extra": {
        "replace": {
            "bulk": {
                "yireo/magento2-replace-inventory": true
            }
        }
    },
```

Replace all Magento GraphQL packages, but not the `magento/module-graph-ql` package itself:
```bash
composer replace:bulk:add yireo/magento2-replace-graphql
composer replace:bulk:exclude magento/module-graph-ql @todo
composer replace:bulk:build @todo
```

This adds all replacements from this meta-package `yireo/magento2-replace-graphql`, except the package `magento/module-graph-ql` to your own `replace` section. And it also adds an additional section like the following:
```json
    "extra": {
        "replace": {
            "bulk": {
                "yireo/magento2-replace-inventory": true
            },
            "exclude": {
                "magento/module-graph-ql": true
            }
        }
    },
```

---

## INFORMATION BELOW IS TO BE DEPRECATED

# Magento 2 removal of (optional) modules included in the core
This repository contains tools to maintain the following repositories:

- [yireo/magento2-replace-bundled](https://github.com/yireo/magento2-replace-bundled) removes third party bundled extensions
Content Staging modules
- [yireo/magento2-replace-content-staging](https://github.com/yireo/magento2-replace-content-staging) removes optional Content Staging modules
- [yireo/magento2-replace-core](https://github.com/yireo/magento2-replace-core) removes optional core modules
- [yireo/magento2-replace-graphql](https://github.com/yireo/magento2-replace-graphql) removes optional GraphQL modules
- [yireo/magento2-replace-inventory](https://github.com/yireo/magento2-replace-inventory) removes optional MSI modules
- [yireo/magento2-replace-sample-data](https://github.com/yireo/magento2-replace-sample-data) removes sample data modules
- [yireo/magento2-replace-all](https://github.com/yireo/magento2-replace-all) removes all packages listed in the other directories

Please note that the `replace` feature of composer as being used in these repositories is not well documented and probably abused a bit. If you
are not willing to invest time to troubleshoot this yourself, please forget about this approach entirely so that we don't waste anyones time.

## Usage
The packages in the repositories above can be installed using simple composer commands (for instance using the `magento2-replace-bundled` package):

- `composer require yireo/magento2-replace-bundled:^4.0 --no-update` for Magento 2.4.X
- `composer require yireo/magento2-replace-bundled:^3.0 --no-update` for Magento 2.3.X

And then:

    composer install

Once you install a replacement, make sure to wipe out the `generated/` folder first and next, run `bin/magento setup:di:compile` and `bin/magento setup:upgrade` to see if Magento still works. Please note that these steps are generic developer steps, not related to this repository.

    rm -r generated/
    bin/magento setup:di:compile
    bin/magento setup:upgrade

## Troubleshooting
**Please note that in the tips below the `magento2-replace-bundled` package is assumed. Substitute this for the package that you are trying to install.**

### If a package can not be installed right away
If the `composer require` command does not work for you, try the following:

    rm -r vendor/
    composer require --no-update yireo/magento2-replace-bundled
    composer install

If this fails, try the following:

    rm -r vendor/ composer.lock
    composer require --no-update yireo/magento2-replace-bundled
    composer update

Last but not least, try to add the GitHub repository 

    composer config repositories.magento2-replace-all vcs git@github.com:yireo/magento2-replace-bundled.git
    composer require --no-update yireo/magento2-replace-bundled
    composer update

Please do **not** contact us for support (via email, Slack or GitHub issue) before having tried out the commands, including the `rm` commands. If you think that running the `rm` command is not needed, because you know how `composer update` works, let's debate this **after** you have actually tried out the instructions above.

### What else could fail
The following things might fail with these replacements:

- A certain extension might have a dependency on Magento module X, documented via its `composer.json` or not. If so, skip
  our main package but copy the `replace` lines to your own project composer.
- After installing certain extensions, everything works fine on a composer level, but things fail when compiling DI
  (`setup:di:compile`). If this concerns a setup with only core packages, make sure to open an **Issue**. 

## FAQ
#### I try to install this with `composer require a/b` but get errors
Please note that this kind of question is not going to be answered anymore, except here: Do **not** use a simple `composer require a/b` command. It is not documented above, it is not part of the procedure and it does not work. Do not reason that if you know composer, you know that a simple `composer require a/b` must work. If you think composer replacements are installed the way as composer packages, you do not know composer replacements.

If you want to receive support, follow along with **all** of the commands outlined above. And stick to it. Don't argue, don't reason, but stick with it. Next, if all of the workarounds with composer commands fail, only then report an issue on GitHub.

#### Your extension does not work
You are damn right it does not! The reason is that it is not an extension. This is **not** about installing Magento modules. This is about replacing composer packages with nothing. The *extension* is not there, it is not a Magento module. It is rather a carefully crafted composer configuration that could be copied manually or installed with the right procedure. It is a composer meta-package with an undocumented trick. If you don't like it, don't use it.

#### Installing a package leads to many errors
Intruiging, isn't it? Yes, this could happen. Perhaps some modules that you are replacing are in use with your own custom code. Or perhaps you are relying on other third party extensions that have yet an undocumented dependency that conflicts with this `replace` trick. If you are not willing to troubleshoot this, simply skip this trick and move on. If you are willing to troubleshoot this, copy the `replace` lines to your own `composer.json` and remove lines one-by-one until you have found the culprit.

#### Is a certain package compatible with Magento 2.x.y?
Theoretically, yes. Make sure to understand that these packages are not modules, not libraries, not Magento extensions. It is a gathering of
hacks. So, if you understand the benefit of the `replace` trick in composer, you can use these repository to ease the pain of upgrading.

One conceptual idea in these repositories is to try to keep track of the main Magento version by creating a branch `2.x.y` with a corresponding release `x.y.z`. So, Magento 2.3.5 matches with the replace branch `3.5.*`. Magento 2.4.1 matches with the replace branch `4.1`. By adding a dependency with `^4.0` in your `composer.json`, this will automatically upgrade to any `4.X` version, but exclude a major bump to `5.X`.

Sometimes the actual work falls behind, which by no means indicates that the current bundling of tricks no longer works. Simply, install this package using `composer` and see if this works for you (see below).

#### How do I upgrade the replacements to Magento 2.4.X?
Please note the above on the versioning strategy. Once that's understood, the actual implementation is simple: `composer require yireo/magento2-replace-core:^4.0 --no-update`.

#### How to test if this is working?
Take your test environment. Install the relevant packages. If this works, run `bin/magento setup:di:compile` (in both Developer Mode and Production Mode) to see if there are any errors. If this fails, feel free to report an issue here. If this works, you could assume that this works ok.

Remember this repository offers a smart hack, not a supported solution. You can also live with a slower Magento installation that fully complies with the Magento standards (and ships with modules you don't use and/or like).

#### How do I know if something is replaced?
Unfortunately, composer does not offer a CLI for this and because the replacements are stored in these packages, they are not mentioned in your own projects `composer.json` (unless you put them there). However, by opening up the `composer.lock` file and searching for the keyword `replace` you can see which packages are replaced by all packages in your installation. A simple `composer show yireo/magento2-replace-bundled` shows which replacements are included in a specific package.

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

