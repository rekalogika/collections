PHP=php
COMPOSER=composer

.PHONY: test
test: composer-dump phpstan phpunit

.PHONY: composer-dump
composer-dump:
	$(COMPOSER) dump-autoload --optimize

.PHONY: phpstan
phpstan:
	$(PHP) vendor/bin/phpstan analyse

.PHONY: phpunit
phpunit:
	$(eval c ?=)
	$(PHP) vendor/bin/phpunit $(c)

.PHONY: php-cs-fixer
php-cs-fixer: tools/php-cs-fixer
	PHP_CS_FIXER_IGNORE_ENV=1 $(PHP) $< fix --config=.php-cs-fixer.dist.php --verbose --allow-risky=yes

.PHONY: tools/php-cs-fixer
tools/php-cs-fixer:
	phive install php-cs-fixer

.PHONY: rector
rector:
	$(PHP) vendor/bin/rector process > rector.log
	make php-cs-fixer

.PHONY: clean
clean:
	rm -rf tests/var/*

.PHONY: merge
monorepo-merge:
	$(PHP) vendor/bin/monorepo-builder merge

.PHONY: monorepo-release-%
monorepo-release-%:
	git update-index --really-refresh > /dev/null; git diff-index --quiet HEAD || (echo "Working directory is not clean, aborting" && exit 1)
	[ `git branch --show-current` == main ] || (echo "Not on main branch, aborting" && exit 1)
	$(PHP) vendor/bin/monorepo-builder release $*
	git switch -c release/$*
	git add .
	git commit -m "release: $*"

.PHONY:
fixtures: tests/var/data.db

tests/var/data.db:
	sqlite3 $@ < tests/etc/data.sql

.PHONY: fixtures-init
fixtures-dump:
	rm tests/var/data.db
	$(PHP) tests/bin/console doctrine:schema:create
	$(PHP) tests/bin/console doctrine:fixtures:load --no-interaction
	sqlite3 tests/var/data.db .dump > tests/etc/data.sql

.PHONY: dump
dump:
	$(PHP) tests/bin/console server:dump

clear-cache:
	$(PHP) vendor/bin/phpstan clear-result-cache
