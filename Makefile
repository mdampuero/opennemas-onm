#!/usr/bin/make

TPL_FOLDER = \
	public/admin/themes/default/tpl/ \
	public/manager/themes/default/tpl

CACHE_FOLDER = 'tmp/cache'

SESSIONS_FOLDERS = \
	tmp/sessions/backend \
	tmp/sessions/frontend

LINGUAS = \
	es_ES \
	gl_ES \
	pt_BR

all: l10n

l10n: extracttrans updatepofiles compiletranslations extracttrans-backend updatepofiles-backend compiletranslations-backend

extracttrans-backend:
	@echo "Extracting backend translations";
	@tsmarty2c $(TPL_FOLDER) > app/Backend/Resources/locale/extracted_strings.c
	@xgettext app/Backend/Controllers/* \
	        app/Backend/Resources/Menu.php vendor/core/*.php \
        	vendor/Onm/**/**/*.php \
	        app/models/*.php \
        	public/admin/themes/default/**/*.php \
		    app/Backend/Resources/locale/extracted_strings.c \
		  -o app/Backend/Resources/locale/onmadmin.pot --from-code=UTF-8

updatepofiles-backend:
	@echo "Updating backend translations";
	@for i in $(LINGUAS); do \
		echo " - $$i";	\
		msgmerge -U "app/Backend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
			'app/Backend/Resources/locale/onmadmin.pot'; \
	done

compiletranslations-backend:
	@echo "Compiling backend translations";
	@for i in $(LINGUAS); do \
		echo " - $$i: " && \
		msgfmt -vf "app/Backend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
			-o "app/Backend/Resources/locale/$$i/LC_MESSAGES/messages.mo"; \
	done

extracttrans:
	@echo "Extracting translations";
	@xgettext public/controllers/* \
		  -o app/Frontend/Resources/locale/onmfront.pot --from-code=UTF-8

updatepofiles:
	@echo "Updating translations";
	@for i in $(LINGUAS); do \
		echo " - $$i";	\
		msgmerge -U "app/Frontend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
			'app/Frontend/Resources/locale/onmfront.pot'; \
	done

compiletranslations:
	@echo "Compiling translations";
	@for i in $(LINGUAS); do \
		echo " - $$i: " && \
		msgfmt -vf "app/Frontend/Resources/locale/$$i/LC_MESSAGES/messages.po" \
			-o "app/Frontend/Resources/locale/$$i/LC_MESSAGES/messages.mo"; \
	done

clean: cleancache cleaninstancefiles cleanlogs

cleancache:
	@echo "Cleaning cache...";
	rm -rf $(CACHE_FOLDER)/*

cleaninstancefiles:
	@echo "Cleaning temporal instance files..."
	rm tmp/instances/* -rf

cleanlogs:
	@echo "Cleaning logs..."
	rm tmp/logs/*.log -f

cleansmarty:
	@echo "Cleaning smarty remporary files..."
	rm tmp/instances/*/smarty/ -r
