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

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;

class CacheController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'CACHE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'config' => 'CACHE_TPL_ADMIN',
        'list'   => 'CACHE_TPL_ADMIN',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'cache';

    /**
     * Displays the configuration form for the service.
     */
    public function configAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('config'));

        return $this->render($this->resource . '/config.tpl');
    }
}
