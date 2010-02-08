<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');
$sessions = $GLOBALS['Session']->getSessions();

// FIXME: está páxina ten que pasar a ser unha template Smarty
require_once('core/privileges_check.class.php');

$RESOURCES_PATH = 'themes/default/';
Privileges_check::CheckPrivileges("CHECK EXPIRE SESSION");

// Control de sesiones de usuarios
//require_once('core/user.class.php');

//PAra crear noticia que vuelva a listado de pendientes.
$_SESSION['desde']='index_portada';
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>..: Panel de Control :..</title>
<link rel="stylesheet" type="text/css" href="<?=$RESOURCES_PATH?>css/general.css" />
<link rel="stylesheet" type="text/css" href="<?=$RESOURCES_PATH?>css/modalbox.css" media="screen" />

<style type="text/css">
body {
	font-family:Arial, Helvetica, sans-serif;
	font-size:12px;

}

#user_activity {
    background-image: url(<?=$RESOURCES_PATH?>images/users_activity.png);
    background-repeat: no-repeat;
    background-position: left middle;
    border-right:  1px solid #CCC;
    border-bottom: 1px solid #CCC;
    
    background-color: inherit;
    
    cursor: pointer;
    text-align: right;
    
    color: #0B55C4;
    font-size: 14px;
    font-weight: bold;
    width: 18px;
    padding: 2px 2px 2px 20px;
    float: left;
    margin-left: 4px;
}

#user_activity:hover {
    background-color: #FFF;
}

#user_live {
    float: left;
    border-right:  1px solid #CCC;
    border-bottom: 1px solid #CCC;
    background-color: inherit;
    
    width: 20px;
    padding: 2px 2px 2px 2px;    
}

#user_live:hover {
    background-color: #FFF;
}
</style>

<script type="text/javascript" src="<?=$RESOURCES_PATH?>js/prototype.js"></script>
<script type="text/javascript" src="<?=$RESOURCES_PATH?>js/scriptaculous/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="<?=$RESOURCES_PATH?>js/modalbox.js"></script>

<script language="javascript" type="text/javascript" src="<?=$RESOURCES_PATH?>js/ypSlideOutMenus.js"></script>
<script language="javascript" type="text/javascript" src="<?=$RESOURCES_PATH?>js/utils.js"></script>
<script language="javascript" type="text/javascript">
<!-- //
sinFrames();
// -->
</script>
<script type="text/javascript" language="javascript">
function salir() {
    if(confirm('¿Desea salir del panel de administración?')) {
        location.href = 'logout.php';
    }
}
</script>
<script type="text/javascript" language="javascript">
//  new ypSlideOutMenu("number menu", "slide position", left, top, width, height)
    new ypSlideOutMenu("sub1","down",144,44,190,200)
    new ypSlideOutMenu("sub2","down",265,44,150,200)
    new ypSlideOutMenu("sub3","down",394,44,150,200)
    new ypSlideOutMenu("sub4","down",507,44,160,300)
    new ypSlideOutMenu("sub5","down",628,44,150,200)
    new ypSlideOutMenu("sub6","down",749,44,150,200)
    new ypSlideOutMenu("sub7","down",870,44,150,300)
    new ypSlideOutMenu("sub8","down",991,44,160,300)

</script>
</head>

<body margin="0" topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">

<table style="border: 1px solid rgb(0, 75, 142); width: 100%;" width="100%" height="100%" cellpadding="0" cellspacing="0">
<tbody>
	<tr>
		<td id="ocultar" height="100%" valign="top">
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="font-size:10px;" bgcolor="#E3E3E3">
				<tr>
					<td align="left" style="padding-left:4px;" width="65" nowrap="nowrap" >
						<a href="index2.php"
								class="logout" title="Inicio">
							<img src="<?=$RESOURCES_PATH?>images/inicio.gif" border="0" align="absmiddle" alt="Inicio" /> Inicio &nbsp;
						</a>
					</td>
					<td align="right" style="padding-right:4px;" width="65" nowrap="nowrap">
						<a href="javascript:salir();"
								class="logout">
							&nbsp;Salir <img src="<?=$RESOURCES_PATH?>images/desconectar<?=$_SESSION['authMethod']?>.gif" border="0"
												align="absmiddle" alt="Salir del Panel de Administración" />
						</a>
					</td>
					<td>
                        <table class="slidemenu" border=0 cellpadding="0" cellspacing="0">
                              <tr>
                                    <td valign="middle" width=\"130px\"><a href="#" onClick="ypSlideOutMenu.showMenu('sub1')"  onmouseover="ypSlideOutMenu.showMenu('sub1')" onmouseout="ypSlideOutMenu.hideMenu('sub1')"> Contenidos </a></td>
                                    <?php
                                    if(Acl::_('MEDIA_ADMIN'))
                                        echo "<td valign=\"middle\" width=\"130px\"><a href=\"#\" onClick=\"ypSlideOutMenu.showMenu('sub2')\" onmouseover=\"ypSlideOutMenu.showMenu('sub2')\" onmouseout=\"ypSlideOutMenu.hideMenu('sub2')\"> Multimedia</a></td>";
                                    if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                        echo "<td valign=\"middle\" width=\"130px\"><a href=\"category.php\" target=\"centro\"> Secciones </a></td>";
                                    if(Privileges_check::CheckPrivileges('PC_ADMIN'))
                                        echo "<td valign=\"middle\" width=\"130px\"><a href=\"#\" onmouseover=\"ypSlideOutMenu.showMenu('sub4')\" onmouseout=\"ypSlideOutMenu.hideMenu('sub4')\"> P. Conecta </a></td>";
                                    if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                        echo "<td valign=\"middle\" width=\"130px\"><a href=\"#\" onmouseover=\"ypSlideOutMenu.showMenu('sub5')\" onmouseout=\"ypSlideOutMenu.hideMenu('sub5')\"> Usuarios </a></td>";
                                    if(Privileges_check::CheckPrivileges('BOL_ADMIN'))
                                        echo "<td valign=\"middle\" width=\"130px\"><a href=\"#\" onmouseover=\"ypSlideOutMenu.showMenu('sub6')\" onmouseout=\"ypSlideOutMenu.hideMenu('sub6')\"> Boletin </a></td>";
                                    if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                        echo "<td valign=\"middle\" width=\"130px\"><a href=\"#\" onmouseover=\"ypSlideOutMenu.showMenu('sub7')\" onmouseout=\"ypSlideOutMenu.hideMenu('sub7')\"> Utils </a></td>";
                                    if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                        echo "<td valign=\"middle\" width=\"130px\"><a href=\"#\" onmouseover=\"ypSlideOutMenu.showMenu('sub8')\" onmouseout=\"ypSlideOutMenu.hideMenu('sub8')\"> Config </a></td>";
                                    ?>
                               </tr>
                        </table>
                        <div class="submenus">
                            <div id="sub1Container">
                                <div id="sub1Content">
                                    <table border="0" cellpadding="0" cellspacing="0" class="cuadromenu" width="170">
                                        <?php
                                            if(Privileges_check::CheckPrivileges('NOT_ADMIN'))
                                                   echo "<tr><td><a href=\"index2.php\" target=\"centro\">Inicio</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('NOT_ADMIN'))
                                                echo "<tr><td><a href=\"article.php\"  target= \"centro\">Gestor de Portada</a></td></tr>";
                                        
                                                echo "<tr><td><a href=\"article.php?action=list_pendientes\" target=\"centro\">Gestor de Pendientes</a></td></tr>";

                                            if(Privileges_check::CheckPrivileges('NOT_ADMIN'))
                                                   echo "<tr><td><a href=\"comment.php\" target=\"centro\">Gestor de Comentarios</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('OP_ADMIN'))
                                                echo "<tr><td><a href=\"opinion.php\" target=\"centro\">Gestor de Opini&oacute;n</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('PUB_ADMIN'))
                                                   echo "<tr><td><a href=\"advertisement.php\" target=\"centro\">Gestor de Publicidad</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('NOT_ADMIN'))
                                                   echo "<tr><td><a href=\"article.php?action=list_hemeroteca\" target=\"centro\">Hemeroteca</a></td></tr>";

                                            echo "<tr><td><a href=\"kiosko.php?action=list\" target=\"centro\">Portadas papel</a></td></tr>";
                                        ?>
                                    </table>
                                </div>
                                </div>
                                <div id="sub2Container">
                                <div id="sub2Content">
                                    <table border="0" cellpadding="0" cellspacing="0" class="cuadromenu"  width="140">
                                        <?php
                                            if(Privileges_check::CheckPrivileges('MUL_ADMIN'))
                                                   echo "<tr><td><a href=\"mediamanager.php\"  target=\"centro\">Im&aacute;genes</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('MUL_ADMIN'))
                                                   echo "<tr><td><a href=\"video.php\"  target=\"centro\">V&iacute;deos</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('MUL_ADMIN'))
                                                   echo "<tr><td><a href=\"album.php\" target=\"centro\">Album</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('MUL_ADMIN'))
                                                   echo "<tr><td><a href=\"ficheros.php\" target=\"centro\">Ficheros</a></td></tr>";

                                                   echo "<tr><td><a href=\"mediagraficos.php\"  target=\"centro\">Gr&aacute;ficos</a></td></tr>";
                                        ?>
                                    </table>
                                </div>
                                </div>
                                <div id="sub4Container">
                                <div id="sub4Content">
                                    <table border="0" cellpadding="0" cellspacing="0" class="cuadromenu"  width="140">
	                                    <tr><td><a href="pc_index.php" target="centro">Portada</a></td></tr>
	                                    <tr><td><a href="pc_sections.php" target="centro">Secciones</a></td></tr>
                                        <tr><td><a href="pc_photo.php" target="centro">Fotos</a></td></tr>
                                        <tr><td><a href="pc_video.php" target="centro">V&iacute;deos</a></td></tr>
                                        <tr><td><a href="pc_letter.php" target="centro">Cartas</a></td></tr>
                                        <tr><td><a href="pc_opinion.php" target="centro">Opini&oacute;n</a></td></tr>
                                        <tr><td><a href="pc_poll.php" target="centro">Encuestas</a></td></tr>
                                        <tr><td><a href="pc_comment.php" target="centro">Comentarios</a></td></tr>
                                        <tr><td><a href="pc_user.php" target="centro">Usuarios</a></td></tr>
                                        <tr><td><a href="pc_hemeroteca.php" target="centro">Hemeroteca</a></td></tr>
                                        <tr><td><a href="pc_litter.php" target="centro">Papelera</a></td></tr>
                                     </table>
                                </div>
                                </div>                                
                                <div id="sub5Container">
                                <div id="sub5Content">
                                    <table border="0" cellpadding="0" cellspacing="0" class="cuadromenu"  width="140">
                                        <?php
                                            if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                                   echo "<tr><td><a href=\"user.php\" target=\"centro\">Usuarios</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                                   echo "<tr><td><a href=\"user_groups.php\" target=\"centro\">Grupo de Usuarios</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                                   echo "<tr><td><a href=\"privileges.php\" target=\"centro\">Permisos</a></td></tr>";
                                            if(Privileges_check::CheckPrivileges('USR_ADMIN'))
                                                   echo "<tr><td><a href=\"author.php\" target=\"centro\">Autores</a></td></tr>";
                                        ?>
                                    </table>
                                </div>
                                </div>
                                <div id="sub6Container">
                                <div id="sub6Content">
                                    <table border="0" cellpadding="0" cellspacing="0" class="cuadromenu"  width="140">
                                        <tr><td><a href="newsletter.php" target="centro">Bolet&iacute;n</a></td></tr>
                                        <tr><td><a href="webq/webq.pl" target="centro">Cola de mensajes</a></td></tr>
                                        <tr><td><a href="webq/webq.pl?flush=5" target="centro">Mensajes Enviados</a></td></tr>
                                    </table>
                                </div>
                                </div>

                                <div id="sub7Container">
                                <div id="sub7Content">
                                    <table border="0" cellpadding="0" cellspacing="0" class="cuadromenu"  width="160">
                                        <?php
                                            if(Privileges_check::CheckPrivileges('MUL_ADMIN'))
                                                {echo "<tr><td><a href=\"search_advanced.php\" target=\"centro\">Busqueda Avanzada</a></td></tr>";}
                                            if(Privileges_check::CheckPrivileges('MUL_ADMIN')) {
                                                echo "<tr><td><a href=\"pclave.php\" target=\"centro\">Palabras clave</a></td></tr>";
                                                echo "<tr><td><a href=\"link_control.php\" target=\"centro\">Control link</a></td></tr>";
                                            }
                                            if(Privileges_check::CheckPrivileges('MUL_ADMIN'))
                                                {echo "<tr><td><a href=\"litter.php\" target=\"centro\">Papelera</a></td></tr>";}
                                        ?>
                                       <tr><td><a href="http://piwik.xornal.com" target="centro">Webstats</a></td></tr>
                                    </table>
                                </div>
                                </div>                                
                                <div id="sub8Container">
                                <div id="sub8Content">
                                    <table border="0" cellpadding="0" cellspacing="0" class="cuadromenu"  width="140">
                                        <tr><td><a href="tpl_manager.php" target="centro">Cache Manager</a></td></tr>
                                        <tr><td><a href="apc.php" target="centro">APC</a></td></tr>
                                        <tr><td><a href="svn.php" target="centro">SVN</a></td></tr>
                                        <tr><td><a href="mysql-check.php?action=check" target="centro">Mysql-Check</a></td></tr>
                                    </table>
                                </div>
                                </div>
                            </div>
					</td>
                    <td style="border-left:1px solid #004B8E;width:100%;text" nowrap="nowrap">
                        <?php if(Privileges_check::CheckPrivileges('USR_ADMIN')): ?>                            
                            <div id="user_activity" title="Usuarios activos en administración">
                                <?php echo count($sessions) ?></div>
                        <?php endif; ?>                                                
                    </td>
				</tr>

				<tr>
					<td valign="top" align="left" width="100%" height="100%" colspan="4">
                        <?php 
                            echo "<iframe onload=\"get_height(this);\" name=\"centro\" width=\"100%\" height=\"550\" src=\"welcome.php\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\" align=\"top\" scrolling=\"auto\">Para el panel de administración necesita un navegador que soporte iframes</iframe>";
                            
                        ?>
                    </td>
				</tr>
				</table>

		</td>
	</tr>
