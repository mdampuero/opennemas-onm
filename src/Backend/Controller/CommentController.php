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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Component\Validator\Validator;

class CommentController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'COMMENT_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'update' => 'COMMENT_UPDATE',
        'list'   => 'COMMENT_ADMIN',
        'show'   => 'COMMENT_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'comment';

    /**
     * Config for article system
     *
     * @return Response the response object
     *
     * @Security("hasExtension('MASTER')")
     */
    public function configAction()
    {
        return $this->render('comment/config.tpl', [ 'id' => 'opciones' ]);
    }
}
