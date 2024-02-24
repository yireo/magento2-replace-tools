# Magento Composer Replacement Tool
**This repository offers a composer plugin to help you manage composer replacements in your root `composer.json`. Once this package is installed, the composer plugin is installed, which allows you to manage replacements via specific commands (`composer replace:?`). To make sure replacements don't conflict, this plugin adds its own section `extra.replace` to your `composer.json` as well.**

## Quickstart
```bash
composer require yireo/magento2-replace-tools # Require this plugin
composer replace:bulk:add yireo/magento2-replace-bundled # Add a replacement bulk package
composer replace:build # Rebuild your composer.json based upon this
composer update --lock # Actually update all your dependencies
```

## Installation of this plugin
```bash
composer require yireo/magento2-replace-tools
```

## General usage
Through a series commands, this composer plugin aims to help you manage your `replace` section more efficiently. Instead of individually adding packages, packages are added in bulk through an additional composer section `extra.replace`:

```json
{
    "replace": {
        "klarna/module-kp-graph-ql": "*",
        "magento/module-async-order-graph-ql": "*",
        "magento/module-authorizenet-graph-ql": "*",
        "magento/module-braintree-graph-ql": "*",
        "magento/module-bundle-graph-ql": "*",
        "magento/module-catalog-graph-ql": "*",
        ...
        "yireo/example-graph-ql"
    },
    "extra": {
        "replace": {
            "bulk": {
                "yireo/magento2-replace-graph-ql"
            },
            "exclude": {
                "magento/module-graph-ql": true
            },
            "include": {
                "yireo/example-graph-ql": true
            }
        }
    }
}
```

## Replacing packages (any composer project)
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

## Replacing packages by bulk (Magento-specific)
Replace all Magento Multi Source Inventory packages:
```bash
composer replace:bulk:add yireo/magento2-replace-inventory
```

This adds all replacements from this meta-package `yireo/magento2-replace-inventory` to your own `replace` section. And it also adds an additional section like the following:
```json
    "extra": {
        "replace": {
            "bulk": {
                "yireo/magento2-replace-inventory"
            }
        }
    },
```

Replace all Magento GraphQL packages, but not the `magento/module-graph-ql` package itself, but again also replacing a package `yireo/example-graph-ql`:
```bash
composer replace:bulk:add yireo/magento2-replace-graphql
composer replace:exclude magento/module-graph-ql
composer replace:include yireo/example-graph-ql
composer replace:validate
composer replace:build
```

This adds all replacements from this meta-package `yireo/magento2-replace-graphql` (except the package `magento/module-graph-ql` but including the package `yireo/example-graph-ql`) to your own `replace` section. And it also adds an additional section like the following:
```json
{
  "replace": {
      "klarna/module-kp-graph-ql": "*",
      "magento/module-async-order-graph-ql": "*",
      "magento/module-authorizenet-graph-ql": "*",
      "magento/module-braintree-graph-ql": "*",
      "magento/module-bundle-graph-ql": "*",
      "magento/module-catalog-graph-ql": "*",
      ...
      "yireo/example-graph-ql"
  },  
  "extra": {
    "replace": {
      "bulk": {
        "yireo/magento2-replace-graph-ql"
      },
      "exclude": {
        "magento/module-graph-ql": "*"
      },
      "include": {
        "yireo/example-graph-ql": "*"
      }
    }
  }
}
```

### Note about `replace:build`

⚠️ Warning: Due to the nature of its implementation, `replace:build` will replace your existing `replace` section in `composer.json`. If you replaced any individual dependencies here, make sure to re-add them after `replace:build`. At the moment this composer extension does not maintain the existing `replace` section of your `composer.json`. If you want to have this tool to manage this individual dependency for you, use the `extra.replace.include` section (see above).

## Available bulk packages

- [yireo/magento2-replace-bundled](https://github.com/yireo/magento2-replace-bundled) removes third party bundled extensions
- [yireo/magento2-replace-content-staging](https://github.com/yireo/magento2-replace-content-staging) removes optional Content Staging modules
- [yireo/magento2-replace-core](https://github.com/yireo/magento2-replace-core) removes optional core modules
- [yireo/magento2-replace-graphql](https://github.com/yireo/magento2-replace-graphql) removes optional GraphQL modules
- [yireo/magento2-replace-inventory](https://github.com/yireo/magento2-replace-inventory) removes optional MSI modules
- [yireo/magento2-replace-sample-data](https://github.com/yireo/magento2-replace-sample-data) removes sample data modules
- [yireo/magento2-replace-all](https://github.com/yireo/magento2-replace-all) removes all packages listed in the other directories

Please note that the `replace` feature of composer as being used in these repositories is not well documented and probably abused a bit. If you are not willing to invest time to troubleshoot this yourself, please forget about this approach entirely so that we don't waste anyones time.

### Building composer replacements
Use the following command to configure your `composer.json` for using bulk replacements:

    composer replace:bulk:add yireo/magento2-replace-bundled
    composer replace:bulk:add yireo/magento2-replace-inventory
    composer replace:bulk:add yireo/magento2-replace-graphql
    composer replace:bulk:add yireo/magento2-replace-sample-data
    composer replace:validate
    composer replace:build

### Using composer replacements
Once you have a `replace` section in your composer.json file

    rm -r vendor/
    composer update --lock

Do not just use `composer install`. Do not use regular composer commands, but please follow this procedure literally and to the point.

## After having replaced Magento composer packages
After you have installed a composer replacement, make sure to wipe out the `generated/` folder first and next, run `bin/magento setup:di:compile` and `bin/magento setup:upgrade` to see if Magento still works. Please note that these steps are generic developer steps, not related to this repository.

    rm -r generated/
    bin/magento setup:di:compile
    bin/magento setup:upgrade
 
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
