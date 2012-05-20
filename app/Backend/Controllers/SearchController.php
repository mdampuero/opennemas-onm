<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controllers;

use Onm\Framework\Controller\Controller,
    Onm\Message as m,
    Onm\Module\ModuleManager as mod;
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 * @author
 **/
class SearchController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     * @author
     **/
    public function init()
    {
        // Initializae the session manager
        require_once './session_bootstrap.php';

        $this->view = new \TemplateAdmin(TEMPLATE_ADMIN);
        $_SESSION['desde'] = 'search_advanced';

        $this->type2res = array(
            'article'       => 'article.php',
            'advertisement' => 'controllers/advertisement/advertisement.php',
            'attachment'    => 'controllers/files/files.php',
            'opinion'       => 'controllers/opinion/opinion.php',
            'comment'       => 'controllers/comment/comment.php',
            'album'         => 'controllers/album/album.php',
            'photo'         => 'controllers/image/image.php',
            'video'         => 'controllers/video/video.php',
            'interviu'      => 'interviu.php',
            'poll'          => 'controllers/poll/poll.php',
            'static_page'   => 'controllers/static_pages/static_pages.php',
            'widget'        => 'controllers/widget/widget.php',
        );
    }

    /**
     * Handles the search form and shows the search contents
     *
     * @return Response the response object
     **/
    public function defaultAction()
    {
        // Get all the available content types
        $contentTypes = \Content::getContentTypes();
        $stringSearch = $this->request->query->filter('stringSearch', null, FILTER_SANITIZE_STRING);
        $page         = $this->request->query->filter('page', null, FILTER_VALIDATE_INT);

        // If search string is empty skip executing some logic
        if (!empty($stringSearch)) {

            $htmlChecks     = null;
            $contentTypesChecked = $this->_checkTypes($htmlChecks);
            $szTags         = trim($stringSearch);
            $objSearch      = \cSearch::Instance();
            $contents   = $objSearch->SearchContentsSelectMerge(
                "contents.title as titule, contents.metadata, contents.slug,
                contents.description, contents.created, contents.pk_content as id,
                contents_categories.catName, contents_categories.pk_fk_content_category as category,
                content_types.title as type, contents.available, contents.content_status,
                contents.in_litter, content_types.name as content_type",
                $szTags,
                $contentTypesChecked,
                "pk_content = pk_fk_content AND fk_content_type = pk_content_type",
                "contents_categories, content_types",
                100
            );

            $szTagsArray  = explode(', ', \StringUtils::get_tags($szTags));

            foreach ($contents as &$content) {
                for($ind=0; $ind < sizeof($szTagsArray); $ind++){
                    $content['titule']   = \StringUtils::ext_str_ireplace($szTagsArray[$ind], '<span style="font-weight:bold; color:blue">$1</span>', $content['titule']);
                    $content['metadata'] = \StringUtils::ext_str_ireplace($szTagsArray[$ind], '<span style="font-weight:bold; color:blue">$1</span>', $content['metadata']);
                }
            }
            $pagination = \Pager::factory(array(
                'mode'        => 'Sliding',
                'perPage'     => 10,
                'delta'       => 0,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => count($contents),
            ));

            $this->view->assign(array(
                'search_string'    => $stringSearch,
                'type2res'         => $this->type2res,
                'pagination'       => $pagination,
                'arrayResults'     => $contents,
                'htmlCheckedTypes' => $htmlChecks
            ));
        }

        return $this->render('search_advanced/index.tpl', array(
            'arrayTypes' => $contentTypes
        ));
    }

    /**
     * Name: checkTypes
     * Description: Parsea el $_REQUEST y obtiene un string con los tipos de contenidos enviados a la página.
     * Output: cadena de texto con los nombre de los tipos de contenidos separados por comas.
     */
    private function _checkTypes(& $htmlCheck)
    {
        $arrayTypes = \Content::getContentTypes();
        $szTypes =  '';
        foreach($arrayTypes as $aType) {
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

            if (mod::moduleExists(strtoupper($aType['name']).'_MANAGER')
                && mod::isActivated(strtoupper($aType['name']).'_MANAGER'))
            {
                if (isset($_REQUEST[$aType[1]])) {
                    $szTypes .= $aType[1] . ", ";
                    $htmlCheck .= '<input id="'.$aType[1] .'" name="' . $aType[1] .'"  type="checkbox" valign="center" checked="true"/>'.$aType['title'];
                } else {
                    $htmlCheck .= '<input id="'. $aType[1].'" name="'.$aType[1].'"  type="checkbox" valign="center"/>'.$aType['title'];
                }
            }
        }

        try {
            $szTypes = trim($szTypes);
            $szTypes = substr($szTypes,0,strlen($szTypes)-1);
        } catch(\Exception $e) {}

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
    private function _paginateLink($Pager, $szSearchString, $arrayCheckedTypes)
    {
        $szPages=null;
        if($Pager->_totalPages>1) {
            $szPages = '<p align="center">';
            if ($Pager->_currentPage != 1) {
                $szPages .= '<a style="cursor:pointer;" href="#" onclick="paginate_search(\'search_paging\', 1, \''.
                            $szSearchString.'\', \'';
                foreach($arrayCheckedTypes as $itemType)
                    $szPages .= "&".$itemType."=on";
                $szPages .= '\'); return false;">Primera</a> ... | ';
            }

            for($iIndex=$Pager->_currentPage-1; $iIndex<=$Pager->_currentPage+1 && $iIndex <= $Pager->_totalPages;$iIndex++) {
                if($Pager->_currentPage == 1) {
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
                } else {
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
            if($Pager->_currentPage != $Pager->_lastPageText) {
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

} // END class SearchController
