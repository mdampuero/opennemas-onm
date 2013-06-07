<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * Handles all the CRUD actions over Related contents.
 *
 * @package    Onm
 * @subpackage Rest
 * @author     me
 **/
class Articles
{
    public $restler;

    /**
    * Get intance for contentManager
    * This is used in some actions at lists function
    */
    public function __construct()
    {
        $this->cm = new ContentManager();
    }

    /**
    * Retrives many types of contents for article based on switch
    *
    * This is used for getting articles contents
    *
    * @param type $opc    the action that we are going to perform
    * @param type $param1 extra param to perform some actions
    * @param type $param2 extra param to perform some actions
    *
    * @return mixed, array of contents
    */
    public function lists($opc = 'all', $param1 = null, $param2 = null)
    {

        switch ($opc) {
            case 'all':
                $article = $this->cm->find_all(
                    'Article',
                    'contents.available = 1 AND '.
                    'contents.fk_content_type=1 AND '.
                    'contents.content_status=1'
                );

                break;
            case 'range':
                // Date format YYYY-MM-DD

                //Validate date format else format exception
                $article = $this->cm->find(
                    'Article',
                    'fk_content_type=1 AND available=1 AND '.
                    'created BETWEEN \'' . $param1 . '\' AND \''. $param2.'\'',
                    ' ORDER BY created DESC'
                );

                break;
            case 'last':

                $this->validateInt($param1);

                $article = $this->cm->find(
                    'Article',
                    'fk_content_type=1 AND available=1 AND '.
                    'created >=DATE_SUB(CURDATE(), INTERVAL ' . $param1 . ' DAY)  ',
                    ' ORDER BY created DESC'
                );

                break;
            case 'search':

                // Apply get_title and replace - by blank space
                // string1, string2, ... , stringN  => string1 string2 stringN
                $param1 = preg_replace('/-/', ' ', \Onm\StringUtils::get_title($param1, false));
                $article = $this->cm->find(
                    'Article',
                    'fk_content_type=1 AND available=1 AND '.
                    'MATCH (contents.metadata) AGAINST ( \''.$param1.'\' IN BOOLEAN MODE) '.
                    'AND MATCH (contents.title) AGAINST ( \''.$param1.'\' IN BOOLEAN MODE) ',
                    'ORDER BY _height DESC, created DESC',
                    '*, MATCH (contents.metadata) AGAINST ( \''.$param1.'\' IN BOOLEAN MODE) + '.
                    'MATCH (contents.title) AGAINST ( \''.$param1.'\' IN BOOLEAN MODE) as _height '
                );

                break;
            case 'category':

                break;
            case 'related':

                $this->validateInt($param1);

                $relationsHandler  = new RelatedContent();
                $ccm = new ContentCategoryManager();
                $relatedContents = array();
                $relations = $relationsHandler->getRelations($param1);

                if (count($relations) > 0) {
                    foreach ($relations as $relatedContentId) {
                        $content = new Content($relatedContentId);

                        // Only include content is is in time and available.
                        if ($content->isReadyForPublish()) {
                            $content->category_name = $ccm->get_name($content->category);
                            $relatedContents []= $content;
                        }
                    }
                }

                return $relatedContents;

                break;
            case 'related-inner':

                $this->validateInt($param1);

                $relationsHandler  = new RelatedContent();
                $ccm = new ContentCategoryManager();
                $relatedContents = array();
                $relationIDs = $relationsHandler->getRelationsForInner($param1);

                if (count($relationIDs) > 0) {
                    $relatedContents = $this->cm->getContents($relationIDs);

                    // Drop contents that are not available or not in time
                    $relatedContents = $this->cm->getInTime($relatedContents);
                    $relatedContents = $this->cm->getAvailable($relatedContents);

                    // Add category name and external Uri
                    foreach ($relatedContents as &$content) {
                        $content->category_name = $ccm->get_category_name_by_content_id($content->id);

                        // Get author_name_slug for opinions
                        if ($content->content_type == 'opinion') {
                            // Generate opinion uri
                            $content->uri = preg_replace('@//@', '/author/', $content->uri);
                        }

                        $content->uri = 'ext'.$content->uri;
                    }
                }

                return $relatedContents;

                break;
            case 'machine-related':

                $this->validateInt($param1);

                // Fetch services
                $er              = $this->container->get('entity_repository');
                $machineSearcher = $this->container->get('automatic_contents');

                // Fetch the content to work with
                $this->validateInt($param1);
                $article = $er->find('Article', $param1);

                // Retrieve the related contents for the given
                $machineSuggestedContents = $machineSearcher->searchSuggestedContents(
                    $article->metadata,
                    'article',
                    "pk_fk_content_category= ".$article->category.
                    " AND contents.available=1 AND pk_content = pk_fk_content",
                    4
                );

                return $machineSuggestedContents;

                break;
            case 'print':

                $this->validateInt($param1);

                // Fetch services
                $machineSearcher = $this->container->get('automatic_contents');

                // Fetch the content to work with
                $this->validateInt($param1);
                $article = $er->find('Article', $param1);

                // Breadcrub/Pathway
                $breadcrub   = array();
                $breadcrub[] = array(
                    'text' => $ccm->get_title($category_name),
                    'link' => '/seccion/' . $category_name . '/'
                );

                // URL impresión

                $title = StringUtils::get_title($article->title);
                $print_url = '/imprimir/' . $title. '/' . $category_name . '/';

                if (!empty($subcategory_name)) {
                    $breadcrub[] = array(
                        'text' => $ccm->get_title($subcategory_name),
                        'link' => '/seccion/' . $category_name . '/' . $subcategory_name . '/'
                    );

                    $print_url .= $subcategory_name . '/';
                }

                $print_url .= $dirtyID . '.html';
                $tpl->assign('print_url', $print_url);

                $cat = $ccm->getByName($category_name);
                if (!is_null($cat) && $cat->inmenu) {
                    $tpl->assign('breadcrub', $breadcrub);
                }

                // Foto interior
                if (isset($article->img2) and ($article->img2 != 0)) {
                    $photoInt = new Photo($article->img2);
                    $tpl->assign('photoInt', $photoInt);
                }

                $tpl->caching = 0;
                $tpl->assign('article', $article);

                $tpl->display('article/article_printer.tpl');
                exit(0);

                break;
            case 'media':

                break;
            default:

                $this->invalidUrlParam();

                break;
        }

        return $article;
    }

