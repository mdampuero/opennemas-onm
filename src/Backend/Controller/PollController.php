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

        $category     = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $version      = $request->query->getDigits('frontpage_version_id', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "poll" and in_litter != 1 and content_status = 1 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'poll');

        if (!empty($contentsInFrontpage)) {
            $oql .= sprintf('and pk_content !in[%s] ', implode(',', $contentsInFrontpage));
        }

        try {
            $oql .= 'order by created desc limit ' . $itemsPerPage;

            if ($page > 1) {
                $oql .= ' offset ' . ($page - 1) * $itemsPerPage;
            }

            $context = $this->get('core.locale')->getContext();
            $this->get('core.locale')->setContext('frontend');

            $response = $this->get('api.service.poll')->getList($oql);

            $this->get('core.locale')->setContext($context);

            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => 8,
                'maxLinks'    => 5,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => 'backend_polls_content_provider',
                    'params' => [
                        'category'             => $category,
                        'frontpage_version_id' => $version
                    ]
                ],
            ]);

            return $this->render('poll/content-provider.tpl', [
                'polls'      => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }
}
