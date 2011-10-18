#!/bin/bash

# if service command is available use it
if [ -x /usr/sbin/service ]; then
	sudo service apache2 reload
else
	sudo /etc/init.d/apache2 reload
fi

# return last exit code
exit $?
