<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

/**
 * Handles the actions for the keywords.
 */
class KeywordsController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'KEYWORD_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'KEYWORD_CREATE',
        'update' => 'KEYWORD_UPDATE',
        'list'   => 'KEYWORD_ADMIN',
        'show'   => 'KEYWORD_UPDATE',
    ];
    /**
     * {@inheritdoc}
     */
    protected $resource = 'keyword';

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
            $service  = $this->get('api.service.keyword');
            $keywords = $service->getList()['items'];
            return new Response($service->replaceTerms($text, $keywords));
        }

        return new Response($text);
    }
}
