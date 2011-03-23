<?php
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');

require_once(SITE_CORE_PATH.'privileges_check.class.php');
if( !Privileges_check::CheckPrivileges('NOT_ADMIN')) {
    Privileges_check::AccessDeniedAction();
}


$tpl = new TemplateAdmin(TEMPLATE_ADMIN);
$tpl->assign('titulo_barra', 'Update system');

function test_url($url) {

    $addr=parse_url($url);
    $host=$addr['host'];
    $path = $addr['path'];

    $headtxt ='';

    try {
		if($sock=@fsockopen($host,80, $errno, $errstr, 3))
		{
			fputs($sock, "HEAD $path HTTP/1.0\r\nHost: $host\r\n\r\n");
			while(!feof($sock)) $headtxt .= fgets($sock);
		}
	}catch(Exception $e){
		$headtxt = '';
	}

    return (stripos($headtxt, "200 OK") || stripos($headtxt, "401 Authorization Required") === false) ? false:true ;
}

$action = filter_input ( INPUT_POST, 'action' , FILTER_SANITIZE_STRING );
if(!isset($action)) {
	$action = filter_input ( INPUT_GET, 'action' , FILTER_SANITIZE_STRING, array( 'options' => array('default' =>  'status' )) );

}
if(isset($action) ) {

    $username = filter_input ( INPUT_POST, 'scm_username' , FILTER_SANITIZE_STRING, array( 'options' => array('default' => $_SESSION['username'])) );
    $password = filter_input ( INPUT_POST, 'scm_password' , FILTER_SANITIZE_STRING );
    $repository = filter_input ( INPUT_POST, 'repository' , FILTER_SANITIZE_STRING, array( 'options' => array('default' => REPOSITORY_URL)) );
    $destination = realpath(SITE_PATH.'../'). '/';
    $checkout = '';
	$result='';

    switch($action) {
		case 'co':
			//$commandToPerform = "svn --work-tree={$destination} --git-dir={$destination}/.git pull --rebase";
		break;
	
		case 'status':
			$commandToPerform = "git --work-tree={$destination} --git-dir={$destination}/.git status";
		break;
		case 'update':
			$commandToPerform = "git pull --rebase";
		break;
	
		case 'info':
			$commandToPerform = 'svn info  --username '.$username.' --password '.$password.' '.$repository;
		break;
	
		case 'list':
			$commandToPerform = "git --work-tree=\"{$destination}/\" --git-dir=\"{$destination}.git/\" ls-files";
		break;
	
		default:
			Application::forward($_SERVER['PHP_SELF']);
		break;
	}


    if (isset($commandToPerform)) {
	    //if (test_url($repository) === false) $tpl->assign('return', "svn-server-error");
		//else {
		chdir($destination);
	    exec($commandToPerform, $return);
        $tpl->assign('return', $return);
		$tpl->assign('checkout', $commandToPerform);

		//}
	}
    $tpl->assign('username', $username);
    $tpl->assign('password', $password);
    $tpl->assign('repository', $repository);
    $tpl->assign('destination', $destination);
    $tpl->assign('action', $action);

}

$tpl->display('updatesystem/index.tpl');
