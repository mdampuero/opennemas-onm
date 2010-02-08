<?php
require_once('core/sessionmanager.class.php');
$GLOBALS['Session'] = SessionManager::getInstance(OPENNEMAS_FRONTEND_SESSIONS);
$GLOBALS['Session']->bootstrap();

