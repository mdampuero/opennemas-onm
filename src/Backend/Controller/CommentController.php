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
     * {@inheritdoc}
     */
    public function listAction(Request $request)
    {
        $params   = [];
        $template = '/list.tpl';

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        $comment_system = $ds->get('comment_system', []);

        if ($comment_system == 'facebook') {
            $template = '/facebook/list.tpl';

            $config = $ds->get('facebook', []);

            $params['fb_app_id'] = empty($config['api_key']) ? null : trim($config['api_key']);
        }

        if ($comment_system == 'disqus') {
            $template = '/disqus/list.tpl';

            $params['disqus_secret_key'] = $ds->get('disqus_secret_key', null);
            $params['disqus_shortname']  = $ds->get('disqus_shortname', null);
        }

        return $this->render($this->resource . $template, $params);
    }

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
