start-docker:
	docker run --rm -it \
		-e PHP_IDE_CONFIG="serverName=docker" \
		-v `pwd`:/app -w /app \
		-v `pwd`/tmp/.bash_history:/root/.bash_history \
		-v `pwd`/../utils/src:/app/vendor/oas-php/utils/src \
		-v `pwd`/../resolver/src:/app/vendor/oas-php/resolver/src \
		biera/php:8.1 /bin/bash

test:
	vendor/bin/phpunit --color tests

test-with-coverage:
	XDEBUG_MODE=coverage vendor/bin/phpunit --color --coverage-html tmp/coverage tests