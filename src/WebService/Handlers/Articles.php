<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService\Handlers;

use Luracast\Restler\RestException;

/**
 * Handles REST actions for articles.
 *
 * @package WebService
 **/
class Articles
{
    public $restler;

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

        $er       = getService('entity_repository');
        $this->cm = new \ContentManager();
        $ccm      = \ContentCategoryManager::get_instance();

        $article = $this->get('content_url_matcher')
            ->matchContentUrl('article', $id);

        if (empty($article)) {
            throw new RestException(404, 'Page not found');
        }

        // Get category title used on tpl's
        $article->category_title   = $ccm->getTitle($article->category_name);
        $article->actualCategoryId = $ccm->get_id($article->category_name);
        // Assigned media_url used with author photo & related or machine articles with photo
        $article->media_url        = MEDIA_IMG_ABSOLUTE_URL;

        // Get inner image for this article
        if (isset($article->img2) && ($article->img2 != 0)) {
            $photoInt = $er->find('Photo', $article->img2);
            $photoInt->media_url = MEDIA_IMG_ABSOLUTE_URL;
            $article->photoInt = $photoInt;
        }

        if (is_object($article->author) && !empty($article->author)) {
            if (!empty($article->author->avatar_img_id)) {
                $article->author->photo = $article->author->getPhoto();
            }
        }

        // Get inner video for this article
        if (isset($article->fk_video2)) {
            $videoInt = $er->find('Video', $article->fk_video2);
            $article->videoInt = $videoInt;
        } else {
            $video =  $this->cm->find_by_category(
                'Video',
                $article->actualCategoryId,
                'contents.content_status=1',
                'ORDER BY created DESC LIMIT 0 , 1'
            );
            if (isset($video[0])) {
                $article->videoInt = $video[0];
            }
        }

        // Get Related contents
        $relationIDs     = getService('related_contents')->getRelationsForInner($articleID);
        $relatedContents = [];
        if (count($relationIDs) > 0) {
            $relatedContents = $this->cm->getContents($relationIDs);

            // Drop contents that are not available or not in time
            $relatedContents = $this->cm->getInTime($relatedContents);
            $relatedContents = $this->cm->getAvailable($relatedContents);

            // Add category name
            foreach ($relatedContents as &$content) {
                $content->category_name = $ccm->getCategoryNameByContentId($content->id);
                // Generate content uri if it's not an attachment
                if ($content->fk_content_type == '4') {
                    $content->uri = "ext".preg_replace('@//@', '/author/', $content->uri);
                } elseif ($content->fk_content_type == 3) {
                    // Get instance media
                    $basePath = INSTANCE_MEDIA;

                    // Get file path for attachments
                    $filePath = \ContentManager::getFilePathFromId($content->id);

                    // Compose the full url to the file
                    $content->fullFilePath = $basePath.FILE_DIR.$filePath;
                } else {
                    $content->uri = "ext".$content->uri;
                }
            }
        }
        $article->relatedContents = $relatedContents;

        // Retrieve the related contents for the given
        $article->suggested = getService('automatic_contents')->searchSuggestedContents(
            'article',
            "category_name= '".$article->category_name."' AND pk_content <>".$article->id,
            4
        );

        // Generate external url for suggested
        foreach ($article->suggested as &$element) {
            $element['uri'] = 'ext'.\Uri::generate(
                'article',
                array(
                    'id'       => $element['pk_content'],
                    'date'     => date('YmdHis', strtotime($element['created'])),
                    'category' => $element['catName'],
                    'slug'     => \StringUtils::getTitle($element['title']),
                )
            );
        }

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
