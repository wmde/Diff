# Diff

[![Latest Stable Version](https://poser.pugx.org/diff/diff/version.png)](https://packagist.org/packages/diff/diff)
[![Latest Stable Version](https://poser.pugx.org/diff/diff/d/total.png)](https://packagist.org/packages/diff/diff)
[![Build Status](https://secure.travis-ci.org/wikimedia/mediawiki-extensions-Diff.png?branch=master)](http://travis-ci.org/wikimedia/mediawiki-extensions-Diff)

Diff is a small PHP library with value objects to represent diffs and service objects to do
various types of operations. These include creating a diff between two data structures,
applying a diff onto a data structure and merging multiple diffs into one.

## Requirements

* PHP 5.3 or later
* [DataValues](https://www.mediawiki.org/wiki/Extension:DataValues) 0.1 or later
* [Serialization](https://github.com/wikimedia/mediawiki-extensions-Serialization/blob/master/README.md) 1.0 or later

## Installation

You can use [Composer](http://getcomposer.org/) to download and install
this package as well as its dependencies. Alternatively you can simply clone
the git repository and take care of loading yourself.

### Composer

To add this package as a local, per-project dependency to your project, simply add a
dependency on `diff/diff` to your project's `composer.json` file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Diff 1.0:

    {
        "require": {
            "diff/diff": "1.0.*"
        }
    }

### Manual

Get the Diff code, either via git, or some other means. Also get all dependencies.
You can find a list of the dependencies in the "require" section of the composer.json file.
Load all dependencies and the load the Diff library by including its entry point:
Diff.php.

## Usage

The [extension page on mediawiki.org](https://www.mediawiki.org/wiki/Extension:Diff)
contains the documentation and examples for this library.

## Links

* [Diff on Packagist](https://packagist.org/packages/diff/diff)
* [Diff on Ohloh](https://www.ohloh.net/p/mwdiff)
* [Diff on MediaWiki.org](https://www.mediawiki.org/wiki/Extension:Diff)
* [TravisCI build status](https://travis-ci.org/wikimedia/mediawiki-extensions-Diff)
* [Latest version of the readme file](https://github.com/wikimedia/mediawiki-extensions-Diff/blob/master/README.md)
