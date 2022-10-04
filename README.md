# Diff

[![Build Status](https://secure.travis-ci.org/wmde/Diff.png?branch=master)](http://travis-ci.org/wmde/Diff)
[![Code Coverage](https://scrutinizer-ci.com/g/wmde/Diff/badges/coverage.png?s=6ef6a74a92b7efc6e26470bb209293125f70731e)](https://scrutinizer-ci.com/g/wmde/Diff/)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/wmde/Diff/badges/quality-score.png?s=d75d876247594bb4088159574cedf7bd648b9db2)](https://scrutinizer-ci.com/g/wmde/Diff/)
[![Latest Stable Version](https://poser.pugx.org/diff/diff/version.png)](https://packagist.org/packages/diff/diff)
[![Download count](https://poser.pugx.org/diff/diff/d/total.png)](https://packagist.org/packages/diff/diff)

**Diff** is a small standalone PHP library for representing differences between data
structures, computing such differences, and applying them as patches. It is extremely
well tested and allows users to define their own comparison strategies.

Diff does not provide any support for computing or representing the differences
between unstructured data, ie text.

A full history of the different versions of Diff can be found in the [release notes](RELEASE-NOTES.md).

## Requirements

**Diff 3.x:**

* PHP 7.2 or later (tested with PHP 7.2 up to PHP 8.1)

**Diff 2.x:**

* PHP 5.3 or later (tested with PHP 5.3 up to PHP 7.1 and HHVM)

## Installation

To add this package as a local, per-project dependency to your project, simply add a
dependency on `diff/diff` to your project's [`composer.json`](https://getcomposer.org/) file.
Here is a minimal example of a `composer.json` file that just defines a dependency on
Diff 3.x:

```json
{
    "require": {
        "diff/diff": "~3.0"
    }
}
```

## High level structure

The Diff library can be subdivided into several components. The main components are:

* **DiffOp** Value objects that represent add, change, remove and composite operations.
* **Differ** Service objects to create a diff between two sets of data.
* **Patcher** Service objects to apply a diff as patch to a set of data.

There are two support components, which are nevertheless package public:

* **Comparer** Service objects for determining if two values are equal.
* **ArrayComparer** Service objects for computing the difference between to arrays.

## Usage

### Representing diffs

A diff consists out of diff operations. These can be atomic operations such as add,
change and remove. These can also be diffs themselves, when dealing with nested structures.
Hence the [composite pattern](https://en.wikipedia.org/wiki/Composite_pattern) is used.

Diff operations implement the **DiffOp** interface.

The available operations are:

* `DiffOpAdd` - addition of a value (newValue)
* `DiffOpChange` - modification of a value (oldValue, newValue)
* `DiffOpRemove` - removal of a value (oldValue)
* `Diff` - a collection of diff operations

These can all be found in [src/DiffOp](src/DiffOp).

The `Diff` class can be set to be either associative or non-associative. In case of the later, only
`DiffOpAdd` and `DiffOpRemove` are allowed in it.

### Diffing data

To compute the difference between two data structures, an instance of **Differ** is used.
The `Differ` interface has a single method.

```php
/**
 * Takes two arrays, computes the diff, and returns this diff as an array of DiffOp.
 *
 * @param array $oldValues The first array
 * @param array $newValues The second array
 *
 * @throws Exception
 * @return DiffOp[]
 */
public function doDiff( array $oldValues, array $newValues ): array;
```

Implementations provided by Diff:

* `ListDiffer`: Differ that only looks at the values of the arrays (and thus ignores key differences).
* `MapDiffer`: Differ that does an associative diff between two arrays, with the option to do this recursively.
* `CallbackListDiffer`: Differ that only looks at the values of the arrays and compares them with a callback.
* `OrderedListDiffer`: Differ that looks at the order of the values and the values of the arrays.

All differ functionality can be found in [src/Differ](src/Differ).

### Applying patches

To apply a diff as a patch onto a data structure, an instance of **Patcher** is used.
The `Patcher` interface has a single method.

```php
/**
 * Applies the applicable operations from the provided diff to
 * the provided base value.
 *
 * @param array $base
 * @param Diff $diffOps
 *
 * @return array
 */
public function patch( array $base, Diff $diffOps ): array;
```

Implementations provided by Diff:

* `ListPatcher`: Applies non-associative diffs to a base. With default options does the reverse of `ListDiffer`
* `MapPatcher`: Applies diff to a base, recursively if needed. With default options does the reverse of `MapDiffer`

All classes part of the patcher component can be found in [src/Patcher](src/Patcher)

### ValueComparer

The `ValueComparer` interface contains one method:

```php
/**
 * @param mixed $firstValue
 * @param mixed $secondValue
 *
 * @return bool
 */
public function valuesAreEqual( $firstValue, $secondValue ): bool;
```

Implementations provided by Diff:

* `StrictComparer`: Value comparer that uses PHPs native strict equality check (ie ===).
* `CallbackComparer`: Adapter around a comparison callback that implements the `ValueComparer` interface.
* `ComparableComparer`: Value comparer for objects that provide an equals method taking a single argument.

All classes part of the ValueComparer component can be found in [src/Comparer](src/Comparer)

### ArrayComparer

The `ArrayComposer` interface contains one method:

```php
/**
 * Returns an array containing all the entries from arrayOne that are not present
 * in arrayTwo.
 *
 * Implementations are allowed to hold quantity into account or to disregard it.
 *
 * @param array $firstArray
 * @param array $secondArray
 *
 * @return array
 */
public function diffArrays( array $firstArray, array $secondArray ): array;
```

Implementations provided by Diff:

* `NativeArrayComparer`: Adapter for PHPs native array_diff method.
* `StrategicArrayComparer`: Computes the difference between two arrays by comparing elements with a `ValueComparer`.
* `StrictArrayComparer`: Does strict comparison of values and holds quantity into account.
* `OrderedArrayComparer`: Computes the difference between two ordered arrays by comparing elements with a `ValueComparer`.

All classes part of the ArrayComparer component can be found in [src/ArrayComparer](src/ArrayComparer)

## Examples

### Manually constructing a diff

```php
$diff = new Diff( array(
	'email' => new DiffOpAdd( 'nyan@c.at' ),
	'awesome' => new DiffOpChange( 42, 9001 ),
) );
```

### Computing a diff

```php
$oldVersion = array(
	'awesome' => 42,
);

$newVersion = array(
	'email' => 'nyan@c.at',
	'awesome' => 9001,
);

$differ = new MapDiffer();
$diff = $differ->doDiff( $oldVersion, $newVersion );
```

### Applying a diff as patch

```php
$oldVersion = array(
	/* ... */
);

$diff = new Diff( /* ... */ );

$patcher = new MapPatcher();
$newVersion = $patcher->patch( $oldVersion, $diff );
```

## Links

* [Diff on Packagist](https://packagist.org/packages/diff/diff)
* [Diff on OpenHub](https://www.openhub.net/p/phpdiff)
* [Diff on TravisCI](https://travis-ci.org/wmde/Diff)
* [Diff on ScrutinizerCI](https://scrutinizer-ci.com/g/wmde/Diff/)
