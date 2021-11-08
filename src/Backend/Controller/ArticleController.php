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

use Api\Exception\GetListException;
use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for managing articles
 */
class ArticleController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'ARTICLE_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'ARTICLE_CREATE',
        'update' => 'ARTICLE_UPDATE',
        'list'   => 'ARTICLE_ADMIN',
        'show'   => 'ARTICLE_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'article_inner'
    ];

    /**
     * {@inheritdoc}
     */
    protected $positions = [
        'article_inner' => [ 7 ]
    ];

    /**
     * The resource name.
     */
    protected $resource = 'article';

    /**
     * List the articles in the content provider action based on the parameters.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $category     = $request->query->getDigits('category', 0);
        $last         = $request->query->getBoolean('last', false);
        $page         = $request->query->getDigits('page', 1);
        $suggested    = $request->query->getBoolean('suggested', false);
        $version      = $request->query->getDigits('frontpage_version_id', 1);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "article" and content_status = 1 and in_litter = 0 ';

        $contentsInFrontpage = $this->get('api.service.frontpage_version')
            ->getContentIds($category, $version, 'article');

        if (!empty($contentsInFrontpage)) {
            $oql .= sprintf('and pk_content !in[%s] ', implode(',', $contentsInFrontpage));
        }

        if ($suggested) {
            $oql .= 'and frontpage = 1 ';
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

            $response = $this->get('api.service.article')->getList($oql);

            $this->get('core.locale')->setContext($context);

            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => 8,
                'maxLinks'    => 5,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => 'backend_articles_content_provider',
                    'params' => [ 'category' => $category ]
                ],
            ]);

            return $this->render('article/content-provider.tpl', [
                'articles'   => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }

    /**
     * Config for article system
     *
     * @return Response the response object
     *
     * @Security("hasExtension('MASTER')")
     */
    public function configAction()
    {
        return $this->render('article/config.tpl', [
            'extra_fields' => $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.ARTICLE_MANAGER')
        ]);
    }
}
