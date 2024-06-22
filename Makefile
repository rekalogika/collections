PHP=php
COMPOSER=composer

.PHONY: test
test: composer-dump phpstan psalm phpunit

.PHONY: composer-dump
composer-dump:
	$(COMPOSER) dump-autoload

.PHONY: phpstan
phpstan:
	$(PHP) vendor/bin/phpstan analyse

.PHONY: psalm
psalm:
	$(PHP) vendor/bin/psalm

.PHONY: phpunit
phpunit:
	$(eval c ?=)
	$(PHP) vendor/bin/phpunit $(c)

.PHONY: php-cs-fixer
php-cs-fixer: tools/php-cs-fixer
	$(PHP) $< fix --config=.php-cs-fixer.dist.php --verbose --allow-risky=yes

.PHONY: tools/php-cs-fixer
tools/php-cs-fixer:
	phive install php-cs-fixer

.PHONY: clean
clean:
	$(PHP) vendor/bin/psalm --clear-cache
	rm -rf tests/var/*

.PHONY: merge
monorepo-merge:
	$(PHP) vendor/bin/monorepo-builder merge

.PHONY:
fixtures: tests/var/data.db

tests/var/data.db:
	sqlite3 $@ < tests/etc/data.sql

.PHONY: fixtures-init
fixtures-init:
	$(PHP) tests/bin/console doctrine:schema:create
	$(PHP) tests/bin/console doctrine:fixtures:load --no-interaction

.PHONY: dump
dump:
	$(PHP) tests/bin/console server:dump