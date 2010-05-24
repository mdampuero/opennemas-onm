#!/bin/bash
################################################################################
## Script postinstall to create directories to store sessions and log         ##
################################################################################

if [ ! "`whoami`" = "root" ]
then
    echo "Please run script as root."
    exit 1
fi

# Uncomment next line to create a custom installation
# APP_NAME="opennemas.com"
APP_NAME=
if [ "$APP_NAME" = "" ]; then
    echo "Application name?"
    read APP_NAME
fi
echo "Setup application name to $APP_NAME"


BASEDIR=/var/lib/opennemas
OPENNEMAS_BACKEND_SESSIONS=$BASEDIR/$APP_NAME/sessions/backend/
OPENNEMAS_FRONTEND_SESSIONS=$BASEDIR/$APP_NAME/sessions/frontend/
OPENNEMAS_LOG=$BASEDIR/$APP_NAME/log

# Check and create backend directory for sessions
if [ -d $OPENNEMAS_BACKEND_SESSIONS ]; then
    echo "$OPENNEMAS_BACKEND_SESSIONS already exists"
else
    mkdir -p $OPENNEMAS_BACKEND_SESSIONS 
fi

# Check and create frontend directory for sessions
if [ -d $OPENNEMAS_FRONTEND_SESSIONS ]; then
    echo "$OPENNEMAS_FRONTEND_SESSIONS already exists"
else
    mkdir -p $OPENNEMAS_FRONTEND_SESSIONS
fi

# Check and create log directory
if [ -d $OPENNEMAS_LOG ]; then
    echo "$OPENNEMAS_LOG already exists"
else
    mkdir -p $OPENNEMAS_LOG
fi


echo "Setting permissions and owner..."
chown -R www-data:www-data $BASEDIR/$APP_NAME
chmod -R 700 $BASEDIR/$APP_NAME

if [ -f /etc/cron.d/$APP_NAME ]; then
    echo "/etc/cron.d/$APP_NAME already exists"
    
else
    echo "Installing cron tasks..."
    touch /etc/cron.d/$APP_NAME
    
    echo "00,30 *     * * *     root   [ -x /usr/lib/php5/maxlifetime ] && [ -d $OPENNEMAS_BACKEND_SESSIONS ] && find $OPENNEMAS_BACKEND_SESSIONS -type f -cmin +$(/usr/lib/php5/maxlifetime) -print0 | xargs -r -0 rm" > /etc/cron.d/$APP_NAME
    echo "15 *     * * *     root   [ -x /usr/lib/php5/maxlifetime ] && [ -d $OPENNEMAS_FRONTEND_SESSIONS ] && find $OPENNEMAS_FRONTEND_SESSIONS -type f -cmin +$(/usr/lib/php5/maxlifetime) -print0 | xargs -r -0 rm" >> /etc/cron.d/$APP_NAME    
fi

