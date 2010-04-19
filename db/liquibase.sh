#!/bin/bash
USERNAME=root
PASSWORD=root
DATABASE=opennemasdemodb

BASEPATH=`dirname "$PRG"`
BASEPATH=`cd "$BASEPATH" && pwd`

$BASEPATH/bin/liquibase --driver=com.mysql.jdbc.Driver --changeLogFile=changelog/db.changelog-master.xml --url="jdbc:mysql://localhost/$DATABASE" --username=$USERNAME --password=$PASSWORD $* 
