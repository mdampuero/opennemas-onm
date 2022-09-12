themes = aletes amon anemoi auris base basic bastet \
eurus forseti freya horus kalliope kibele lrinternacional \
mihos moura nemo nemty notus nyx odin olympus pekka rhea \
skanda slido tecnofisis televisionlr trasno verbeia

www-data = $(shell id -u www-data > /dev/null 2>&1 && echo 'www-data' || echo 'http')

dbhost = $(shell [ -f app/config/connections.yml ] && \
	        grep 'host:' app/config/connections.yml | head -n 1 | sed -e "s/.*host:\s\+//g" || \
		grep 'host:' app/config/parameters.yml | head -n 1 | sed -e "s/.*host:\s\+//g")

dbuser = $(shell [ -f app/config/connections.yml ] && \
		grep 'user:' app/config/connections.yml | head -n 1 | sed -e "s/.*user:\s\+//g" || \
		grep 'user:' app/config/parameters.yml | head -n 1 | sed -e "s/.*user:\s\+//g")

dbpass = $(shell [ -f app/config/connections.yml ] && \
		grep 'password:' app/config/connections.yml | head -n 1 | sed -e "s/.*password:\s\+//g" || \
		grep 'password:' app/config/parameters.yml | head -n 1 | sed -e "s/.*password:\s\+//g")

ifndef $(branch)
    branch = $(shell git rev-parse --abbrev-ref HEAD)
endif

.PHONY: assets clean clean-database components database dev doc install \
node_modules prepare prod routes translations vendor

################################################################################
# Main targets
################################################################################

# Execute required targets to run opennemas
all: init prepare install build

# Generate files to include in build
build: assets translations

# Generate documentation
doc: jsdoc

# Initialize opennemas
init: database clean-database themes media

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
# Build targets
################################################################################

# Compile assets for admin and manager themes
assets:
	bin/console core:assets:compile

# Compile translations
translations:
	bin/console translation:core

################################################################################
# Documentation targets
################################################################################
jsdoc: node_modules
	node_modules/jsdoc/jsdoc.js \
		-c node_modules/openhost-jsdoc/conf.json \
		-t node_modules/openhost-jsdoc/template \
		-d build/docs/javascript \
		-r public/assets/src \
		-r public/core/themes/admin \
		-r public/core/themes/manager

################################################################################
# Installation targets
################################################################################

# Install js/css dependencies
components: public/assets/package.json
	cd public/assets && ../../node_modules/.bin/yarn install --no-progress

# Create required databases to run opennemas
database:
	mysql -h $(dbhost) -u$(dbuser) -p$(dbpass) -e "show databases;" | \
		grep -q "onm-instances" && return 0 || \
		mysql -h $(dbhost) -u$(dbuser) -p$(dbpass) -e "create database \`onm-instances\`;" && \
		mysql -h $(dbhost) -u$(dbuser) -p$(dbpass) onm-instances < db/onm-instances.sql
	mysql -h $(dbhost) -u$(dbuser) -p$(dbpass) -e "show databases;" | \
		grep -q "^1$$" && return 0 || \
		mysql -h $(dbhost) -u$(dbuser) -p$(dbpass) -e "create database \`1\`;" && \
		mysql -h $(dbhost) -u$(dbuser) -p$(dbpass) 1 < db/instance-default.sql

clean-database:
	mysql -h $(dbhost) -u$(dbuser) -p$(dbpass) 1 -e "REPLACE INTO settings (name, value) \
		VALUES ('recaptcha', 'a:2:{s:10:\"public_key\";s:40:\"6LdWlgkUAAAAADzgu34FyZ-wBSB0xlCUc7UVFWGw\";s:11:\"private_key\";s:40:\"6LdWlgkUAAAAAOUnzzBwHNpPgTBIaLwfDjr6XaeQ\";}')"

# Dumps Symfony routes to make them available in javascript
routes:
	bin/console fos:js-routing:dump --target public/assets/js/routes.js

# Copies the media for the default instance
media:
	mkdir -p public/media

	[ -d public/media/opennemas ] || cp -r public/core/media/default public/media/opennemas

# Install node dependencies
node_modules: package.json
	npm install

# Install missing opennemas themes
themes:
	mkdir -p public/themes

	for theme in $(themes); do \
		[ -d public/themes/$$theme ] || git clone git@bitbucket.org:opennemas/onm-theme-$$theme.git public/themes/$$theme || exit 1; \
		git -C public/themes/$$theme checkout $(branch) || exit 1; \
		git -C public/themes/$$theme pull || exit 1; \
	done

# Install php dependencies
vendor:
	bin/composer.phar install --prefer-dist --no-progress


################################################################################
# Prepare targets
################################################################################
clean:
	rm -rf build
	rm -rf public/build/assets
	rm -rf node_modules
	rm -rf vendor
	rm -rf tmp/cache


################################################################################
# Runtime target
################################################################################

# Fix permissions for some folders
permissions:
	[ -d tmp/cache ] && chmod 775 tmp/cache
	[ -d tmp/cache ] && chown $(www-data):$(www-data) -R tmp/cache
	[ -d build ] && chmod 775 build
	[ -d build ] && chown $(www-data):$(www-data) -R build
	[ -d public/media ] && chmod 775 public/media
	[ -d public/media ] && chown $(www-data):$(www-data) -R public/media
	[ -d public/build/assets ] && chmod 775 public/build/assets
	[ -d public/build/assets ] && chown $(www-data):$(www-data) -R public/build/assets


################################################################################
# Tests targets
################################################################################
eslint: node_modules
	node_modules/.bin/eslint -c .eslintrc --format=checkstyle \
		public/assets/src \
		public/core/themes/admin \
		public/core/themes/manager -o build/logs/eslint.xml

phplint:
	find libs src -name *.php -print0 | xargs -0 -n1 -P0 php -l

phpunit:
	bin/phpunit -c phpunit.xml \
		--coverage-html build/coverage \
		--coverage-clover build/coverage/coverage.xml \
		--log-junit build/php-result.xml
