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
     * Get a complete article
     *
     * @param $id the id of the requested article
     *
     * @return $article
     */
    public function complete($id = null)
    {
        $this->validateInt($id);

        $machineSearcher = $this->restler->container->get('automatic_contents');
        $er              = $this->restler->container->get('entity_repository');
        $ccm             = ContentCategoryManager::get_instance();

        // Resolve dirty Id
        $articleId = Content::resolveID($id);

        // Fetch the content to work with
        $article = $er->find('Article', $articleId);

        // Get category title used on tpl's
        $article->category_title   = $ccm->get_title($article->category_name);
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
            $video =  $this->cm->find_by_category_name(
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
            $relatedContents = $this->cm->cache->getContents($relationIDs);

            // Drop contents that are not available or not in time
            $relatedContents = $this->cm->getInTime($relatedContents);
            $relatedContents = $this->cm->cache->getAvailable($relatedContents);

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
     * Validates a number
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
}
