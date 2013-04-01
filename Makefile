help:
	@echo "Commands:"
	@echo " - make test"
	@echo " - make update"

test:
	@vendor/bin/phpunit

update:
	@php composer.phar update
