<?php
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

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

if(isset($_REQUEST['action']) ) {

    $username = $_REQUEST['svn_username'];
    $password = $_REQUEST['svn_password'];
    $repository = $_REQUEST['repository'];
    $destination = $_REQUEST['destination'];
    $action = $_REQUEST['action'];
    $checkout = '';$result='';

    switch($_REQUEST['action']) {
	case 'co':
        $checkout = 'svn co --username '.$username.' --password '.$password.' '.$repository.' '.$destination;
	break;
    case 'status':
        $checkout = 'svn status '.$destination;
	break;
    case 'update':
        $checkout = 'svn update --username '.$username.' --password '.$password.' '.$destination.'/*';
	break;

	case 'info':
        $checkout = 'svn info  --username '.$username.' --password '.$password.' '.$repository;

	break;

    case 'list':
        $checkout = 'svn list  --username '.$username.' --password '.$password.' '.$repository.' -v';
	break;

	default:
        Application::forward('svn.php');
	break;
	}

    $tpl->assign('checkout', $checkout);
    $tpl->assign('username', $username);
    $tpl->assign('password', $password);
    $tpl->assign('repository', $repository);
    $tpl->assign('destination', $destination);
    $tpl->assign('action', $action);


    if (test_url($repository) === false) $tpl->assign('return', "svn-server-error");
    else
    {
        exec($checkout, $return);
        $tpl->assign('return', $return);
        exec("chown www-data:www-data $destination/* -R ", $return);
        exec("find ./ -type d -name .svn -exec chmod 777 {}/ -R \;");
        exec("find ./ -type d -name .svn -exec chown www-data:www-data {}/ -R \;");
        exec("chmod 777 $destination/* -R", $return);

        exec($checkout, $return);
        $tpl->assign('return', $return);
    }

} else {
        $username = $_SESSION['username'];
        $password = "XXXXXXXXX";
        $repository = "http://svn.openhost.es/opennemasdemo/trunk/";
        $destination = "/home/opennemas/retrincos/code/";
        $checkout = "svn info --username $username --password $password $repository";

		exec($checkout, $return);

        $tpl->assign('checkout', $checkout);
        $tpl->assign('return', $return);
        $tpl->assign('username', $username);
        $tpl->assign('password', $password);
        $tpl->assign('repository', $repository);
        $tpl->assign('destination', $destination);
        $tpl->assign('action', "info");
}

$tpl->display('svn.tpl');
?>