    /**
     * Retrives an article by it's id
     *
     * This is used for getting articles objects
     *
     * @param type $id the id of the requested article
     *
     * @return $article
     */
    public function index($id = null)
    {
        $article = array();
        $params = func_get_args();
        $id = $params[0];

        // If the first param is an valid id
        if (is_numeric($id) && !is_infinite($id)) {
            $article = new Article($id);
            // If has no params list all articles
        } elseif (is_null($id)) {
            $article = $this->lists();
            // Other case call list method
        } else {
            $this->invalidUrlParam();
        }

        return $article;
    }

    /**
     * Get a complete article
     *
     * @param type $id the id of the requested article
     *
     * @return $article
     */
    public function complete($id = null)
    {
        $ccm             = ContentCategoryManager::get_instance();
        $cm              = new ContentManager();
        $er              = $this->container->get('entity_repository');
        $machineSearcher = $this->container->get('automatic_contents');

        // Resolve dirty Id
        $articleId = Content::resolveID($id);

        // Fetch the content to work with
        $article = $er->find('Article', $articleId);

        // Get category title used on tpl's
        $article->category_title = $ccm->get_title($article->category_name);
        $article->actualCategoryId = $ccm->get_id($article->category_name);

        // Get inner image for this article
        if (isset($article->img2) && ($article->img2 != 0)) {
            $photoInt = new Photo($article->img2);
            $photoInt->media_url = MEDIA_IMG_PATH_WEB;
            $article->photoInt = $photoInt;
        }

        // Get inner video for this article
        if (isset($article->fk_video2)) {
            $videoInt = new Video($article->fk_video2);
            $article->videoInt = $videoInt;
        } else {
            $video =  $cm->find_by_category_name(
                'Video',
                $article->category_name,
                'contents.content_status=1',
                'ORDER BY created DESC LIMIT 0 , 1'
            );
            if (isset($video[0])) {
                $article->videoInt = $video[0];
            }
        }

        // Get Related contents
        $relContent      = new RelatedContent();
        $relatedContents = array();

        $relationIDs     = $relContent->cache->getRelationsForInner($articleId);
        if (count($relationIDs) > 0) {
            $relatedContents = $cm->cache->getContents($relationIDs);

            // Drop contents that are not available or not in time
            $relatedContents = $cm->getInTime($relatedContents);
            $relatedContents = $cm->cache->getAvailable($relatedContents);

            // Add category name
            foreach ($relatedContents as &$content) {
                $content->category_name = $ccm->get_category_name_by_content_id($content->id);
                // Generate content uri if it's not an attachment
                if ($content->fk_content_type == '4') {
                    $content->uri = "ext".preg_replace('@//@', '/author/', $content->uri);
                } elseif ($content->fk_content_type == 3) {
                    // Get instance media
                    $basePath = INSTANCE_MEDIA;

                    // Get file path for attachments
                    $filePath = ContentManager::getFilePathFromId($content->id);

                    // Compose the full url to the file
                    $content->fullFilePath = $basePath.FILE_DIR.$filePath;
                } else {
                    $content->uri = "ext".$content->uri;
                }
            }
        }
        $article->relatedContents = $relatedContents;

        // Retrieve the related contents for the given
        $machineSuggestedContents = $machineSearcher->searchSuggestedContents(
            $article->metadata,
            'article',
            "pk_fk_content_category= ".$article->category.
            " AND contents.available=1 AND pk_content = pk_fk_content",
            4
        );
        foreach ($machineSuggestedContents as &$element) {
            $element['uri'] = 'ext'.\Uri::generate(
                'article',
                array(
                    'id'       => $element['pk_content'],
                    'date'     => date('YmdHis', strtotime($element['created'])),
                    'category' => $element['catName'],
                    'slug'     => StringUtils::get_title($element['title']),
                )
            );
        }

        $article->suggested = $machineSuggestedContents;

        return serialize($article);
    }

    /**
     * Validates a finite number
     *
     * This is used for checking the int parameters
     *
     * @param type $number the number to validate
     *
     * @return void
     */
    private function validateInt($number)
    {
        if (!is_numeric($number)) {
            throw new RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new RestException(400, 'parameter is not finite');
        }
    }

    /*
    * Private function for validating url paramaters
    */
    /**
     * Validates a url parameter
     *
     * This is used for checking the url parameters
     *
     * @return void
     */
    private function invalidUrlParam()
    {
        throw new RestException(400, 'parameter is not valid');
    }
}
