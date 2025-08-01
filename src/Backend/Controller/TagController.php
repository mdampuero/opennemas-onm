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

class TagController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.tags';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'TAG_CREATE',
        'config' => 'TAG_ADMIN',
        'list'   => 'TAG_ADMIN',
        'show'   => 'TAG_UPDATE'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'tag';

    /**
     * Displays the configuration form for tags.
     *
     * @return Response The response object.
     */
    public function configAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('config'));

        return $this->render('tag/config.tpl');
    }
}
