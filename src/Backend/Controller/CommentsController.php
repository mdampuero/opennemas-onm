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
        // Select between comments system
        $commentSystem = $this->get('setting_repository')->get('comment_system');

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

        $sm = $this->get('setting_repository');
        switch ($type) {
            case 'onm':
                $sm->set('comment_system', 'onm');
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Now you are using the Opennemas comment system.")
                );
                return $this->redirect($this->generateUrl('backend_comments_config'));

            case 'disqus':
                $sm->set('comment_system', 'disqus');

                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Now you are using the Facebook comment system.")
                );

                return $this->redirect($this->generateUrl('backend_comments_disqus_config'));

            case 'facebook':
                $sm->set('comment_system', 'facebook');
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Now you are using the Facebook comment system.")
                );
                return $this->redirect($this->generateUrl('backend_comments_facebook_config'));

            case 'reset':
                return $this->render('comment/select_module.tpl');

            default:
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Comment data sent not valid.")
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
                [ 'title' => _('Not moderated'), 'value' => null ],
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
        $defaultConfigs = $this->getDefaultConfigs();

        $sm = $this->get('setting_repository');

        if ('POST' !== $request->getMethod()) {
            $configs         = $sm->get('comments_config');
            $commentsHandler = $sm->get('comment_system');

            foreach ($configs as $configName => $value) {
                if ($value == '1') {
                    $configs[$configName] = true;
                }
            }

            $configs = array_merge($defaultConfigs, $configs);

            return $this->render('comment/config.tpl', [
                'configs' => $configs,
                'extra'   => [
                    'handler' => $commentsHandler
                ],
            ]);
        }

        $configs = $request->request->get('configs', []);

        $defaultConfigs['moderation_manual'] = false;

        $configs = array_merge($defaultConfigs, $configs);

        $result = ['success', _('Settings saved.')];
        if (!$sm->set('comments_config', $configs)) {
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
        $disqusShortName = $this->get('setting_repository')->get('disqus_shortname');
        $disqusSecretKey = $this->get('setting_repository')->get('disqus_secret_key');

        // Check if module is configured, if not redirect to configuration form
        if (!$disqusShortName || !$disqusSecretKey) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please provide your Disqus configuration to start to use your Disqus Comments module')
            );

            return $this->redirect($this->generateUrl('backend_comments_disqus_config'));
        }

        return $this->render('comment/disqus/list.tpl', [
            'disqus_shortname'  => $disqusShortName,
            'disqus_secret_key' => $disqusSecretKey,
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
        $sm = $this->get('setting_repository');

        if ($request->getMethod() != 'POST') {
            $configs = array_merge($this->getDefaultConfigs(), $sm->get('comments_config'));

            return $this->render('comment/config.tpl', [
                'configs'        => $configs,
                'extra' => [
                    'handler' => $sm->get('comment_system'),
                    'shortname' => $sm->get('disqus_shortname'),
                    'secretKey' => $sm->get('disqus_secret_key'),
                ]
            ]);
        }

        $configs   = $request->request->filter('configs', [], FILTER_SANITIZE_STRING);
        $shortname = $request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
        $secretKey = $request->request->filter('secret_key', null, FILTER_SANITIZE_STRING);

        $configs = array_merge($this->getDefaultConfigs(), $configs);
        if ($sm->set('disqus_shortname', $shortname)
            && $sm->set('disqus_secret_key', $secretKey)
            && $sm->set('comments_config', $configs)
        ) {
            $sm->set('comment_system', 'disqus');
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Disqus configurations saved properly.')
            );

            return $this->redirect($this->generateUrl('backend_comments_disqus_config'));
        } else {
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
        $fbSettings = $this->get('setting_repository')->get('facebook');

        return $this->render('comment/facebook/list.tpl', [
            'fb_app_id'  =>
                array_key_exists('api_key', $fbSettings)
                    ? $fbSettings['api_key'] : null,
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
        $sm         = $this->get('setting_repository');
        $fbSettings = $this->get('setting_repository')->get('facebook');

        if ($request->getMethod() != 'POST') {
            $configs = array_merge($this->getDefaultConfigs(), $sm->get('comments_config'));

            return $this->render('comment/config.tpl', [
                'configs' => $configs,
                'extra'   => [
                    'handler'   => $sm->get('comment_system'),
                    'fb_app_id' =>
                        array_key_exists('api_key', $fbSettings)
                            ? $fbSettings['api_key'] : null,
                ]
            ]);
        }

        $fbAppId    = $request->request->filter('facebook', null, FILTER_SANITIZE_STRING);
        $configs    = $request->request->filter('configs', [], FILTER_SANITIZE_STRING);
        $fbSettings = array_merge($fbSettings, $fbAppId);

        $configs = array_merge($this->getDefaultConfigs(), $configs);
        if ($sm->set('facebook', $fbSettings)
            && $sm->set('comments_config', $configs)
        ) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Facebook configuration saved successfully')
            );

            return $this->redirect($this->generateUrl('backend_comments_facebook_config'));
        }

        $this->get('session')->getFlashBag()->add(
            'error',
            _('There was an error while saving the Facebook comments module configuration')
        );

        return $this->redirect($this->generateUrl('backend_comments_facebook_config'));
    }

    /**
     * Returns a list of configurations for the comments module
     *
     * @return array the list of default configurations
     **/
    public function getDefaultConfigs()
    {
        return [
            'disable_comments'      => false,
            'with_comments'         => true,
            'number_elements'       => 10,
            'moderation_manual'     => true,
            'moderation_autoreject' => false,
            'moderation_autoaccept' => false,
            'moderation_blacklist'  => "",
        ];
    }
}
