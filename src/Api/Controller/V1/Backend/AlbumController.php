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

class AlbumController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ALBUM_MANAGER';

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
        $this->checkSecurity($this->extension, 'ALBUM_SETTINGS');

        $settings = [ 'album_settings' => [
                'total_widget'     => $request->request->getDigits('total_widget'),
                'crop_width'       => $request->request->getDigits('crop_width'),
                'crop_height'      => $request->request->getDigits('crop_height'),
                'orderFrontpage'   => $request->request
                    ->filter('orderFrontpage', '', FILTER_SANITIZE_STRING),
                'time_last'        => $request->request->getDigits('time_last'),
                'total_front'      => $request->request->getDigits('total_front'),
                'total_front_more' => $request->request->getDigits('total_front_more'),
        ] ];

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
        $this->checkSecurity($this->extension, 'ALBUM_SETTINGS');

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('album_settings', []);

        return new JsonResponse($settings);
    }

    /**
     * {@inheritDoc}
     **/
    public function getExtraData($items = null)
    {
        $us = $this->get('api.service.category');

        $categories = $this->container->get('data.manager.filter')
            ->set($us->getList('inmenu=1')['items'])
            ->filter('mapify', [ 'key' => 'pk_content_category' ])
            ->get();

        return array_merge(parent::getExtraData($items), [
            'categories' => $us->responsify($categories),
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
        return $this->get($this->service)->getL10nKeys('album');
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
            if (empty($content->cover_id)
            ) {
                continue;
            }

            $photo = $em->find('Photo', $item->cover_id);

            $extra[] = \Onm\StringUtils::convertToUtf8($photo);
        }

        return $extra;
    }
}
