LOCALE_FOLDER = './locale/'

TPL_FOLDER = './themes/default/tpl/'

LINGUAS = \
	es_ES \
	gl_ES

all: default updatepofiles compiletranslations


default:
	@echo "Extracting translations";
	@tsmarty2c $(TPL_FOLDER) > $(LOCALE_FOLDER)'extracted_strings.c'
	@xgettext include/menu.php ../core/*.php ../libs/Onm/**/**/*.php **/**/*.php ./themes/default/**/*.php $(LOCALE_FOLDER)'extracted_strings.c' -o $(LOCALE_FOLDER)'onmadmin.pot' --from-code=UTF-8

updatepofiles:
	@echo "Updating translations";
	@for i in $(LINGUAS); do \
		echo " - $$i";	\
		msgmerge -U "./locale/$$i/LC_MESSAGES/messages.po" './locale/onmadmin.pot'; \
	done

compiletranslations:
	@echo "Compiling translations";
	@for i in $(LINGUAS); do \
		echo " - $$i: " && \
		msgfmt -vf "./locale/$$i/LC_MESSAGES/messages.po" -o "./locale/$$i/LC_MESSAGES/messages.mo"; \
	done
