#!/usr/bin/make

LOCALE_FOLDER = './public/admin/locale/'

TPL_FOLDER = \
	public/admin/themes/default/tpl/ \
	public/manager/themes/default/tpl

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
        	vendor/Onm/**/**/*.php \
	        public/models/*.php \
        	public/manager/controllers/**/*.php \
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

doc: generate-phpdoc-doc generate-doxygen-doc generate-apigen-doc generate-docblox-doc

generate-phpdoc-doc:
	@echo "Generating documentation using PHP_Documentator..."
	phpdoc --directory $(DOC_FOLDERS) --target doc/phpdoc

generate-doxygen-doc:
	@echo "Generating documentation using Doxygen..."
	doxygen doc/doxygen.conf

generate-apigen-doc:
	@echo "Generating documentation using APIGen..."
	mkdir doc/apigen log/ -p
	apigen --config doc/apigen.conf
	rm -r log

generate-docblox-doc:
	@echo "Generating documentation using DocBlox..."
	mkdir -p doc/docblox/log
	docblox -c doc/docblox.xml --title="OpenNemas"

clean: cleancache cleaninstancefiles cleanlogs cleandocs

cleancache:
	@echo "Cleaning cache...";
	rm -rf $(CACHE_FOLDER)/*

cleaninstancefiles:
	@echo "Cleaning temporal instance files..."
	rm tmp/instances/* -rf

cleanlogs:
	@echo "Cleaning logs..."
	rm tmp/logs/*.log -f

cleandocs:
	@echo "Cleaning generated documentations..."
	rm doc/doxygen doc/phpdoc doc/apigen doc/docblox -r
