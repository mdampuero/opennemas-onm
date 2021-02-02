<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
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
     * Render the content provider for videos
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "video" and in_litter = 0';

        try {
            if (!empty($categoryId)) {
                $oql .= ' and category_id = ' . $categoryId;
            }

            $oql .= ' order by created desc limit ' . $itemsPerPage;

            if ($page > 1) {
                $oql .= ' offset ' . ($page - 1) * $itemsPerPage;
            }

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
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $total,
                'route'       => [
                    'name'   => 'backend_videos_content_provider',
                    'params' => [ 'category' => $categoryId ]
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
