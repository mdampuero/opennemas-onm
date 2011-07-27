#!/usr/bin/make

LOCALE_FOLDER = './public/admin/locale/'

TPL_FOLDER = './public/admin/themes/default/tpl/'
CACHE_FOLDER = './tmp/cache'
SESSIONS_FOLDERS = \
	tmp/sessions/backend \
	tmp/sessions/frontend

LINGUAS = \
	es_ES \
	gl_ES

all: default updatepofiles compiletranslations

default:
	@echo "Extracting translations";
	@tsmarty2c $(TPL_FOLDER) > $(LOCALE_FOLDER)'extracted_strings.c'
	@xgettext public/admin/include/menu.php public/core/*.php \
			  public/libs/Onm/**/**/*.php **/**/*.php \
			  public/admin/themes/default/**/*.php \
			  $(LOCALE_FOLDER)'extracted_strings.c' \
			  -o $(LOCALE_FOLDER)'onmadmin.pot' --from-code=UTF-8

updatepofiles:
	@echo "Updating translations";
	@for i in $(LINGUAS); do \
		echo " - $$i";	\
		msgmerge -U "public/admin/locale/$$i/LC_MESSAGES/messages.po" \
			'public/admin/locale/onmadmin.pot'; \
	done

compiletranslations:
	@echo "Compiling translations";
	@for i in $(LINGUAS); do \
		echo " - $$i: " && \
		msgfmt -vf "public/admin/locale/$$i/LC_MESSAGES/messages.po" \
			-o "public/admin/locale/$$i/LC_MESSAGES/messages.mo"; \
	done

clean: cleancache cleansessions

cleancache:
	@echo "Cleaning cache...";
	rm -r $(CACHE_FOLDER)'/*'

cleansessions:
	@echo "Cleaning sessions..."
	@for i in $(SESSIONS_FOLDERS); do \
		echo " - $$i: " && \
		rm "$$i/*" \
	done
