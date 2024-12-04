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
    protected $extension = 'es.openhost.module.openai';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_openai_prompt_get_item';

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
        $helperOpenAI = $this->get('core.helper.openai');
        return [
            'inputTypes' => $helperOpenAI->getInputTypes(),
            'tones' => $helperOpenAI->getTones(),
            'roles' => $helperOpenAI->getRoles(),
            'modes' => $helperOpenAI->getModes(),
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

        $oql        = $request->query->get('oql', '');
        $repository = $this->get('orm.manager')->getRepository('PromptManager');
        $us         = $this->get($this->service);

        return [
            'items' => $us->responsify($repository->findBy($oql)),
            'total' => $repository->countBy($oql),
        ];
    }
}
