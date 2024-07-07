# Changelog

## 0.8.1

* feat: add `ArrayCollection` that does `matching()` calls against the private
  properties, not indirectly through the getters.

## 0.8.0

* feat: `$key` parameter type widening, to accommodate object primary keys, like
  UUIDs.
* chore: rename `FindFetchTrait` to `FetchTrait`
* chore: add `Override` attribute where applicable
* chore: cleanup unused code
* refactor: rename `QueryCollection` to `QueryRecollection`
* feat: default count limits
* refactor: remove `Countable` from minimal classes
* refactor: rename `RestrictedCountStrategy` to `DisabledCountStrategy`
* feat: `$offset` parameter type widening for `ArrayAccess` methods
* fix(`DefaultKeyTransformer`): only convert `Stringable` to string for now.
* feat: add `UuidKeyTransformer`
* refactor(`Repository`): rename DX methods

## 0.7.0

* refactor: remove `RefreshableCount` interface
* refactor: `MinimalReadableRecollection` now extends `Countable`
* refactor: cleanup count traits
* refactor: refactor counting, change default strategy to
  `ConditionalDelegatedCountStrategy`
* refactor: add `PageableRecollection`

## 0.6.2

* fix: add `instanceId` to `createCriteriaPageable`

## 0.6.1

* feat: add `createCriteria()` convenience method

## 0.6.0

* feat: add `find()` and `fetch()` methods, remove `getOrFail()`

## 0.5.2

* fix: fix various problems with criteria recollections
* feat: add several DX methods
* feat: add `createCriteriaPageable` DX method

## 0.5.1

* refactor: refactor counting strategy
* fix: use closure for counting, defer counting until it is asked
* refactor: refresh count

## 0.4.0

* refactor: reorganize exceptions
* feat: add `fetch()`
* feat: add `QueryPageable`
* feat: repository interfaces
* refactor: rename `createFrom()` to `with()`
* refactor: rename the term 'safe' to 'large'
* refactor(`RecollectionDecorator`): rename `withCriteria` to `applyCriteria`
* refactor: rename the term 'large' to 'basic'
* cleanup: Basic classes do not require soft and hard limits
* cleanup: orderBy should be non-empty-array
* feat: initial version of basic repository
* refactor: consolidate repeated orderBy logic to `OrderByUtil`
* refactor: use `configure()` method for subclasses to configure the repository
* test: rearrange test directories
* feat: add 404 status code to `NotFoundException`
* test: add skeleton tests using Symfony framework
* fix(`BasicRepository`): `remove()` should be `removeElement()`
* feat(`BasicRepository`): add `remove()` method
* test: add basic repository test
* refactor(`AbstractBasicRepository`): change configuration method
* test: add phpunit and PSR support in static analyses
* deps: update rekapager to 0.12.0
* deps: add debug bundle
* refactor: move `refreshCount` to its own interface
* refactor: rename `getReference` to `reference`
* refactor: rename the term 'basic' to 'minimal'
* test: refactor tests
* fix: make sure all objects support indexBy
* feat: instance caching for collection decorators
* fix: should be static instead of self for instantiation in decorators
* feat: instance caching for criteria recollections
* test: criteria recollection
* refactor: rename basic repository to minimal repository
* fix: repository slice now use QueryBuilder setMaxResults and setFirstResult
* test: reorganize

## 0.3.0

* feat: add `SafePageableCollection` and `SafeReadablePageableCollection`
* refactor: rename interface & classes
* fix: add covariance to applicable interfaces
* feat: detect extra lazy collections
* deps: update rekapager to 0.11.2
* build: fix CI on lowest deps
* fix: disable extra lazy detection for now
* chore: remove strict in favor of safe classes
