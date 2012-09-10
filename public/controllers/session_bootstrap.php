<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
$GLOBALS['Session'] = SessionManager::getInstance(OPENNEMAS_FRONTEND_SESSIONS);
$GLOBALS['Session']->bootstrap();

