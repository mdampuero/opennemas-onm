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

                $content->author = new \User($content->fk_author);
                $content->author->external = 1;


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

    /*
    * @url GET /frontpages/allcontentblog/:category_name/:page
    */
    public function allContentBlog($categoryName, $page = 1)
    {
        // Get category object
        $categoryManager = $this->restler->container->get('category_repository');
        $category = $categoryManager->findBy(array('name' => $categoryName));

        if (empty($category)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }
        $category = $category[0];

        $itemsPerPage = 10;

        // Get all articles for this page
        $cm = new \ContentManager();
        list($countArticles, $articles) = $cm->getCountAndSlice(
            'Article',
            (int) $category->pk_content_category,
            'in_litter != 1 AND contents.available=1',
            'ORDER BY created DESC, available ASC',
            $page,
            $itemsPerPage
        );

        $imageIdsList = array();
        foreach ($articles as $content) {
            if (isset($content->img1)) {
                $imageIdsList []= $content->img1;
            }
        }
        $imageIdsList = array_unique($imageIdsList);

        if (count($imageIdsList) > 0) {
            $imageList = $cm->find('Photo', 'pk_content IN ('. implode(',', $imageIdsList) .')');
        } else {
            $imageList = array();
        }

        foreach ($imageList as &$img) {
            $img->media_url = MEDIA_IMG_PATH_WEB;
        }

        // Overloading information for contents
        foreach ($articles as &$content) {

            // Load category related information
            $content->category_name  = $content->loadCategoryName($content->id);
            $content->category_title = $content->loadCategoryTitle($content->id);
            $content->author         = new \User($content->fk_author);
            $content->author->photo  = $content->author->getPhoto();
            $content->author->photo->media_url  = MEDIA_IMG_PATH_WEB;
            $content->author->external = 1;

             //Change uri for href links except widgets
            if ($content->content_type != 'Widget') {
                $content->uri = "ext".$content->uri;
            }

            // Load attached and related contents from array
            $content->loadFrontpageImageFromHydratedArray($imageList)
                    ->loadAttachedVideo()
                    ->loadRelatedContents($categoryName);
        }

        // Get url generator
        $generator = $this->restler->container->get('url_generator');

        // Set pagination
        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $page,
                'items' => $itemsPerPage,
                'total' => $countArticles,
                'url'   => $generator->generate(
                    'blog_sync_category',
                    array(
                        'category_name' => $categoryName,
                    )
                )
            )
        );

        return serialize(array($pagination, $articles));
    }
}
