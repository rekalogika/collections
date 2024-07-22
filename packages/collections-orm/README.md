# rekalogika/collections-orm

A collection class using Doctrine ORM `QueryBuilder` as the data source. Unlike
doing the query in the traditional way, this class allows lazy loading. You can
safely pass the object around, and it will only execute the query when you start
getting items from it.

The class also implements the `PageableInterface` from the
[`rekalogika/rekapager`](https://rekalogika.dev/rekapager) library. This allows
you to iterate over the collection without loading all the items into memory.
And also useful for creating paginated user interfaces and API outputs.

## Documentation

[rekalogika.dev/collections](https://rekalogika.dev/collections)

## License

MIT

## Contributing

This library consists of multiple repositories split from a monorepo. Be sure to
submit issues and pull requests to the
[rekalogika/collections](https://github.com/rekalogika/collections) monorepo.