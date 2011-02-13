<?php
/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');
$sessions = $GLOBALS['Session']->getSessions();

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

require_once(SITE_CORE_PATH.'privileges_check.class.php');

$RESOURCES_PATH = TEMPLATE_ADMIN_PATH_WEB;



if( Privileges_check::CheckPrivileges('USER_ADMIN') ) {
    // Peticiones por Ajax
    if( isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && ( $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest')) {
        $action = (isset($_REQUEST['action']))? $_REQUEST['action']: 'list';
        switch($action) {
            case 'purge':

                // If you try to logout one user, clean him session
                // and reload the session list
                if($_SESSION['userid']!=$_REQUEST['userid']) {
                    $GLOBALS['Session']->purgeSession( intval($_REQUEST['userid']) );
                    $sessions = $GLOBALS['Session']->getSessions();
                }

            case 'show_panel':
                $html = '<table width="90%" align="center"><tr></tr>';
$tpl_user =<<< TPLUSER
<tr>
    <td>%s</td>
    <td align="center">%s</td>
    <td align="center">
        <a href="user.php?action=read&id=%s" title="Editar usuario" onclick="Modalbox.hide();" target="centro">
            <img src="images/users_edit.png" border="0" /></a>
        <a href="index.php?action=purge&userid=%s" class="modal" title="Purgar sesión">
            <img src="{$RESOURCES_PATH}images/publish_r.png" border="0" /></a>
    </td>
    <td><img src="{$RESOURCES_PATH}images/iconos/%s.gif" border="0" alt="" title="%s" /></td>
</tr>
TPLUSER;

$tpl_admin =<<< TPLADMIN
<tr>
    <td>%s</td>
    <td align="center">%s</td>
    <td align="center">-</td>
    <td><img src="{$RESOURCES_PATH}images/iconos/%s.gif" border="0" alt="" title="%s" /></td>
</tr>
TPLADMIN;
                $authMethodTitles = array('database' => 'Logged in with OpenNemas', 'google_clientlogin' => 'Logged in from Google account');
                foreach($sessions as $session) {
                    $authMethod = (isset($session['authMethod']))? $session['authMethod']: 'database';
                    if(($session['userid']!=$_SESSION['userid']) && ($_SESSION['isAdmin'])) {
                        $html .= sprintf($tpl_user, $session['username'],  date(' H:i ', $session['expire']),
                                         $session['userid'], $session['userid'], $authMethod, $authMethodTitles[$authMethod]);
                    } else {
                        $html .= sprintf($tpl_admin, $session['username'], date(' H:i ', $session['expire']),
                                         $authMethod, $authMethodTitles[$authMethod]);
                    }
                }

                $html .= '</table>';
                echo( $html );
            break;

            case 'list':
            default:
                echo json_encode( $sessions );
            break;
        }

        exit(0); // Finalizar la petición por Ajax
    }

// Not authenticated user admin
}

//Para crear noticia que vuelva a listado de pendientes.
$_SESSION['desde']='index_portada';

$feeds = array (
                array('name' => 'El pais', 'url'=> 'http://www.elpais.com/rss/feed.html?feedId=1022'),
                array('name' => '20 minutos', 'url'=> 'http://20minutos.feedsportal.com/c/32489/f/478284/index.rss'),
                array('name' => 'Publico.es', 'url'=> 'http://www.publico.es/rss/'),
                array('name' => 'El mundo', 'url'=> 'http://elmundo.feedsportal.com/elmundo/rss/portada.xml'),
                );

$tpl->assign('feeds',$feeds);
$tpl->display('welcome/index.tpl');
