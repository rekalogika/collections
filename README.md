# rekalogika/collections

Pragmatic, opinionated enhancements to Doctrine's Collections library. Improves
the use of Doctrine Collections in large datasets, and other common problems.

Full documentation: [rekalogika.dev/collections](https://rekalogika.dev/collections)

## Background

We work with huge datasets that are managed by Doctrine ORM, and have complex
business rules. These come with these challenges:

* With standard Doctrine, it seems it is too easy to introduce bugs that will
  accidentally load the entire dataset into memory and cause out-of-memory
  errors. And these sorts of errors will usually only show up in production, but
  never in the development environment.

* Iterating over large datasets with the correct method is difficult and
  cumbersome. You usually need to devise custom solutions for each use case.

* Counting the number of records is very slow. Sometimes we can do away with the
  count, sometimes it is a must, and we need to work around the problem.

Other, non-performance issues include:

* Doctrine's `Selectable` appears to be a prevalent source of abstraction leak.
  Coders tend to litter the codebase with internal-dependent `Criteria` objects,
  and updating the entity can potentially become a nightmare. No static analysis
  tool can currently detect this problem. In fact, some exacerbate the problem
  by assuming a `Collection` must also be a `Selectable`.

Previously, we created `rekalogika/doctrine-collections-decorator` to solve
these problems. However, it is still too cumbersome because we need to approach
the problem one at a time. We need a more comprehensive solution that applies
to most, if not all, of our use cases.

## Components

* Decorator classes that enhance any Doctrine Collections classes.
* Query-backed collections. Turns a `QueryBuilder` into a lazy-loading
  collection.
* An alternative implementation of the repository pattern that implements
  `Collection`.
* Modifications to `ArrayCollection` that does `matching()` against the private
  properties directly, to reproduce the same behavior of `PersistentCollection`.

## Features

* Safeguards against potential out-of-memory situations. Throws an exception
  before it hits harder-to-debug out-of-memory situation.
* Pluggable counting strategies. Work around long counting times using your own
  counting strategies.
* Full versions of the collection classes that offer full compatibility with the
  original Doctrine Collection. And the minimal flavors that only expose the
  safe methods.
* Built in keyset pagination using
  [`rekalogika/rekapager`](https://rekalogika.dev/rekapager) library. Iterate
  over collections of any size without loading them all into memory. And without
  having to create ad-hoc queries every time you need to achieve that.
* Keyset pagination can also be used to create pagination for user interfaces
  and API outputs.
* Encourages you to create expressive, higher-level methods to provide the same
  functionality as the `Selectable` interface, but without exposing the inner
  workings of the class.

## Documentation

[rekalogika.dev/collections](https://rekalogika.dev/collections)

## License

MIT

## Contributing

This library consists of multiple repositories split from a monorepo. Be sure to
submit issues and pull requests to the
[rekalogika/collections](https://github.com/rekalogika/collections) monorepo.