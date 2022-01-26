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

class MenuController extends BackendController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'MENU_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'update' => 'MENU_UPDATE',
        'list'   => 'MENU_ADMIN',
        'show'   => 'MENU_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $resource = 'menues';

    /**
     * {@inheritdoc}
     */
    // public function listAction(Request $request)
    // {
    //     $params   = [];
    //     $template = '/list.tpl';

    //     if ($this->get('core.helper.locale')->hasMultilanguage()) {
    //         $params['locale'] = $request->query->get('locale');
    //     }

    //     $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

    //     $config         = $ds->get('comment_settings', []);
    //     $defaultConfigs = $this->get('core.helper.comment')->getDefaultConfigs();

    //     $config = array_merge($defaultConfigs, $config);

    //     if ($config['comment_system'] == 'facebook') {
    //         $template = '/facebook/list.tpl';

    //         $params['fb_app_id'] = $config['facebook_apikey'];
    //     }

    //     if ($config['comment_system'] == 'disqus') {
    //         $template = '/disqus/list.tpl';

    //         $params['disqus_secret_key'] = $config['disqus_shortname'];
    //         $params['disqus_shortname']  = $config['disqus_secretkey'];
    //     }

    //     return $this->render($this->resource . $template, $params);
    // }

    /**
     * Config for article system
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_SETTINGS')")
     */
    public function configAction()
    {
        return $this->render('comment/config.tpl');
    }
}
