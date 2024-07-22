# rekalogika/collections-domain

Transforms a Doctrine `Collection` object into our `Recollection` object, which
extends `Collection` itself but also extends `PageableInterface` from our
`rekalogika/rekapager` library.

The features include:

* Safeguards against potential out-of-memory situations.
* Pluggable counting strategies.
* Keyset pagination for batch processing and user interfaces.

The classes also available in the minimal flavor, which only exposes the safe
methods, those which won't trigger full load of an extra-lazy collection.

## Documentation

[rekalogika.dev/collections](https://rekalogika.dev/collections)

## License

MIT

## Contributing

This library consists of multiple repositories split from a monorepo. Be sure to
submit issues and pull requests to the
[rekalogika/collections](https://github.com/rekalogika/collections) monorepo.