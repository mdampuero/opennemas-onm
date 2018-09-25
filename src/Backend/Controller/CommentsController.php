<?php
/**
 * Handles the actions for comments
 *
 * @package Backend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Common\Core\Component\Validator\Validator;

/**
 * Handles the actions for comments
 *
 * @package Backend_Controllers
 */
class CommentsController extends Controller
{
    /**
     * Redirects to the proper comments module
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function defaultAction()
    {
        $commentSystem = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('comment_system');

        switch ($commentSystem) {
            case 'onm':
                return $this->redirect($this->generateUrl('backend_comments_list'));

            case 'disqus':
                return $this->redirect($this->generateUrl('backend_comments_disqus'));

            case 'facebook':
                return $this->redirect($this->generateUrl('backend_comments_facebook'));

            default:
                return $this->render('comment/select_module.tpl');
        }
    }

    /**
     * Action to select a comment handler
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function selectAction(Request $request)
    {
        $type = $request->query->filter('type', '', FILTER_SANITIZE_STRING);

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        // check if the comment system is valid
        if (!in_array($type, [ 'onm', 'facebook', 'disqus'])) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Comment data sent not valid.")
            );

            return $this->redirect($this->generateUrl('backend_comments'));
        }

        try {
            $ds->set('comment_system', $type);

            switch ($type) {
                case 'onm':
                    $commentSystemName = 'Opennemas';
                    $commentConfigUrl  = 'backend_comments_config';
                    break;

                case 'disqus':
                    $commentSystemName = 'disqus';
                    $commentConfigUrl  = 'backend_comments_disqus_config';
                    break;

                case 'facebook':
                    $commentSystemName = 'Facebook';
                    $commentConfigUrl  = 'backend_comments_facebook_config';
                    break;
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_("Now you are using the %s comment system."), $commentSystemName)
            );

            return $this->redirect($this->generateUrl($commentConfigUrl));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Unable to save the settings.")
            );

            return $this->redirect($this->generateUrl('backend_comments'));
        }
    }

    /**
     * Lists comments
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('comment/list.tpl', [
            'statuses' => [
                [ 'title' => _('All'), 'value' => null ],
                [ 'title' => _('Accepted'), 'value' => \Comment::STATUS_ACCEPTED ],
                [ 'title' => _('Rejected'), 'value' => \Comment::STATUS_REJECTED ],
                [ 'title' => _('Pending'), 'value' => \Comment::STATUS_PENDING ],
            ]
        ]);
    }

    /**
     * Shows coments in the edit form
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $comment = new \Comment($id);

        // If comment is not valid redirect to listing and show message
        if (empty($comment->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Comment with id "%d" doesn\'t exists.'), $id)
            );

            return $this->redirect($this->generateUrl('backend_comments_list'));
        }

        $comment->content = new \Content($comment->content_id);
        $languageData     = $this->getLocaleData('frontend', $request, true);

        return $this->render('comment/new.tpl', [
            'statuses'      => [
                [ 'title' => _('Accepted'), 'value' => \Comment::STATUS_ACCEPTED ],
                [ 'title' => _('Rejected'), 'value' => \Comment::STATUS_REJECTED ],
                [ 'title' => _('Pending'), 'value' => \Comment::STATUS_PENDING ],
            ],
            'comment'       => $comment,
            'language_data' => $languageData
        ]);
    }

    /**
     * Updates a comment given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $comment = new \Comment($id);

        if (is_null($comment->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Comment with id "%d" doesn\'t exists.'), $id)
            );

            return $this->redirect($this->generateUrl('backend_comments_list'));
        }

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Comment data sent not valid.")
            );

            return $this->redirect($this->generateUrl(
                'admin_comment_show',
                [ 'id' => $id ]
            ));
        }

        $data = [
            'status' => $request->request->filter('status'),
            'body'   => $request->request->filter('body', ''),
        ];

        try {
            $comment->update($data);

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Comment updated successfully.')
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                $e->getMessage()
            );
        }

        return $this->redirect($this->generateUrl(
            'admin_comment_show',
            [ 'id' => $id ]
        ));
    }

    /**
     * Shows the disqus configuration form and stores its values
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function configAction(Request $request)
    {
        $defaultConfigs = $this->get('core.helper.comment')->getDefaultConfigs();

        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        if ('POST' !== $request->getMethod()) {
            $configs         = $ds->get('comments_config', []);
            $commentsHandler = $ds->get('comment_system');
            foreach ($configs as $configName => $value) {
                $configs[$configName] = $value == '1';
            }

            $configs = array_merge($defaultConfigs, $configs);

            return $this->render('comment/config.tpl', [
                'configs' => $configs,
                'extra'   => [
                    'handler' => $commentsHandler,
                    'blacklist_comment' => $this->get('core.validator')
                        ->getConfig(Validator::BLACKLIST_RULESET_COMMENTS),
                ],
            ]);
        }

        $configs = $request->request->get('configs', []);
        if (!array_key_exists('moderation_manual', $configs)) {
            $configs['moderation_manual'] = false;
        }

        if (!array_key_exists('with_comments', $configs)) {
            $configs['with_comments'] = false;
        }

        $configs = array_merge($defaultConfigs, $configs);

        $this->get('core.validator')->setConfig(
            Validator::BLACKLIST_RULESET_COMMENTS,
            $request->request->get('blacklist_comment', '')
        );

        try {
            $ds->set('comments_config', $configs);

            $result = ['success', _('Settings saved.')];
        } catch (\Exception $e) {
            $result = [
                'error',
                _('There was an error while saving the settings')
            ];
        }

        list($type, $message) = $result;
        $this->get('session')->getFlashBag()->add($type, $message);

        return $this->redirect($this->generateUrl('backend_comments_config'));
    }

    /**
     * Shows the initial Disqus page
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function disqusDefaultAction()
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get([ 'disqus_shortname', 'disqus_secret_key']);

        // Check if module is configured, if not redirect to configuration form
        if (empty($settings['disqus_shortname'])
            || empty($settings['disqus_secret_key'])
        ) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please provide your Disqus configuration to start to use your Disqus Comments module')
            );

            return $this->redirect($this->generateUrl('backend_comments_disqus_config'));
        }

        return $this->render('comment/disqus/list.tpl', [
            'disqus_shortname'  => $settings['disqus_shortname'],
            'disqus_secret_key' => $settings['disqus_secret_key'],
        ]);
    }

    /**
     * Shows the disqus configuration form and stores its values
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function disqusConfigAction(Request $request)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        if ($request->getMethod() != 'POST') {
            $configs = array_merge(
                $this->get('core.helper.comment')->getDefaultConfigs(),
                $ds->get('comments_config', [])
            );

            return $this->render('comment/config.tpl', [
                'configs'        => $configs,
                'extra' => [
                    'handler' => $ds->get('comment_system'),
                    'shortname' => $ds->get('disqus_shortname'),
                    'secretKey' => $ds->get('disqus_secret_key'),
                ]
            ]);
        }

        $configs   = $request->request->filter('configs', [], FILTER_SANITIZE_STRING);
        $shortname = $request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
        $secretKey = $request->request->filter('secret_key', null, FILTER_SANITIZE_STRING);

        $configs = array_merge(
            $this->get('core.helper.comment')->getDefaultConfigs(),
            $configs
        );

        try {
            $ds->set([
                'comment_system'    => 'disqus',
                'disqus_shortname'  => $shortname,
                'disqus_secret_key' => $secretKey,
                'comments_config'   => $configs,
            ]);

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Disqus configurations saved properly.')
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while saving the Disqus module configuration')
            );
        }

        return $this->redirect($this->generateUrl('backend_comments_disqus_config'));
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function facebookDefaultAction()
    {
        $fbSettings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('facebook');

        return $this->render('comment/facebook/list.tpl', [
            'fb_app_id' => array_key_exists('api_key', $fbSettings) ?
                $fbSettings['api_key'] : null,
        ]);
    }

    /**
     * Shows the disqus configuration form and stores its values
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function facebookConfigAction(Request $request)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings', 'instance');

        $fbSettings = $ds->get('facebook', []);

        if (!is_array($fbSettings)) {
            $fbSettings = [];
        }

        if ($request->getMethod() != 'POST') {
            $configs = array_merge(
                $this->get('core.helper.comment')->getDefaultConfigs(),
                $ds->get('comments_config', [])
            );

            return $this->render('comment/config.tpl', [
                'configs' => $configs,
                'extra'   => [
                    'handler'   => $ds->get('comment_system'),
                    'fb_app_id' =>
                        array_key_exists('api_key', $fbSettings)
                            ? $fbSettings['api_key'] : null,
                ]
            ]);
        }

        $fbAppId    = $request->request->filter('facebook', null, FILTER_SANITIZE_STRING);
        $configs    = $request->request->filter('configs', [], FILTER_SANITIZE_STRING);
        $fbSettings = array_merge($fbSettings, $fbAppId);

        $configs = array_merge(
            $this->get('core.helper.comment')->getDefaultConfigs(),
            $configs
        );

        try {
            $ds->set([
                'facebook'        => $fbSettings,
                'comments_config' => $configs
            ]);

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Facebook configuration saved successfully')
            );
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while saving the Facebook comments module configuration')
            );
        }

        return $this->redirect($this->generateUrl('backend_comments_facebook_config'));
    }
}
