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

class PromptController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.openai';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'PROMPT_CREATE',
        'config' => 'PROMPT_ADMIN',
        'list'   => 'PROMPT_ADMIN',
        'show'   => 'PROMPT_UPDATE'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'prompt';
}
