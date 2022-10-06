These are the release notes for the [Diff library](README.md).

Latest release:
[![Latest Stable Version](https://poser.pugx.org/diff/diff/version.png)](https://packagist.org/packages/diff/diff)

## Version 3.3.1 (2022-10-06)

* Made our __unserialize declarations match PHP 7's, to avoid PHP warnings

## Version 3.3 (2022-10-05)

* Raised minimum PHP version from 7.0 to 7.2
* Added testing with PHP 7.3, 7.4, 8.0 and 8.1

## Version 3.2 (2018-09-11)

* Deprecated constant `Diff_VERSION`
* Switched License from GPL-2.0-or-later to BSD-3-Clause

## Version 3.1 (2018-04-17)

* Fixed bug in `ListPatcher` that caused it to compare objects by identity rather than by value
* Add `.gitattributes` file to exclude not needed files from git exports
* Removed MediaWiki extension registration

## Version 3.0 (2017-05-10)

#### Improvements

* Added return type hints where possible
* Added scalar type hints where possible
* Added strict_types declare statements to all files 

#### Breaking changes

* Dropped support for PHP 5.x
* Dropped class aliases deprecated since Diff 1.0
* Removed `ListDiff` and `MapDiff`, deprecated since Diff 0.5
* Removed `ListDiffer::MODE_NATIVE` and `ListDiffer::MODE_STRICT`, deprecated since Diff 0.8
* Removed `MapDiffer::setComparisonCallback` in favour of a new constructor argument

## Version 2.3 (2018-04-11)

* Fixed bug in `ListPatcher` that caused it to compare objects by identity rather than by value

## Version 2.2 (2017-08-09)

* Removed MediaWiki extension registration
* Add `.gitattributes` file to exclude not needed files from git exports

## Version 2.1 (2016-09-01)

* Improved various PHPDocs

## Version 2.0 (2015-03-17)

* Added `Diff::equals`
* Removed unused `Diff\Appendable` interface
* Removed `Diff.credits.php`
* Changed visibility of most protected fields and methods to private

#### Internal changes

* `bootstrap.php` no longer runs `composer update`
* Added PHPCS and PHPMD support and configuration (`phpcs.xml` and `phpmd.xml`)
* Added `composer cs` command for running the code style checks
* CI now runs `composer ci` (includes code style checks) instead of `phpunit`

## Version 1.0.1 (2014-05-07)

* Removed not needed support for the MediaWiki i18n system
* Updated the url in `Diff.credits.php` (used on Special:Version when included with MediaWiki)

## Version 1.0 (2014-04-10)

#### Improvements

* Diff is now PSR-4 compliant

#### Breaking changes

* Removed the `Diff\IDiff` interface (deprecated since 0.5)
* Removed the `Diff\IDiffOp` interface (deprecated since 0.4)
* Replaced custom autoloader with PSR-4 based loading via Composer

#### Deprecations

* The classes that got moved into other namespace now have their old names as deprecated aliases:
	* All Differ classes that resided directly in the Diff namespace are now in Diff\Differ.
	* All DiffOp classes that resided directly in the Diff namespace are now in Diff\DiffOp.
	* All Patcher classes that resided directly in the Diff namespace are now in Diff\Patcher.

## Version 0.9 (2013-10-04)

#### Additions

* Added `OrderedArrayComparer`, an `ArrayComparer` for ordered arrays
* Added `OrderedListDiffer`, a Differ that acts as facade for a `ListDiffer` using an `OrderedArrayComparer`
* Added `ComparableComparer`, a `ValueComparer` that makes use of a "equals" method of the objects it compares

## Version 0.8 (2013-08-26)

#### Additions

* Added Diff\ArrayComparer\ArrayComparer interface
* Added NativeArrayComparer, ArrayComparer adapter for array_diff
* Added StrictArrayComparer, containing the "strict mode" logic from ListDiffer
* Added StrategicArrayComparer, implementation of ArrayComparer that takes a ValueComparer as strategy

#### Improvements

* MapPatcher will now report conflicts for remove operations that specify a value to be removed
different from the value in the structure being patched.
* ListDiffer now supports arbitrary array comparison behaviour by using an ArrayComparer strategy.
* The installation and usage documentation can now be found in README.md.

#### Removals

* Removed obsolete tests/phpunit.php test runner
* Removed obsolete INSTALL file. Installation instructions are now in README.md.

#### Deprecations

* The "comparison mode" flag in the ListDiffer constructor has been deprecated in favour of
  the ArrayComparer strategy it now has.

## Version 0.7 (2013-07-16)

#### Improvements

* Added extra tests for MapPatcher and ListPatcher
* Added extra tests for Diff
* Added extra tests for MapDiffer
* Added @covers tags to the unit tests to improve coverage report accuracy

#### Removals

* Removed static methods from ListDiff and MapDiff (all deprecated since 0.4)
* Removed DiffOpTestDummy

#### Bug fixes

* MapPatcher will now no longer stop patching after the first remove operation it encounters
* MapPatcher now always treats its top level input diff as a map diff
* Fixed several issues in ListPatcherTest

## Version 0.6 (2013-05-08)

#### Compatibility changes

* The tests can now run independently of MediaWiki
* The tests now require PHPUnit 3.7 or later

#### Additions

* Added phpunit.php runner in the tests directory
* Added Diff\Comparer\ValueComparer interface with CallbackComparer and StrictComparer implementations
* Added MapPatcher::setValueComparer to facilitate patching maps containing objects
* Added PHPUnit configuration file using the new tests/bootstrap.php

#### Removals

* GenericArrayObject has been removed from this package.
  Diff derives from ArrayObject rather than GenericArrayObject.
  Its interface has not changed expect for the points below.
* The getObjectType method in Diff (previously defined in GenericArrayObject)
  is now private rather than public.
* Adding a non-DiffOp element to a Diff will now result in an InvalidArgumentException
  rather than a MWException.
* Removed Diff\Exception

## Version 0.5 (2013-02-26)

#### Additions

* Added DiffOpFactory
* Added DiffOp::toArray
* Added CallbackListDiffer
* Added MapDiffer::setComparisonCallback

#### Deprecations

* Hard deprecated ListDiff, MapDiff and IDiff

#### Removals

* Removed Diff::getApplicableDiff

## Version 0.4 (2013-01-08)

#### Additions

* Split off diffing code from MapDiff and ListDiff to dedicated Differ classes
* Added dedicated Patcher classes, which are used for the getApplicableDiff functionality

#### Deprecations

* Deprecated ListDiff:newFromArrays and MapDiff::newFromArrays
* Deprecated ListDiff::newEmpty and MapDiff::newEmpty
* Deprecated Diff::getApplicableDiff
* Soft deprecated DiffOp interface in favour of DiffOp
* Soft deprecated IDiff interface in favour of Diff
* Soft deprecated MapDiff and ListDiff in favour of Diff

#### Removals

* Removed parentKey functionality from Diff
* Removed constructor from Diff interface
* Removed Diff::newEmpty

## Version 0.3 (2012-11-21)

* Improved entry point and setup code. Diff.php is now the main entry point for both MW extension and standalone library
* ListDiffs with only add operations can now be applied on top of bases that do not have their key
* Added Diff::removeEmptyOperations
* Improved type hinting
* Improved test coverage
    * Added constructor tests for MapDiff and ListDiff
    * Added extra tests for Diff and MapDiff
    * Test coverage is now 100%
* Removed static method from Diff interface

## Version 0.2 (2012-11-01)

* Fixed tests to work with PHP 5.4 and above
* Added translations
* Added some profiling calls

## Version 0.1 (2012-9-25)

Initial release with these features:

* Classes to represent diffs or collections of diff operations: Diff, MapDiff, ListDiff
* Classes to represent diff operations: Diff, MapDiff, ListDiff, DiffOpAdd, DiffOpChange, DiffOpRemove
* Methods to compute list and maps diffs
* Support for recursive diffs of arbitrary depth
* Works as MediaWiki extension or as standalone library
