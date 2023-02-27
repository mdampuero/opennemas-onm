<?php

namespace Api\Controller\V1\Backend;

use Common\Model\Entity\Content;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ArticleController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'ARTICLE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'ARTICLE_CREATE',
        'delete' => 'ARTICLE_DELETE',
        'patch'  => 'ARTICLE_UPDATE',
        'update' => 'ARTICLE_UPDATE',
        'list'   => 'ARTICLE_ADMIN',
        'save'   => 'ARTICLE_CREATE',
        'show'   => 'ARTICLE_UPDATE',
    ];

    protected $module = 'article';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_article_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.article';

    /**
     * Loads extra data related to the given contents.
     *
     * @param array $items The items array
     *
     * @return array Array of extra data.
     */
    protected function getExtraData($items = null)
    {
        $extra = parent::getExtraData($items);

        $extraFields = null;

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.ARTICLE_MANAGER');
        }

        $categories = $this->get('api.service.category')->responsify(
            $this->get('api.service.category')->getList()['items']
        );

        $subscriptions = $this->get('api.service.subscription')->responsify(
            $this->get('api.service.subscription')->getList('enabled = 1 order by name asc')['items']
        );

        return array_merge([
            'categories'    => $categories,
            'extra_fields'  => $extraFields,
            'subscriptions' => $subscriptions,
            'tags'          => $this->getTags($items),
            'formSettings'  => [
                'name'             => $this->module,
                'expansibleFields' => $this->getFormSettings($this->module)
            ]
        ], $extra);
    }

    /**
     * {@inheritdoc}
     */
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('article');
    }

    /**
     * Description of this action.
     *
     * @return Response The response object.
     */
    public function getPreviewAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $session = $this->get('session');
        $content = $session->get('last_preview');

        $session->remove('last_preview');

        return new Response($content);
    }

    /**
     * Previews an article in frontend by sending the article info by POST.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function savePreviewAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        if ($this->get('core.instance') && !$this->get('core.instance')->isSubdirectory()) {
            $this->get('core.locale')->setContext('frontend')
                ->setRequestLocale($request->get('locale'));
        }

        $article = new Content([ 'pk_content' => 0 ]);

        $data = $request->request->filter('item');
        $data = json_decode($data, true);

        foreach ($data as $key => $value) {
            if (isset($value) && !empty($value)) {
                $article->{$key} = $value;
            }
        }

        $article = $this->get('data.manager.filter')->set($article)
            ->filter('localize', [ 'keys'   => $this->get($this->service)->getL10nKeys('article') ])
            ->get();

        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        list($positions, $advertisements) = $this->getAdvertisements();

        $params = [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'item'           => $article,
            'content'        => $article,
            'contentId'      => $article->pk_content
        ];

        $this->view->assign($params);

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch('article/article.tpl')
        );

        return new Response('OK');
    }
}
