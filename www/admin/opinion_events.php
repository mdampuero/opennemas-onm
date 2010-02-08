<?php
// Application events
if (eregi('opinion_events.php', $_SERVER['PHP_SELF'])) {
	die();
}

/*
 clearCacheContent, callback registered into Content
*/
$GLOBALS['application']->register('onAfterUpdateOpinion', 'onUpdateClearCacheOpinion');

