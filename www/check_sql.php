<?php
// don't execute 
exit(0);

# $db is the connection object
function &CountExecs($db, $sql, $inputarray) {
	global $EXECS;
	
    /*$firephp = FirePHP::getInstance(true);
    $firephp->log($sql, 'Consulta');*/
    
	if (!is_array(inputarray)) $EXECS++;
	# handle 2-dimensional input arrays
	else if (is_array(reset($inputarray))) $EXECS += sizeof($inputarray);
	else $EXECS++;
	
	$null = null;
	return $null;
}

# $db is the connection object
function CountCachedExecs($db, $secs2cache, $sql, $inputarray) {
	global $CACHED;
    $CACHED++;
}

$DEBUGSQL=1;

include_once 'index.php';

/***************************  DEBUG  *******************************************/
//$perf = NewPerfMonitor($GLOBALS['application']->conn);
//$perf->UI();
//require_once('FirePHPCore/FirePHP.class.php');

# After many sql statements:
