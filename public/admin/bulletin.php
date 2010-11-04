<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');


/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

// Libraries for handle JSON strings
require_once(SITE_LIBS_PATH.'PEAR.php');
require_once(SITE_LIBS_PATH.'JSON.php');
require_once(SITE_CORE_PATH.'string_utils.class.php');

// FIXME: incluir en bulletin
function parseData($param) {
    $json = new Services_JSON();
    $values = $json->decode($json->decode( clearslash($_REQUEST['data']))->{$param});
    //$values = json_decode( clearslash($_REQUEST['data']));

    if(in_array($param, array('news', 'opinions'))) {
        foreach($values as $k => $v) {
            foreach($values[$k] as $kk => $vv) {
                if($kk !== 'id') {
                    $values[$k]->{$kk} = base64_decode($vv);
                }
            }
        }
    }

    return( $values );
}

// Eliminar scripts y estilos predefinidos; añadir necesarios
$scripts = array('utils.js', 'photos.js', 'swfobject.js', 'validation.js', 'fabtabulous.js', 'jsvalidate/jsvalidate_beta04.js');
$tpl->removeScript( $scripts );
$tpl->removeScript( 'wz_tooltip.js', 'body');

$tpl->addScript('base64.js');

$stylesheets = array('calendar_date_select.css', 'mediamanager.css', 'uploader.css', 'botonera.css') ;
$tpl->removeStyle( $stylesheets );



//$tpl->assign('titulo_barra', 'Boletín de Noticias');
$titulo_barra = 'Newsletter';

//if( !in_array('NEWSLETTER_ADMIN', $_SESSION['privileges']))
//{
//    Application::forward($_SERVER['HTTP_REFERER'].'?action=list_pendientes');
//}

// Actions by XHR
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {    
    $action = (!isset($_POST['action']))? '': $_POST['action'];
    
    switch($action) {
        case 'getArticle':
            $article = new Article( $_REQUEST['id'] );            
            
            $data = new stdClass();
            $data->id         = '';
            $data->pk_content = $article->id;
            $data->title      = base64_encode($article->title);
            $data->subtitle   = base64_encode($article->subtitle);             
            $data->summary    = base64_encode($article->summary);
            
            //$data->agencia = base64_encode($article->agency_web);
            
            // Data
            $json = new Services_JSON();
            $data = $json->encode( $data );
            
            $tpl->assign('message', $data);
	    
        break;
    
        case 'getOpinion':
            $opinion = new Opinion($_REQUEST['id']);
            
            $data = new stdClass();
            $data->id         = '';
            $data->pk_content = $opinion->id;
            $data->title      = base64_encode( $opinion->title );
            
            $summary          = Bulletin::filterString( $opinion->body ); //String_Utils::str_stop( strip_tags( stripslashes($opinion->body) ), 60);
            $data->summary    = base64_encode( $summary ); 
            
            // Data
            $json = new Services_JSON();
            $data = $json->encode( $data );
            
            $tpl->assign('message', $data);
        break;    
    
        case 'searchNew':
            $cm = new ContentManager();
			// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE> c.content t.article, <CLAUSE_ORDER>);
			$articles = $cm->find('Article', 'title LIKE "%'.utf8_decode($_REQUEST['query']).'%"', 'ORDER BY created DESC');

            $tpl->assign('articles', $articles);
        break;

        case 'searchOpinion':
        	// TODO: test this code
            $cm = new ContentManager();
			// ContentManager::find(<TIPO_CONTENIDO>, <CLAUSE_WHERE> c.content t.article, <CLAUSE_ORDER>);
			$opinions = $cm->find('Opinion', 'title LIKE "%'.utf8_decode($_REQUEST['query']).'%"', 'ORDER BY created DESC');

            $tpl->assign('opinions', $opinions);
        break;

        case 'searchArchive':
            $b = new Bulletin();
            $_D = explode('-', $_REQUEST['queryA']);

            $archs = $b->search('DAY(created)="'.$_D['2'].'" AND MONTH(created)="'.$_D['1'].'" AND YEAR(created)="'.$_D['0'].'"');

            $tpl->assign('archives', $archs);
        break;
    }

    $tpl->display('bulletin.ajax.tpl');
    exit(0);
}

