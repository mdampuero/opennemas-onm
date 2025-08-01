<?php

namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class KeywordController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'KEYWORD_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'KEYWORD_CREATE',
        'delete' => 'KEYWORD_DELETE',
        'patch'  => 'KEYWORD_UPDATE',
        'update' => 'KEYWORD_UPDATE',
        'list'   => 'KEYWORD_ADMIN',
        'save'   => 'KEYWORD_CREATE',
        'show'   => 'KEYWORD_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.keyword';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_keyword_get_item';

    /**
     * Given a text, this action replaces all the registered keywords with the
     * valid keyword link
     *
     * @param Request $request the request object
     *
     * @return Reponse the response object
     *
     * @Security("hasExtension('KEYWORD_MANAGER')")
     */
    public function autolinkAction(Request $request)
    {
        $text = $request->request->get('text', null);

        if (!empty($text)) {
            $service  = $this->get($this->service);
            $keywords = $service->getList()['items'];
            $response = $service->replaceTerms($text, $keywords);
            return new Response($response);
        }

        return new Response($text);
    }
}
