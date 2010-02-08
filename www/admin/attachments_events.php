<?php
if (eregi('attachments_events.php', $_SERVER['PHP_SELF'])) {
	die();
}
// Attachment events

/*
 clearCacheContent, callback registered into Content
*/


$GLOBALS['application']->register('onAfterCreateAttach', 'refreshHome');