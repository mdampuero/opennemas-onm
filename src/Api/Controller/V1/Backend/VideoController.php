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
     * Saves configuration for tags.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, 'VIDEO_SETTINGS');

        $settings = [ 'video_settings' => $request->request->all() ];

        $msg = $this->get('core.messenger');

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Get the tag config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function showConfigAction()
    {
        $this->checkSecurity($this->extension, 'VIDEO_SETTINGS');

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('video_settings', []);

        return new JsonResponse($settings);
    }

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

        $msg = $this->get('core.messenger');

        if (!$url) {
            $msg->add(_("Please check the video url, seems to be incorrect"), 'error', 412);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        try {
            $videoP = new \Panorama\Video($url, $params);
            $output = $videoP->getVideoDetails();

            return new JsonResponse($output, 200);
        } catch (\Exception $e) {
            $msg->add(_("Can't get video information. Check the url"), 'error', 412);

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getL10nKeys()
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

        foreach ($content as $item) {
            if (is_array($item->information)
                && !array_key_exists('thumbnail', $item->information)
                || empty($item->information['thumbnail'])
            ) {
                continue;
            }

            $photo = $em->find('Photo', $item->information['thumbnail']);

            $extra[] = \Onm\StringUtils::convertToUtf8($photo);
        }

        return $extra;
    }
}
