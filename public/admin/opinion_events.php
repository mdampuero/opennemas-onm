<?php
// Application events
if (preg_match('%opinion_events.php%', $_SERVER['PHP_SELF'])) {
	die();
}

/*
 clearCacheContent, callback registered into Content
*/
$GLOBALS['application']->register('onAfterUpdateOpinion', 'onUpdateClearCacheOpinion');