</tbody>
</table>


<?php  if(Privileges_check::CheckPrivileges('USR_ADMIN')): ?>
<script type="text/javascript">
/* <![CDATA[ */
var users_online = [];

function linkToMB() {
    $('MB_content').select('td a.modal').each(function(item) {        
        item.observe('click', function(event) {
            Event.stop(event);
            
            Modalbox.show(this.href, {
                title: 'Usuarios activos',
                afterLoad: linkToMB, 
                width: 300
            });            
        });
    });
}

document.observe('dom:loaded', function() {
    if( $('user_activity') ) {                       
        $('user_activity').observe('click', function() {
            Modalbox.show('./index.php?action=show_panel', {
                title: 'Usuarios activos',
                afterLoad: linkToMB, 
                width: 300
            });
        });
        
        new PeriodicalExecuter( function(pe) {
            $('user_activity').update('<img src="<?=$RESOURCES_PATH?>images/loading.gif" border="0" width="16" height="16" />');
            new Ajax.Request('index.php', {
                onSuccess: function(transport) {
                    // Actualizar o número de usuarios en liña e gardar o array en users_online
                    eval('users_online = ' + transport.responseText + ';');
                    $('user_activity').update( users_online.length );
                    
                    //new Effect.Hightlight('user_activity', {startcolor: '#ffff99', endcolor: '#ffffff'});
                } 
            });
            //pe.stop(); 
        }, 2*60); // Actualizar cada 2*60 segundos 
    }
});
/* ]]> */
</script>
<?php endif; ?>

</body>
</html>