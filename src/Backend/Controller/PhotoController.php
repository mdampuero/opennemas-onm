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

class PhotoController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'IMAGE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'PHOTO_CREATE',
        'update' => 'PHOTO_UPDATE',
        'list'   => 'PHOTO_ADMIN',
        'show'   => 'PHOTO_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'photo';

    /**
     * Config for photo system
     *
     * @return Response the response object
     *
     */
    public function configAction()
    {
        return $this->render('photo/config.tpl');
    }
}
