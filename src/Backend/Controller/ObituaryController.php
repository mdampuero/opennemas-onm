<?php

namespace Backend\Controller;

use Api\Exception\GetListException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for managing obituaries
 */
class ObituaryController extends BackendController
{
    /**
     * The extension name required by this controller.
     */
    protected $extension = 'es.openhost.module.obituaries';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'OBITUARY_CREATE',
        'update' => 'OBITUARY_UPDATE',
        'list'   => 'OBITUARY_ADMIN',
        'show'   => 'OBITUARY_UPDATE',
    ];

    /**
     * The resource name.
     */
    protected $resource = 'obituary';

    /**
     * Render the content provider for obituary
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('es.openhost.module.obituaries')")
     */
    public function contentProviderAction(Request $request)
    {
        $category     = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $version      = $request->query->getDigits('frontpage_version_id', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "obituary" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'obituary');

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
                    'name'   => 'backend_obituaries_content_provider',
                    'params' => [
                        'category'             => $category,
                        'frontpage_version_id' => $version
                    ]
                ],
            ]);

            return $this->render('obituary/content-provider.tpl', [
                'obituaries' => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }
}
