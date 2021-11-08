<?php

namespace Backend\Controller;

use Api\Exception\GetListException;
use Symfony\Component\HttpFoundation\Request;

class VideoController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'VIDEO_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'VIDEO_CREATE',
        'update' => 'VIDEO_UPDATE',
        'list'   => 'VIDEO_ADMIN',
        'show'   => 'VIDEO_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'video_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'video';

    /**
     * Render the content provider for videos.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $category = $request->query->getDigits('category', 0);
        $page     = $request->query->getDigits('page', 1);
        $version  = $request->query->getDigits('frontpage_version_id', 1);
        $epp      = 8;
        $oql      = 'content_type_name = "video" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'video');

        if (!empty($contentsInFrontpage)) {
            $oql .= sprintf('and pk_content !in[%s] ', implode(',', $contentsInFrontpage));
        }

        $oql .= ' order by created desc limit ' . $epp;

        try {
            $context = $this->get('core.locale')->getContext();
            $this->get('core.locale')->setContext('frontend');

            $response = $this->get('api.service.content')->getList($oql);
            $videos   = $response['items'];
            $total    = $response['total'];

            $this->get('core.locale')->setContext($context);

            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => $epp,
                'page'        => $page,
                'total'       => $total,
                'route'       => [
                    'name' => 'backend_videos_content_provider',
                    'params' => [
                        'category'             => $category,
                        'frontpage_version_id' => $version
                    ]
                ],
            ]);

            return $this->render('video/content-provider.tpl', [
                'videos'     => $videos,
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }
}