$footer_javascript =<<<EOD
<script language="javascript" type="text/javascript" defer="defer">
try {
    new Validation('formulario', {immediate : true});
} catch(e) { void(0); }
</script>
EOD;

// Actions by POST
$action = (!isset($_POST['action']))? 'select': $_POST['action'];


switch($action) {

	case 'select':
        $cm = new ContentManager();
        //$sin_archivar = '(contents.archive == 1)';
        $sin_archivar = null;
        $date_archive = date('W');//to get the number of the present week
        $date_archive = $date_archive + 10;
        
        //Last opinions
        //$opinions = $cm->find('Opinion', $sin_archivar, 'ORDER BY archive DESC LIMIT 0, 30');
        //$opinions = $cm->find('Opinion', "archive >= '$date_archive'", 'ORDER BY archive DESC, pk_content DESC');
        $opinions = $cm->find('Opinion', NULL, 'ORDER BY archive DESC, pk_content DESC LIMIT 0, 30');
        $tpl->assign('opinions', $opinions);
        
        // Últimos artículos
        $articles = $cm->find('Article', 'content_status=1 AND frontpage=1', 'ORDER BY archive DESC, position ASC');
        
        // Agrupa los artículos por categoría y controla si están publicados
        $articles_agrupados = Bulletin::sortArticles($articles);
        $tpl->assign('articles_agrupados', $articles_agrupados);
        
        
        $tpl->assign('titulo_barra', $titulo_barra.': Paso 1/5 - Selección de noticias y opiniones');
        
        // Cargar archivo
        $b = new Bulletin();
        $archs = $b->search('1=1 ORDER BY created DESC LIMIT 0,10');
        
        $content = $tpl->fetch('bulletin/actions/select.tpl');
        $tpl->assign('layout_content', $content );
        
        $tpl->addStyle('bulletin.css');
	break;

    case 'archive_list':
        // Cargar archivo
        $b = new Bulletin();
        $archs = $b->search('1=1 ORDER BY created DESC LIMIT 0,10');
        $tpl->assign('archivos', $archs);
        
        $tpl->assign('titulo_barra', $titulo_barra.': Restaurar bolet&iacute;n desde archivo');
    break;

    /* case 'new':
    case 'reset':
        $tpl->assign('ACTION', 'news'); */

    case 'news':
        if(isset($_REQUEST['archivos'])) {
            $bulletin = new Bulletin();
            $b = $bulletin->search('pk_bulletin='.$_REQUEST['archivos'].' ORDER BY created DESC');
            
            $_POST['data_bulletin'] = $data = clearslash($b[0]->data); // Hack para $smarty.post.data_bulletin
            $tpl->assign('data', $data);
        }
        
        // En paso anterior seleccionó artículos
        if(isset($_REQUEST['articles']) && count($_REQUEST['articles'])>0) {            
            // Save bulletin into database
            //$b = new Bulletin();
            $data = new stdClass();
            $data->news     = Bulletin::prependItems($_REQUEST['articles'], 'Article');
            $data->opinions = Bulletin::prependItems($_REQUEST['opinions'], 'Opinion');        
    
            // Data
            $json = new Services_JSON();
            $data = $json->encode( $data );
    
            $_POST['data_bulletin'] = $data = clearslash($data); // Hack para $smarty.post.data_bulletin
            $tpl->assign('data', $data);
        }
        
        // Últimos artículos
        $cm = new ContentManager();
        $articles = $cm->find('Article','content_status=1 AND frontpage=1', 'ORDER BY archive DESC');
        // Agrupa los artículos por categoría y controla si están publicados
        $articles_agrupados = Bulletin::sortArticles($articles);
        $tpl->assign('articles_agrupados', $articles_agrupados);            
        
        $tpl->assign('titulo_barra', $titulo_barra.': Paso 2/5 - Noticias');
        //$tpl->assign('footer_javascript', $footer_javascript);
        
        $content = $tpl->fetch('bulletin/actions/news.tpl');
        $tpl->assign('layout_content', $content );
        
        $tpl->addStyle('bulletin.css');
    break;

    case 'opinions':
        $tpl->assign('titulo_barra', $titulo_barra.': Paso 3/5 - Opiniones');
        //$tpl->assign('footer_javascript', $footer_javascript);
        
        $_REQUEST['data_bulletin'] = clearslash($_REQUEST['data_bulletin']);
        
        $cm = new ContentManager();        
        // Últimas opiniones
		$opinions = $cm->find('Opinion', null, 'ORDER BY created DESC LIMIT 0, 5');
        $tpl->assign('opinions', $opinions);
        
        $content = $tpl->fetch('bulletin/actions/opinions.tpl');
        $tpl->assign('layout_content', $content );
        
        $tpl->addStyle('bulletin.css');
    break;

    case 'mailboxes':
        $tpl->assign('titulo_barra', $titulo_barra.': Paso 4/5 - Destinatarios');
        $tpl->assign('footer_javascript', $footer_javascript);
        
        $_REQUEST['data_bulletin'] = clearslash($_REQUEST['data_bulletin']);
        
        $content = $tpl->fetch('bulletin/actions/mailboxes.tpl');
        $tpl->assign('layout_content', $content );
        
        $tpl->addStyle('bulletin.css');        
    break;

    case 'preview':
        $tpl->assign('titulo_barra', $titulo_barra.': Paso 5/5 - Vista Previa');

        $json = new Services_JSON();
        $data = $json->decode( clearslash($_REQUEST['data_bulletin']) );

        $tpl->assign('data', $data);
        
        $content = $tpl->fetch('bulletin/actions/preview.tpl');
        $tpl->assign('layout_content', $content );
        
        $tpl->addStyle('bulletin.css');
    break;

    case 'send':
        $tpl->assign('titulo_barra', $titulo_barra.': Envío');

        // Save bulletin into database
        $b = new Bulletin();
        $id = $b->create($_REQUEST);

        // Data
        $json = new Services_JSON();
        $data = $json->decode( clearslash($_REQUEST['data_bulletin']) );

        $tpl->assign('data', $data);

        // Generate PDF
        if(intval($_REQUEST['attach_pdf'])==1) {
            $pdf_filename = dirname(__FILE__).'/../media/bulletins/boletin'.$b->id.'.pdf';
            $htmlcontent = $tpl->fetch('bulletin.pdf.tpl');
            $b->get_pdf($htmlcontent, $pdf_filename, 'F');
            $_REQUEST['pdf_filename'] = $pdf_filename;
        }

        // Template for email
        $tpl->assign('separator', true);
        $htmlcontent = $tpl->fetch('bulletin.html.tpl');

        // Options before to start sending
        set_time_limit(0);
        ignore_user_abort(true);
        ini_set('display_errors', 0);

        // Start process
        $b->send(base64_decode($data->mailboxes), $htmlcontent, $_REQUEST);

        // Finalización del proceso muestra de resultados
        $tpl->assign('errors', $b->errors);
    break;

    case 'view_pdf':
        $json = new Services_JSON();
        $data = $json->decode( clearslash($_REQUEST['data_bulletin']) );

        $tpl->assign('data', $data);

        $htmlcontent = $tpl->fetch('bulletin.pdf.tpl');
        //include('bulletin.pdf.php');
        $b = new Bulletin();
        $b->get_pdf($htmlcontent, 'boletin.pdf', 'I');
        exit(0);
    break;

    default:
    break;
}


$tpl->display('bulletin/index.tpl');

