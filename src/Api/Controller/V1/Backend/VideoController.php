<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class VideoController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'VIDEO_MANAGER';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_video_show';

    /**
     * Returns the video information for a given url.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function fetchInformationAction(Request $request)
    {
        $url    = $request->query->get('url', null, FILTER_DEFAULT);
        $url    = rawurldecode($url);
        $params = $this->container->getParameter('panorama');
        $output = _("Please check the video url, seems to be incorrect");

        if ($url) {
            try {
                $videoP = new \Panorama\Video($url, $params);
                $output = $videoP->getVideoDetails();
            } catch (\Exception $e) {
                $output = _("Can't get video information. Check the url");
            }
        }

        return new JsonResponse($output);
    }

    /**
     * {@inheritDoc}
     **/
    public function getExtraData($items = null)
    {
        if (!is_array($items)) {
            $items = [ $items ];
        }
        $us = $this->get('api.service.category');

        $categories = $this->container->get('data.manager.filter')
            ->set($us->getList('inmenu=1')['items'])
            ->filter('mapify', [ 'key' => 'pk_content_category' ])
            ->get();

        $photos = [];
        foreach ($items as $item) {
            if (!array_key_exists('thumbnail', $item->information)
                || empty($item->information['thumbnail'])
            ) {
                continue;
            }

            $photos[$item->information['thumbnail']] = new \Photo($item->information['thumbnail']);
        }

        return array_merge(parent::getExtraData($items), [
            'categories' => $us->responsify($categories),
            'photos'     => $us->responsify($photos),
        ]);
    }
    /**
     * Returns the list of l10n keys
     * @param Type $var Description
     *
     * @return array
     **/
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('video');
    }

    /**
     * Returns the list of contents related with items.
     *
     * @param Content $content The content.
     *
     * @return array The list of photos linked to the content.
     */
    protected function getRelatedContents($content)
    {
        $em    = $this->get('entity_repository');
        $extra = [];

        if (empty($content)) {
            return $extra;
        }

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $element) {
            foreach (['img1', 'img2'] as $relation) {
                if (!empty($element->{$relation})) {
                    $photo = $em->find('Photo', $element->{$relation});

                    $extra[] = \Onm\StringUtils::convertToUtf8($photo);
                }
            }
        }

        return $extra;
    }
}
