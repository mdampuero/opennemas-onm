themes = amon anemoi auris base basic bastet bragi cronicas dryads eurus \
forseti freya galatea hathor hermes horus kalliope kibele kronos \
lrinternacional mega mercury mihos moura nemo nemty notus nyx odin \
olympus pekka rhea selket simplo skanda slido tecnofisis televisionlr \
verbeia xaman zisa

.PHONY: clean components dev install prepare prod routes vendor

################################################################################
# Main targets
################################################################################

# Execute required targets to run opennemas
all: init prepare install

# Generate documentation
doc: jsdoc

# Initialize opennemas
init: database themes

# Install all required dependencies to run opennemas
install: vendor node_modules components routes

# Prepare directory for build
prepare: clean

test: phplint eslint phpunit

################################################################################
# Application modes
################################################################################
dev:
	touch .development
	rm -rf tmp/cache/dev/*

prod:
	test -f .development && rm .development || true
	rm -rf tmp/cache/prod/*


################################################################################
# Documentation targets
################################################################################
jsdoc: build/docs/javascript

build/docs/javascript:
	node_modules/jsdoc/jsdoc.js \
		-c node_modules/openhost-jsdoc/conf.json \
		-t node_modules/openhost-jsdoc/template \
		-d build/docs/javascript \
		-r public/assets/src \
		-r public/themes/admin \
		-r public/themes/manager

################################################################################
# Installation targets
################################################################################

# Alias to easy install js/css dependencies
components: public/assets/components

# Create required databases to run opennemas
database:
	mysql -h mysql -uroot -proot -e "show databases;" | \
		grep -q "onm-instances" && return 0 || \
		mysql -h mysql -uroot -proot -e "create database \`onm-instances\`;" && \
		mysql -h mysql -uroot -proot onm-instances < db/onm-instances.sql
	mysql -h mysql -uroot -proot -e "show databases;" | \
		grep -q "^1$$" && return 0 || \
		mysql -h mysql -uroot -proot -e "create database \`1\`;" && \
		mysql -h mysql -uroot -proot 1 < db/instance-default.sql
	mysql -h mysql -uroot -proot c-default -e "REPLACE INTO settings (name, value) \
		VALUES ('recaptcha', 'a:2:{s:10:\"public_key\";s:40:\"6LdWlgkUAAAAADzgu34FyZ-wBSB0xlCUc7UVFWGw\";s:11:\"private_key\";s:40:\"6LdWlgkUAAAAAOUnzzBwHNpPgTBIaLwfDjr6XaeQ\";}')"

# Alias to easy dump Symfony routes
routes: public/assets/js/routes.js

# Install node dependencies
node_modules: package.json
	npm install

# Install missing opennemas themes
themes:
	for theme in $(themes); do \
		[ -d public/themes/$$theme ] && continue \
			|| git clone git@bitbucket.org:opennemas/onm-theme-$$theme.git public/themes/$$theme; \
	done

# Install php dependencies
vendor:
	bin/composer.phar install --prefer-dist --no-progress

# Install js/css dependencies
public/assets/components: public/assets/package.json
	cd public/assets && ../../node_modules/.bin/yarn install

# Dumps Symfony routes to make them available in javascript
public/assets/js/routes.js:
	bin/console fos:js-routing:dump --target public/assets/js/routes.js


################################################################################
# Prepare targets
################################################################################
clean:
	rm -rf build && mkdir build
	rm -rf tmp/cache && mkdir tmp/cache

################################################################################
# Tests targets
################################################################################
eslint: node_modules
	node_modules/.bin/eslint -c .eslintrc --format=checkstyle \
		public/assets/src \
		public/themes/admin \
		public/themes/manager -o build/logs/eslint.xml

phplint:
	find libs src -name *.php -print0 | xargs -0 -n1 -P0 php -l

phpunit:
	bin/phpunit -c phpunit.xml \
		--coverage-html build/coverage \
		--coverage-clover build/report/php-coverage.xml \
		--log-junit build/report/php-result.xml
