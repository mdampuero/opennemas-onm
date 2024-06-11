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
 * Handles the actions for managing notifications
 */
class OpenAIController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.openai';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'list'   => 'OPENAI_ADMIN',
    ];



    /**
     * Configures the openAI notifications module
     */
    public function configAction()
    {
        return $this->render('openai/config.tpl');
    }


    /**
     * Configures the OpenAI notifications module
     */
    public function usageAction()
    {
        return $this->render('openai/usage.tpl');
    }
}
