<?php

/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;
use Symfony\Component\HttpFoundation\Request;

/**
 * Lists and displays prompts.
 */
class PromptController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.onmai';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_onmai_prompt_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'PROMPT_CREATE',
        'delete' => 'PROMPT_DELETE',
        'list'   => 'PROMPT_ADMIN',
        'patch'  => 'PROMPT_UPDATE',
        'save'   => 'PROMPT_CREATE',
        'show'   => 'PROMPT_UPDATE',
        'update' => 'PROMPT_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.prompt';

    /**
     * Returns a list of extra data.
     *
     * @param mixed $items The item when called in a single-item action or the
     *                     array of items when called in a list-of-items action.
     *
     * @return array The extra data.
     */
    protected function getExtraData()
    {
        $helperAI = $this->get('core.helper.ai');
        return [
            'tones' => $helperAI->getTones(),
            'roles' => $helperAI->getRoles(),
        ];
    }

    /**
     * Returns a list of items.
     *
     * @param Request $request The request object.
     *
     * @return array The list of items and all extra information.
     */
    public function getListAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $us           = $this->get($this->service);
        $oql          = $request->query->get('oql', '');
        $helperLocale = $this->get('core.helper.locale');
        $response     = $us->getList($oql);

        return [
            'items'      => $helperLocale->translateAttributes($us->responsify($response['items']), ['mode', 'field']),
            'total'      => $response['total'],
            'extra'      => $this->getExtraData($response['items']),
            'o-filename' => $this->filename,
        ];
    }

    /**
     * Returns a list of items.
     *
     * @param Request $request The request object.
     *
     * @return array The list of items and all extra information.
     */
    public function getListManagerAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $oql          = $request->query->get('oql', '');
        $repository   = $this->get('orm.manager')->getRepository('PromptManager');
        $us           = $this->get($this->service);
        $helperLocale = $this->get('core.helper.locale');
        $items        = $us->responsify($repository->findBy($oql));

        return [
            'items' => $helperLocale->translateAttributes($items, ['mode', 'field']),
            'total' => $repository->countBy($oql)
        ];
    }
}
