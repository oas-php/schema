start-docker:
	docker run -e PHP_IDE_CONFIG="serverName=docker" --rm -it -v `pwd`/../utils/src:/app/vendor/oas-php/utils/src -v `pwd`/.bash_history:/root -v `pwd`:/app -w /app biera/php:8.1 /bin/bash

test:
	vendor/bin/phpunit --color tests

test-with-coverage:
	XDEBUG_MODE=coverage vendor/bin/phpunit --color --coverage-html tmp tests