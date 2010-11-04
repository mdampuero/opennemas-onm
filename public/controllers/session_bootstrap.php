<?php
require_once (SITE_CORE_PATH . 'sessionmanager.class.php');
$GLOBALS['Session'] = SessionManager::getInstance(OPENNEMAS_FRONTEND_SESSIONS);
$GLOBALS['Session']->bootstrap();
