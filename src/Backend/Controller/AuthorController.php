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

class AuthorController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'AUTHOR_CREATE',
        'list'   => 'AUTHOR_ADMIN',
        'show'   => 'AUTHOR_UPDATE'
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'author';
}
