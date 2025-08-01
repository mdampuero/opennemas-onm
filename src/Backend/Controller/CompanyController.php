<?php

namespace Backend\Controller;

use Api\Exception\GetListException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for managing companies
 */
class CompanyController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.companies';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'COMPANY_CREATE',
        'update' => 'COMPANY_UPDATE',
        'list'   => 'COMPANY_ADMIN',
        'show'   => 'COMPANY_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'company_inner'
    ];

    /**
     * The resource name.
     */
    protected $resource = 'company';

    /**
     * List the companies in the content provider action based on the parameters.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('es.openhost.module.companies')")
     */
    public function contentProviderAction(Request $request)
    {
        $category     = $request->query->getDigits('category', 0);
        $last         = $request->query->getBoolean('last', false);
        $page         = $request->query->getDigits('page', 1);
        $version      = $request->query->getDigits('frontpage_version_id', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "company" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'company');

        if (!empty($contentsInFrontpage)) {
            $oql .= sprintf('and pk_content !in[%s] ', implode(',', $contentsInFrontpage));
        }

        if (!empty($category) && !$last) {
            $oql .= sprintf('and category_id = %d ', $category);
        }

        try {
            $oql .= 'order by created desc limit ' . $itemsPerPage;

            if ($page > 1) {
                $oql .= ' offset ' . ($page - 1) * $itemsPerPage;
            }

            $context = $this->get('core.locale')->getContext();
            $this->get('core.locale')->setContext('frontend');

            $response = $this->get('api.service.company')->getList($oql);

            $this->get('core.locale')->setContext($context);

            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => 8,
                'maxLinks'    => 5,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => 'backend_companies_content_provider',
                    'params' => [
                        'category'             => $category,
                        'last'                 => $last,
                        'frontpage_version_id' => $version
                    ]
                ],
            ]);

            return $this->render('company/content-provider.tpl', [
                'companies'  => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }

    /**
     * Config for company system
     *
     * @return Response the response object
     *
     */
    public function configAction()
    {
        return $this->render('company/config.tpl');
    }
}
