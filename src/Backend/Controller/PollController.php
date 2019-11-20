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
use Symfony\Component\HttpFoundation\Request;

class PollController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'POLL_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'POLL_CREATE',
        'update' => 'POLL_UPDATE',
        'list'   => 'POLL_ADMIN',
        'show'   => 'POLL_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'poll_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'poll';

    /**
     * Render the content provider for polls.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $epp                = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Poll');

        $order   = [ 'created' => 'desc' ];
        $filters = [
            'content_type_name' => [ [ 'value' => 'poll' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $polls = $em->findBy($filters, $order, $epp, $page);
        $total = $em->countBy($filters);

        $this->get('core.locale')->setContext('frontend');

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $epp,
            'page'        => $page,
            'total'       => $total,
            'route'       => [
                'name'   => 'backend_polls_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render('poll/content-provider.tpl', [
            'polls'      => $polls,
            'pagination' => $pagination,
        ]);
    }
}
