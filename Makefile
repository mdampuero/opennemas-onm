#!/usr/bin/make

LOCALE_FOLDER = './public/admin/locale/'

TPL_FOLDER = './public/admin/themes/default/tpl/'
CACHE_FOLDER = 'tmp/cache'
SESSIONS_FOLDERS = \
	tmp/sessions/backend \
	tmp/sessions/frontend

LINGUAS = \
	es_ES \
	gl_ES

DOC_FOLDERS = public/core \
	public/controllers \
	public/libs/Onm/ \
	public/libs/Panorama/Panorama/ \
	

all: l10n

l10n: extracttrans updatepofiles compiletranslations

extracttrans:
	@echo "Extracting translations";
	@tsmarty2c $(TPL_FOLDER) > $(LOCALE_FOLDER)'extracted_strings.c'
	@xgettext public/admin/controllers/**/* \
			  public/admin/include/menu.php public/core/*.php \
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

generate-doc:
	mkdir doc/html -p
	phpdoc --directory $(DOC_FOLDERS) --target doc/html

generate-apigen-doc:
	mkdir doc/apigen log/ -p
	apigen --config doc/apigen.conf
	rm -r log

clean: cleancache cleansessions cleanlogs

cleancache:
	@echo "Cleaning cache...";
	rm -rf $(CACHE_FOLDER)/*

cleansessions:
	@echo "Cleaning sessions..."
	rm tmp/sessions/backend/* -f
	rm tmp/sessions/frontend/* -f

cleanlogs:
	@echo "Cleaning logs..."
	rm tmp/logs/*.log -f
