<?php

namespace Api\Controller\V1\Backend;

use Api\Exception\GetItemException;
use Common\Model\Entity\Content;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ObituaryController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.obituaries';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'OBITUARY_CREATE',
        'delete' => 'OBITUARY_DELETE',
        'patch'  => 'OBITUARY_UPDATE',
        'update' => 'OBITUARY_UPDATE',
        'list'   => 'OBITUARY_ADMIN',
        'save'   => 'OBITUARY_CREATE',
        'show'   => 'OBITUARY_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_obituary_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.obituary';

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

        return array_merge([ 'tags' => $this->getTags($items) ], $extra);
    }

    /**
     * {@inheritdoc}
     */
    public function getL10nKeys()
    {
        //TODO: Check what multilanguage keys needs
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

        $this->get('core.locale')->setContext('frontend')
            ->setRequestLocale($request->get('locale'));

        $obituary = new Content([ 'pk_content' => 0 ]);

        $data = $request->request->filter('item');
        $data = json_decode($data, true);

        foreach ($data as $key => $value) {
            if (isset($value) && !empty($value)) {
                $obituary->{$key} = $value;
            }
        }

        $obituary = $this->get('data.manager.filter')->set($obituary)
            ->filter('localize', [ 'keys'   => $this->get($this->service)->getL10nKeys('article') ])
            ->get();

        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        list($positions, $advertisements) = $this->getAdvertisements();

        $params = [
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'item'           => $obituary,
            'content'        => $obituary,
            'contentId'      => $obituary->pk_content
        ];

        $this->view->assign($params);

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch('obituary/obituary.tpl')
        );

        return new Response('OK');
    }
}
