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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class AttachmentController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'FILE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'ATTACHMENT_CREATE',
        'update' => 'ATTACHMENT_UPDATE',
        'list'   => 'ATTACHMENT_ADMIN',
        'show'   => 'ATTACHMENT_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'attachment';
}
