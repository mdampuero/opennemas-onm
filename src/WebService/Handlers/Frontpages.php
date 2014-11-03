<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService\Handlers;

/**
 * Handles REST actions for frontpages.
 *
 * @package WebService
 **/
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
        $ccm = \ContentCategoryManager::get_instance();

        // Check if category exists and initialize contents var
        $existsCategory = $ccm->exists($category);
        $contentsInHomepage = null;

        if (!$existsCategory) {
            // throw RestException bad category
            throw new \RestException(404, 'parameter is not valid');
        } else {
            // Run entire logic
            $actualCategoryId = $ccm->get_id($category);
            $categoryData = null;
            if ($actualCategoryId != 0 && array_key_exists($actualCategoryId, $ccm->categories)) {
                $categoryData = $ccm->categories[$actualCategoryId];
            }

            $cm = new \ContentManager;
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
                $er = getService('entity_repository');
                $order = array('created' => 'DESC');
                $imgFilters = array(
                    'content_type_name' => array(array('value' => 'photo')),
                    'pk_content'        => array(array('value' => $imageIdsList, 'operator' => 'IN')),
                );
                $imageList = $er->findBy($imgFilters, $order);
            } else {
                $imageList = array();
            }

            foreach ($imageList as &$img) {
                $img->media_url = MEDIA_IMG_ABSOLUTE_URL;
            }

            $ur = getService('user_repository');
            // Overloading information for contents
            foreach ($contentsInHomepage as &$content) {

                // Load category related information
                $content->category_name  = $content->loadCategoryName($content->id);
                $content->category_title = $content->loadCategoryTitle($content->id);

                $content->author = $ur->find($content->fk_author);
                if (!is_null($content->author)) {
                    $content->author->external = 1;
                }

                // Load attached and related contents from array
                $content->loadFrontpageImageFromHydratedArray($imageList)
                        ->loadAttachedVideo()
                        ->loadRelatedContents($category);

                //Change uri for href links except widgets
                if ($content->content_type_name != 'widget') {
                    $content->uri = "ext".$content->uri;

                    // Overload floating ads with external url's
                    if ($content->content_type_name == 'advertisement') {
                        $content->extWsUrl = SITE_URL;
                        $content->extUrl = SITE_URL.'ads/'. date('YmdHis', strtotime($content->created))
                            .sprintf('%06d', $content->pk_advertisement).'.html';
                        $content->extMediaUrl = SITE_URL.'media/'.INSTANCE_UNIQUE_NAME.'/images';
                    }
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
                        $filePath = \ContentManager::getFilePathFromId($item->id);

                        // Compose the full url to the file
                        $item->fullFilePath = $basePath.FILE_DIR.$filePath;
                    } else {
                        $item->uri = "ext".$item->uri;
                    }
                }
            }

            // Use htmlspecialchars to avoid utf-8 erros with json_encode
            return htmlspecialchars(utf8_encode(serialize($contentsInHomepage)));
        }
    }

    /*
    * @url GET /frontpages/allcontentblog/:category_name/:page
    */
    public function allContentBlog($categoryName, $page = 1)
    {
        // Get category object
        $categoryManager = getService('category_repository');
        $category = $categoryManager->findBy(
            array('name' => array(array('value' => $categoryName))),
            '1'
        );

        if (empty($category)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }
        $category = $category[0];

        $itemsPerPage = 10;

        $order = array('created' => 'DESC');
        $filters = array(
            'content_type_name' => array(array('value' => 'article')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'category_name'     => array(array('value' => $category->name))
        );

        // Get all articles for this page
        $er            = getService('entity_repository');
        $articles      = $er->findBy($filters, $order, $itemsPerPage, $page);
        $countArticles = $er->countBy($filters);

        $imageIdsList = array();
        foreach ($articles as $content) {
            if (isset($content->img1)) {
                $imageIdsList []= $content->img1;
            }
        }
        $imageIdsList = array_unique($imageIdsList);

        if (count($imageIdsList) > 0) {
            $imgFilters = array(
                'content_type_name' => array(array('value' => 'photo')),
                'pk_content'        => array(array('value' => $imageIdsList, 'operator' => 'IN')),
            );
            $imageList = $er->findBy($imgFilters, $order);
        } else {
            $imageList = array();
        }

        foreach ($imageList as &$img) {
            $img->media_url = MEDIA_IMG_ABSOLUTE_URL;
        }

        $ur = getService('user_repository');
        // Overloading information for contents
        foreach ($articles as &$content) {

            // Load category related information
            $content->category_name  = $content->loadCategoryName($content->id);
            $content->category_title = $content->loadCategoryTitle($content->id);
            $content->author         = $ur->find($content->fk_author);
            if (!is_null($content->author)) {
                $content->author->photo  = $content->author->getPhoto();
                $content->author->photo->media_url  = MEDIA_IMG_ABSOLUTE_URL;
                $content->author->external = 1;
            }

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
        $generator = getService('router');

        // Set pagination
        $pagination = \Onm\Pager\SimplePager::getPagerUrl(
            array(
                'page'  => $page,
                'items' => $itemsPerPage,
                'total' => $countArticles,
                'url'   => $generator->generate(
                    'categ_sync_frontpage',
                    array(
                        'category_name' => $categoryName,
                    )
                )
            )
        );

        return utf8_encode(serialize(array($pagination, $articles)));
    }
}
