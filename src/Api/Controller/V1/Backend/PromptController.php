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
}
