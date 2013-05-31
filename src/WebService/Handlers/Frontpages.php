<?php

class Frontpages
{
    public $restler;

    /*
    * @url GET /frontpages/allcontent/:category
    */
    public function allContent($category)
    {
        /**
         * Init the Content and Database object
        */
        $ccm = ContentCategoryManager::get_instance();

        // Check if category exists and initialize contents var
        $existsCategory = $ccm->exists($category);
        $contentsInHomepage = null;

        if (!$existsCategory) {
            // throw RestException bad category
            throw new RestException(404, 'parameter is not valid');
        } else {
            // Run entire logic
            $actualCategoryId = $actual_category_id = $ccm->get_id($category);
            $categoryData = null;
            if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $ccm->categories)) {
                $categoryData = $ccm->categories[$actualCategoryId];
            }

            $cm = new ContentManager;

            $contentsInHomepage = $cm->getContentsForHomepageOfCategory($actualCategoryId);

            // Filter articles if some of them has time scheduling and sort them by position
            $contentsInHomepage = $cm->getInTime($contentsInHomepage);
            $contentsInHomepage = $cm->sortArrayofObjectsByProperty($contentsInHomepage, 'position');

            // Get all frontpages images
            $imageIdsList = array();
            foreach ($contentsInHomepage as $content) {
                if (isset($content->img1)) {
                    $imageIdsList []= $content->img1;
                }
            }

            if (count($imageIdsList) > 0) {
                $imageList = $cm->find('Photo', 'pk_content IN ('. implode(',', $imageIdsList) .')');
            } else {
                $imageList = array();
            }

            foreach ($imageList as &$img) {
                $img->media_url = MEDIA_IMG_PATH_WEB;
            }

            // Overloading information for contents
            foreach ($contentsInHomepage as &$content) {

                // Load category related information
                $content->category_name  = $content->loadCategoryName($content->id);
                $content->category_title = $content->loadCategoryTitle($content->id);

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                        ->loadAttachedVideo()
                        ->loadRelatedContents($category);

                //Change uri for href links except widgets
                if ($content->content_type != 'Widget') {
                    $content->uri = "ext".$content->uri;
                }

                // Generate uri for related content
                foreach ($content->related_contents as &$item) {
                    // Generate content uri if it's not an attachment
                    if ($item->fk_content_type == '4') {
                        $item->uri = "ext".preg_replace('@//@', '/author/', $item->uri);
                    } elseif ($item->fk_content_type == 3) {
                        // Get instance media
                        $basePath = INSTANCE_MEDIA;

                        // Get file path for attachments
                        $filePath = ContentManager::getFilePathFromId($item->id);

                        // Compose the full url to the file
                        $item->fullFilePath = $basePath.FILE_DIR.$filePath;
                    } else {
                        $item->uri = "ext".$item->uri;
                    }
                }
            }
            // Use htmlspecialchars to avoid utf-8 erros with json_encode
            return htmlspecialchars(serialize($contentsInHomepage));
        }
    }
}
