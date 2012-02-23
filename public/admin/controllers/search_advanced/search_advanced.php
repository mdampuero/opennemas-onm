<?php

/**
 * Setup app
*/
require_once('../../../bootstrap.php');
require_once('../../session_bootstrap.php');
use \Onm\Module\ModuleManager as mod;
/**
 * Setup view
*/
$tpl = new \TemplateAdmin(TEMPLATE_ADMIN);

$tpl->assign('titulo_barra', 'Advanced Search');

$_SESSION['desde'] ='search_advanced';

// Assocciate content_type to resource, it has be static array because
// don't exist a convention, sample attachment go on fichero.php
$type2res = array(
    'article' => 'article.php',
    'advertisement' => 'controllers/advertisement/advertisement.php',
    'attachment' => 'controllers/files/files.php',
    'opinion' => 'controllers/opinion/opinion.php',
    'comment' => 'controllers/comment/comment.php',
    'album' => 'controllers/album/album.php',
    'photo' => 'controllers/image/image.php',
    'video' => 'controllers/video/video.php',
    'interviu' => 'interviu.php',
    'poll' => 'controllers/poll/poll.php',
    'static_page' => 'controllers/static_pages/static_pages.php',
    'widget' => 'controllers/widget/widget.php',
);


$action = filter_input(INPUT_POST,'action',FILTER_SANITIZE_STRING);
if (is_null($action)) {
    $action = filter_input(INPUT_GET,'action',FILTER_SANITIZE_STRING, array('options' => array('default' => 'index')));
}

switch ($action) {

    case 'index':
    case 'search':

        // Get all the available content types
        $contentTypes = Content::getContentTypes();
        $tpl->assign('arrayTypes', $contentTypes);
        $stringSearch = filter_input(INPUT_GET, 'stringSearch', FILTER_SANITIZE_STRING);

        /**
         * if search string is empty skip executing some logic
        */
        if (!empty($stringSearch)) {

            $htmlChecks=null;
            $szCheckedTypes = checkTypes($htmlChecks);
            $szTags  = trim($stringSearch);
            $objSearch = cSearch::Instance();
            $arrayResults = $objSearch->SearchContentsSelectMerge(
                "contents.title as titule, contents.metadata, contents.slug,
                contents.description, contents.created, contents.pk_content as id,
                contents_categories.catName, contents_categories.pk_fk_content_category as category,
                content_types.title as type, contents.available, contents.content_status,
                contents.in_litter, content_types.name as content_type",
                $szTags,
                $szCheckedTypes,
                "pk_content = pk_fk_content AND fk_content_type = pk_content_type",
                "contents_categories, content_types",
                100
            );

            $Pager        = null;
            $arrayResults = cSearch::Paginate($Pager, $arrayResults, "id", 10);
            $indice       = 0; $ind = 0;
            $res          = array();
            $szTagsArray  = explode(', ', String_Utils::get_tags($szTags));


            foreach ($arrayResults as $res ) {
                for($ind=0; $ind < sizeof($szTagsArray); $ind++){
                    $arrayResults[$indice]['titule']   = String_Utils::ext_str_ireplace($szTagsArray[$ind], '<b><span style="color:blue">$1</font></b>', $arrayResults[$indice]['titule']);
                    $arrayResults[$indice]['metadata'] = String_Utils::ext_str_ireplace($szTagsArray[$ind], '<b><span style="color:blue">$1</font></b>', $arrayResults[$indice]['metadata']);
                }
                $indice++;
            }

            $szPagesLink = PaginateLink($Pager,$szTags, explode(", ", $szCheckedTypes));

            $tpl->assign(array(
                'search_string'    => $stringSearch,
                'type2res'         => $type2res,
                'pagination'       => $szPagesLink,
                'arrayResults'     => $arrayResults,
                'htmlCheckedTypes' => $htmlChecks
            ));
        }

        $tpl->display('search_advanced/index.tpl');

    break;

    case 'search_paging':

        if( !isset($_REQUEST['stringSearch']) ||
            empty($_REQUEST['stringSearch']))
        {
            $Types = Content::getContentTypes();
            break;
        }

        $htmlChecks     =null;
        $szCheckedTypes = checkTypes($htmlChecks);
        $szTags         = trim($_REQUEST['stringSearch']);
        $objSearch      = cSearch::Instance();
        $arrayResults   = $objSearch->SearchContentsSelectMerge(
            "contents.title as titule, contents.metadata, contents.slug,
            contents.description, contents.created, contents.pk_content as id,
            contents_categories.catName, contents_categories.pk_fk_content_category as category,
            content_types.title as type, contents.available, contents.content_status,
            contents.in_litter, content_types.name as content_type",
            $szTags,
            $szCheckedTypes,
            "pk_content = pk_fk_content AND fk_content_type = pk_content_type",
            "contents_categories, content_types",
            100
        );


        if( isset($arrayResults) && !empty($arrayResults)){
            $arrayResults = cSearch::Paginate($Pager, $arrayResults, "id", 10);
        }

        $indice = 0;
        $ind = 0;
        $res = array();
        $szTagsArray = explode(', ', String_Utils::get_tags($szTags));

        foreach ($arrayResults as $res ) {
            for($ind=0; $ind < sizeof($szTagsArray); $ind++){
                $arrayResults[$indice]['titule']   = String_Utils::ext_str_ireplace($szTagsArray[$ind], '<b><span style="color:blue">$1</font></b>', $arrayResults[$indice]['titule']);
                $arrayResults[$indice]['metadata'] = String_Utils::ext_str_ireplace($szTagsArray[$ind], '<b><span style="color:blue">$1</font></b>', $arrayResults[$indice]['metadata']);
            }

            $indice++;
        }

        $htmlPaging = PaginateLink($Pager,$szTags, explode(", ", $szCheckedTypes));

        $tpl->assign(array(
            'type2res'     => $type2res,
            'pagination'   => $htmlPaging,
            'arrayResults' => $arrayResults
        ));

        $html_out=$tpl->fetch('search_advanced/partials/_list.tpl');
        Application::ajax_out($html_out);

    break;

    case 'content-provider':
        $tpl->display('search_advanced/content-provider.tpl');
        break;

    default:
        Application::forward('search_advanced.php');
        return;
}

