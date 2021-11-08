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
use Symfony\Component\HttpFoundation\Response;

class OpinionController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'OPINION_CREATE',
        'update' => 'OPINION_UPDATE',
        'list'   => 'OPINION_ADMIN',
        'show'   => 'OPINION_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'opinion_inner'
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'opinion';

    /**
     * Render the content provider for opinion
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $category     = $request->query->getDigits('category', 0);
        $version      = $request->query->getDigits('frontpage_version_id', 1);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "opinion" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'opinion');

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
                    'name'   => 'backend_opinions_content_provider'
                ],
            ]);

            return $this->render('opinion/content-provider.tpl', [
                'opinions'   => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }

    /**
     * Handles the configuration for the opinion manager.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_SETTINGS')")
     */
    public function configAction()
    {
        return $this->render('opinion/config.tpl');
    }
}
