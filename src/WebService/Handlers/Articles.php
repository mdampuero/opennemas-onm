<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Luracast\Restler\RestException;

/**
 * Handles REST actions for articles.
 *
 * @package WebService
 */
class Articles
{
    public $restler;

    /**
     * Get a complete article
     *
     * @param $id the id of the requested article
     *
     * @return $article
     *
     * @throws RestException
     */
    public function complete($id = null)
    {
        $this->validateInt($id);

        $article = getService('content_url_matcher')
            ->matchContentUrl('article', $id);

        if (empty($article)) {
            throw new RestException(404, 'Page not found');
        }

        $er       = getService('entity_repository');
        $this->cm = new \ContentManager();

        // Assigned media_url used with author photo & related or machine articles with photo
        $article->media_url = MEDIA_IMG_ABSOLUTE_URL;

        // Get inner image for this article
        if (isset($article->img2) && ($article->img2 != 0)) {
            $photoInt = $er->find('Photo', $article->img2);
            if (!empty($photoInt)) {
                $photoInt->media_url = MEDIA_IMG_ABSOLUTE_URL;
                $article->photoInt   = $photoInt;
            }
        }

        if (is_object($article->author) && !empty($article->author)) {
            if (!empty($article->author->avatar_img_id)) {
                $article->author->photo = $article->author->getPhoto();
            }
        }

        // Get inner video for this article
        if (isset($article->fk_video2)) {
            $videoInt          = $er->find('Video', $article->fk_video2);
            $article->videoInt = $videoInt;
        }

        // Related contents code ---------------------------------------
        $relations       = getService('related_contents')->getRelations($article->id, 'inner');
        $relatedContents = [];
        if (!empty($relations)) {
            $relatedObjects = getService('entity_repository')->findMulti($relations);

            // Filter out not ready for publish contents.
            foreach ($relatedObjects as $content) {
                if (!$content->isReadyForPublish()) {
                    continue;
                }

                // Generate content uri if it's not an attachment
                if ($content->fk_content_type == '4') {
                    $content->uri = "ext" . preg_replace('@//@', '/author/', get_content_url($content));
                } elseif ($content->fk_content_type == 3) {
                    // Get instance media
                    $basePath = INSTANCE_MEDIA;

                    // Get file path for attachments
                    $filePath = \ContentManager::getFilePathFromId($content->id);

                    // Compose the full url to the file
                    $content->fullFilePath = $basePath . FILE_DIR . $filePath;
                } else {
                    $content->uri = "ext" . get_content_url($content);
                }
                $relatedContents[] = $content;
            }
        }
        $article->relatedContents = $relatedContents;

        return serialize($article);
    }

    /**
     * Validates a number
     *
     * This is used for checking the int parameters
     *
     * @param type $number the number to validate
     *
     * @throws RestException
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
