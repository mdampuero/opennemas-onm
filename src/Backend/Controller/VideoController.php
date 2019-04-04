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
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class VideoController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'VIDEO_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
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
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'video';

    /**
     * Handles the form for create a new video.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_CREATE')")
     */
    public function createAction(Request $request)
    {
        return $this->render('video/item.tpl');
    }

    /**
     * Shows the form for a video given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_UPDATE')")
     */
    public function showAction(Request $request, $id)
    {
        $id = $request->query->getDigits('id', null);

        return $this->render('video/item.tpl', [ 'id' => $id]);
    }


    /**
     * Handles the form for configure the video module.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('VIDEO_MANAGER')
     *     and hasPermission('VIDEO_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        return $this->render('video/config.tpl');

        if ('POST' == $request->getMethod()) {
            $settings = $request->request;

            foreach ($settings as $key => $value) {
                $this->get('orm.manager')->getDataSet('Settings')
                    ->set($key, $value);
            }

            $this->get('session')->getFlashBag()->add('success', _('Settings saved.'));

            return $this->redirect($this->generateUrl('backend_videos_config'));
        } else {
            return $this->render('video/config.tpl', [
                'configs' => $this->get('orm.manager')->getDataSet('Settings')
                    ->get([ 'video_settings' ])
            ]);
        }
    }

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
        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $itemsPerPage       = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Video');

        $filters = [
            'content_type_name' => [['value' => 'video']],
            'content_status'    => [['value' => 1]],
            'in_litter'         => [['value' => 1, 'operator' => '!=']],
            'pk_content'        => [['value' => $ids, 'operator' => 'NOT IN']]
        ];

        $videos      = $em->findBy($filters, ['created' => 'desc'], $itemsPerPage, $page);
        $countVideos = $em->countBy($filters);

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countVideos,
            'route'       => [
                'name'   => 'admin_videos_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render('video/content-provider.tpl', [
            'videos'     => $videos,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Returns the list of authors.
     *
     * @return array The list of authors.
     */
    protected function getAuthors()
    {
        $response = $this->get('api.service.author')
            ->getList('order by name asc');

        return $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'id'])
            ->get();
    }
}
