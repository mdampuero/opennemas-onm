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
use Symfony\Component\HttpFoundation\Request;

class EventController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'EVENT_CREATE',
        'update' => 'EVENT_UPDATE',
        'list'   => 'EVENT_ADMIN',
        'show'   => 'EVENT_UPDATE',
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'event';

    /**
     * Render the content provider for event
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('es.openhost.module.events')")
     */
    public function contentProviderAction(Request $request)
    {
        $category     = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $version      = $request->query->getDigits('frontpage_version_id', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "event" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'event');

        if (!empty($contentsInFrontpage)) {
            $oql .= sprintf('and pk_content !in[%s] ', implode(',', $contentsInFrontpage));
        }

        try {
            $oql .= ' order by created desc limit ' . $itemsPerPage;

            if ($page > 1) {
                $oql .= ' offset ' . ($page - 1) * $itemsPerPage;
            }

            $context = $this->get('core.locale')->getContext();
            $this->get('core.locale')->setContext('frontend');

            $response = $this->get('api.service.content')->getList($oql);

            $this->get('core.locale')->setContext($context);

            // Build the pagination
            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => 'backend_events_content_provider',
                    'params' => [
                        'category'             => $category,
                        'frontpage_version_id' => $version
                    ]
                ],
            ]);

            return $this->render('event/content-provider.tpl', [
                'events'     => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }

    /**
     * Handles the configuration for the event manager.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('es.openhost.module.events')
     */
    public function configAction()
    {
        exit("dsadas");
        return $this->render('event/config.tpl');
    }
}