/*
 * Name: checkTypes
 *
 * Description: Parsea el $_REQUEST y obtiene un string con los tipos de contenidos enviados a la página.
 *
 * Input:   $void
 *
 * Output: cadena de texto con los nombre de los tipos de contenidos separados por comas.
*/
function checkTypes(& $htmlCheck)
{
    $arrayTypes = Content::getContentTypes();
    $szTypes =  '';
    foreach($arrayTypes as $aType)
    {
        if($aType['name']== 'advertisement') {
            $aType['name']= 'ads';
        }
        if($aType['name']== 'attachment'){
            $aType['name']= 'file';
        }
        if($aType['name']== 'photo'){
            $aType['name']= 'image';
        }
        if($aType['name']== 'static_page'){
            $aType['name']= 'static_pages';
        }

        if (mod::moduleExists(strtoupper($aType['name']).'_MANAGER')) {
            if (mod::isActivated(strtoupper($aType['name']).'_MANAGER')) {
                if(isset($_REQUEST[$aType[1]]))
                {
                    $szTypes .= $aType[1] . ", ";
                    $htmlCheck .= '<input id="'.$aType[1] .'" name="' . $aType[1] .'"  type="checkbox" valign="center" checked="true"/>'.$aType['title'];
                }
                else
                {
                    $htmlCheck .= '<input id="'. $aType[1].'" name="'.$aType[1].'"  type="checkbox" valign="center"/>'.$aType['title'];
                }
            }
        }
    }

    try
    {
        $szTypes = trim($szTypes);
        $szTypes = substr($szTypes,0,strlen($szTypes)-1);
    }
    catch(exception $e) {}

    return $szTypes;
}

/*
 * Name: PaginateLink
 *
 * Description: Crea los link clicables con tres paginas para seleccionar y un primera y última.
 *
 * Input:   $Pager.......: (object) Paginador de la libreria externa.
 *          $szSearchString..: (strings) Metadatos a buscar en la base de datos.
 *          $arrayCheckedTypes..: (array) Array con los tipos de datos en los cuales buscaremos.
 *
 * Output: codigo html con los links a las diferentes páginas.
*/
function PaginateLink($Pager, $szSearchString, $arrayCheckedTypes)
{
    $szPages=null;
    if($Pager->_totalPages>1)
    {
        $szPages = '<p align="center">';
        if ($Pager->_currentPage != 1)
        {
            $szPages .= '<a style="cursor:pointer;" href="#" onclick="paginate_search(\'search_paging\', 1, \''.
                        $szSearchString.'\', \'';
            foreach($arrayCheckedTypes as $itemType)
                $szPages .= "&".$itemType."=on";
            $szPages .= '\'); return false;">Primera</a> ... | ';
        }

        for($iIndex=$Pager->_currentPage-1; $iIndex<=$Pager->_currentPage+1 && $iIndex <= $Pager->_totalPages;$iIndex++)
        {
            if($Pager->_currentPage == 1)
            {
                if(($iIndex+1) > $Pager->_totalPages)
                    break;
                $szPages .= '<a style="cursor:pointer;" href="#" onclick="paginate_search(\'search_paging\',' .
                            ($iIndex+1) . ', \''. $szSearchString.'\', \'';
                foreach($arrayCheckedTypes as $itemType)
                    $szPages .= "&".$itemType."=on";
                $szPages .= '\'); return false;">';

                if($Pager->_currentPage == ($iIndex+1))
                    $szPages .= '<b>' . ($iIndex+1) . '</b></a> | ';
                else
                    $szPages .= ($iIndex+1) . '</a> | ';
            }
            else
            {
                $szPages .= '<a style="cursor:pointer;" href="#" onclick="paginate_search(\'search_paging\',' .
                            $iIndex . ', \''. $szSearchString.'\', \'';
                foreach($arrayCheckedTypes as $itemType)
                    $szPages .= "&".$itemType."=on";
                $szPages .= '\'); return false;">';

                if($Pager->_currentPage == ($iIndex))
                    $szPages .= '<b>' . $iIndex . '</b></a> | ';
                else
                    $szPages .= $iIndex . '</a> | ';
            }
        }
        if($Pager->_currentPage != $Pager->_lastPageText)
        {
            $szPages .= '... <a style="cursor:pointer;" href="#" onclick="paginate_search(\'search_paging\',' .
                            $Pager->_lastPageText . ', \''. $szSearchString.'\', \'';
            foreach($arrayCheckedTypes as $itemType)
                    $szPages .= "&".$itemType."=on";
                    $szPages .= '\'); return false;">Última</a>';
        }
        $szPages .= "</p> ";
    }
    return $szPages;
}
