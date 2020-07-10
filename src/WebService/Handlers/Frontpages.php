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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

/**
 * Handles REST actions for frontpages.
 *
 * @package WebService
 */
class Frontpages
{
    public $restler;

    /*
    * @url GET /frontpages/allcontent/:category
    */
    public function allContent($category)
    {
        $contentsInHomepage = null;

        try {
            $category = getService('api.service.category')->getItemBySlug($category);

            list(, $contentsInHomepage, , ) = getService('api.service.frontpage')
                ->getCurrentVersionForCategory($category->id);

            // Get all frontpages images
            $imageIdsList = [];
            foreach ($contentsInHomepage as $content) {
                if (isset($content->img1)) {
                    $imageIdsList [] = $content->img1;
                }
            }

            if (!empty($imageIdsList)) {
                $er         = getService('entity_repository');
                $order      = [ 'created' => 'DESC' ];
                $imgFilters = [
                    'content_type_name' => [[ 'value' => 'photo' ]],
                    'pk_content'        => [[ 'value' => $imageIdsList, 'operator' => 'IN' ]],
                ];
                $imageList  = $er->findBy($imgFilters, $order);
            } else {
                $imageList = [];
            }

            foreach ($imageList as &$img) {
                $img->media_url = MEDIA_IMG_ABSOLUTE_URL;
            }

            $ur = getService('user_repository');
            // Overloading information for contents
            foreach ($contentsInHomepage as &$content) {
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
                    $content->externalUri = getService('router')
                        ->generate(
                            'frontend_external_article_show',
                            [
                                'category_slug' => get_category_slug($content),
                                'slug'          => $content->slug,
                                'article_id'    => date('YmdHis', strtotime($content->created)) .
                                                   sprintf('%06d', $content->pk_content),
                            ]
                        );
                    // Overload floating ads with external url's
                    if ($content->content_type_name == 'advertisement') {
                        $content->extWsUrl    = SITE_URL;
                        $content->extUrl      = SITE_URL . 'ads/' . date('YmdHis', strtotime($content->created))
                            . sprintf('%06d', $content->pk_advertisement) . '.html';
                        $content->extMediaUrl = SITE_URL . 'media/' . INSTANCE_UNIQUE_NAME . '/images';
                    }
                }

                // Generate uri for related content
                foreach ($content->related_contents as &$item) {
                    // Generate content uri if it's not an attachment
                    if ($item->fk_content_type == '4') {
                        $item->externalUri = "ext" . preg_replace('@//@', '/author/', get_url($item));
                    } elseif ($item->fk_content_type == 3) {
                        // Get instance media
                        $basePath = INSTANCE_MEDIA;
                        // Get file path for attachments
                        $filePath = \ContentManager::getFilePathFromId($item->id);
                        // Compose the full url to the file
                        $item->externalUri = $basePath . FILE_DIR . $filePath;
                    } else {
                        $item->externalUri = "ext" . get_url($item);
                    }
                }
            }
        } catch (\Exception  $e) {
            throw new RestException(404, $e->getMessage());
        }

        // Use htmlspecialchars to avoid utf-8 erros with json_encode
        return htmlspecialchars(utf8_encode(serialize($contentsInHomepage)));
    }

    /*
    * @url GET /frontpages/allcontentblog/:category_slug/:page
    */
    public function allContentBlog($categoryName, $page = 1)
    {
        try {
            $category = getService('api.service.category')
                ->getItemBySlug($categoryName);
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }

        $epp = getService('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 10);

        $order   = [ 'starttime' => 'DESC' ];
        $filters = [
            'join' => [
                [
                    'type'                => 'INNER',
                    'table'               => 'content_category',
                    'contents.pk_content' => [
                        [ 'value' => 'content_category.content_id', 'field' => true ]
                    ]
                ]
            ],
            'content_type_name' => [[ 'value' => 'article' ]],
            'content_status'    => [[ 'value' => 1 ]],
            'in_litter'         => [[ 'value' => 1, 'operator' => '!=' ]],
            'category_id'       => [ [ 'value' => $category->id ] ],
            'starttime'         => [
                'union' => 'OR',
                [ 'value' => '0000-00-00 00:00:00' ],
                [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                [ 'value' => date('Y-m-d H:i:s'), 'operator' => '<=' ],
            ]
        ];

        // Get all articles for this page
        $er            = getService('entity_repository');
        $articles      = $er->findBy($filters, $order, $epp, $page);
        $countArticles = $er->countBy($filters);

        $imageIdsList = [];
        foreach ($articles as $content) {
            if (isset($content->img1) && !empty($content->img1)) {
                $imageIdsList [] = $content->img1;
            }
        }

        $imageIdsList = array_unique($imageIdsList);

        if (!empty($imageIdsList)) {
            $imgFilters = [
                'content_type_name' => [[ 'value' => 'photo' ]],
                'pk_content'        => [[ 'value' => $imageIdsList, 'operator' => 'IN' ]],
            ];
            $imageList  = $er->findBy($imgFilters, $order);
        } else {
            $imageList = [];
        }

        foreach ($imageList as &$img) {
            $img->media_url = MEDIA_IMG_ABSOLUTE_URL;
        }

        $ur = getService('user_repository');
        // Overloading information for contents
        foreach ($articles as &$content) {
            // Load category related information
            $content->author = $ur->find($content->fk_author);

            if (!is_null($content->author)) {
                $content->author->photo            = $content->author->getPhoto();
                $content->author->photo->media_url = MEDIA_IMG_ABSOLUTE_URL;
                $content->author->external         = 1;
            }

             // Change uri for href links except widgets
            if ($content->content_type != 'Widget') {
                $content->externalUri = getService('router')
                    ->generate(
                        'frontend_external_article_show',
                        [
                            'category_slug' => get_category_slug($content),
                            'slug'          => $content->slug,
                            'article_id'    => date('YmdHis', strtotime($content->created)) .
                                               sprintf('%06d', $content->pk_content),
                        ]
                    );
            }

            // Load attached and related contents from array
            $content->loadFrontpageImageFromHydratedArray($imageList)
                ->loadAttachedVideo()
                ->loadRelatedContents($categoryName);
        }

        // Set pagination
        $pagination = getService('paginator')->get([
            'page'  => $page,
            'epp'   => $epp,
            'total' => $countArticles,
            'route' => [
                'name'   => 'categ_sync_frontpage',
                'params' => [
                    'category_slug' => $categoryName,
                ]
            ]
        ]);

        return utf8_encode(serialize([ $pagination->links, $articles ]));
    }
}
