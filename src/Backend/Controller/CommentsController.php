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
     * Common code for all the actions
     *
     * @return Response the response object
     */
    public function init()
    {
        $this->statuses = [
            [ 'title' => _('All'), 'value' => null ],
            [ 'title' => _('Accepted'), 'value' => \Comment::STATUS_ACCEPTED ],
            [ 'title' => _('Rejected'), 'value' => \Comment::STATUS_REJECTED ],
            [ 'title' => _('Pending'), 'value' => \Comment::STATUS_PENDING ],
        ];

        $this->sm = $this->get('setting_repository');

        $this->view->assign('statuses', $this->statuses);
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function defaultAction()
    {
        // Select between comments system
        $commentSystem = $this->sm->get('comment_system');

        switch ($commentSystem) {
            case 'onm':
                return $this->redirect($this->generateUrl('admin_comments_list'));

            case 'disqus':
                return $this->redirect($this->generateUrl('admin_comments_disqus'));

            case 'facebook':
                return $this->redirect($this->generateUrl('admin_comments_facebook'));

            default:
                return $this->render('comment/select_module.tpl');
        }
    }

    /**
     * Description of the action
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

        switch ($type) {
            case 'onm':
                $this->sm->set('comment_system', 'onm');
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Now you are using the Opennemas comment system.")
                );
                return $this->redirect($this->generateUrl('admin_comments_config'));

            case 'disqus':
                return $this->redirect($this->generateUrl('admin_comments_disqus_config'));

            case 'facebook':
                $this->sm->set('comment_system', 'facebook');
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Now you are using the Facebook comment system.")
                );
                return $this->redirect($this->generateUrl('admin_comments_facebook_config'));

            case 'reset':
                return $this->render('comment/select_module.tpl');

            default:
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Comment data sent not valid.")
                );
                return $this->redirect($this->generateUrl('admin_comments'));
        }
    }

    /**
     * Description of the action
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function defaultDisqusAction()
    {
        $disqusShortName = $this->sm->get('disqus_shortname');
        $disqusSecretKey = $this->sm->get('disqus_secret_key');

        // Check if module is configured, if not redirect to configuration form
        if (!$disqusShortName || !$disqusSecretKey) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please provide your Disqus configuration to start to use your Disqus Comments module')
            );

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
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
    public function configDisqusAction(Request $request)
    {
        if ($request->getMethod() != 'POST') {
            $disqusShortName = $this->sm->get('disqus_shortname');
            $disqusSecretKey = $this->sm->get('disqus_secret_key');

            return $this->render('comment/disqus/config.tpl', [
                'shortname' => $disqusShortName,
                'secretKey' => $disqusSecretKey,
                'configs'   => $this->sm->get('comments_config'),
            ]);
        } else {
            $shortname = $request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
            $secretKey = $request->request->filter('secret_key', null, FILTER_SANITIZE_STRING);
            $configs   = $request->request->filter('configs', [], FILTER_SANITIZE_STRING);

            if ($this->sm->set('disqus_shortname', $shortname)
                && $this->sm->set('disqus_secret_key', $secretKey)
                && $this->sm->set('comments_config', $configs)
            ) {
                $this->sm->set('comment_system', 'disqus');

                return $this->redirect($this->generateUrl('admin_comments_disqus'));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('There was an error while saving the Disqus module configuration')
                );
            }

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }
    }


    /**
     * Description of the action
     *
     * @return Response the response object
     *
     * @Security("hasExtension('COMMENT_MANAGER')
     *     and hasPermission('COMMENT_ADMIN')")
     */
    public function defaultFacebookAction()
    {
        $fbSettings = $this->sm->get('facebook');

        return $this->render('comment/facebook/list.tpl', [
            'fb_app_id'  => $fbSettings['api_key'],
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
    public function configFacebookAction(Request $request)
    {
        $fbSettings = $this->sm->get('facebook');

        if ($request->getMethod() != 'POST') {
            $fbAppId = $fbSettings['api_key'];

            return $this->render(
                'comment/facebook/config.tpl',
                [
                    'fb_app_id' => $fbAppId,
                    'configs'   => $this->sm->get('comments_config'),
                ]
            );
        } else {
            $fbAppId    = $request->request->filter('facebook', null, FILTER_SANITIZE_STRING);
            $configs    = $request->request->filter('configs', [], FILTER_SANITIZE_STRING);
            $fbSettings = array_merge($fbSettings, $fbAppId);

            if ($this->sm->set('facebook', $fbSettings)
                && $this->sm->set('comments_config', $configs)
            ) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Facebook configuration saved successfully')
                );

                return $this->redirect($this->generateUrl('admin_comments_facebook'));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('There was an error while saving the Facebook comments module configuration')
                );
            }

            return $this->redirect($this->generateUrl('admin_comments_facebook_config'));
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
        return $this->render(
            'comment/list.tpl',
            [ 'statuses' => $this->statuses ]
        );
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

        if (!is_null($comment->id)) {
            $comment->content = new \Content($comment->content_id);
            $languageData     = $this->getLocaleData('frontend', $request, true);
            return $this->render(
                'comment/new.tpl',
                [
                    'comment'       => $comment,
                    'language_data' => $languageData
                ]
            );
        }

        $this->get('session')->getFlashBag()->add(
            'error',
            sprintf(_('Comment with id "%d" doesn\'t exists.'), $id)
        );

        return $this->redirect($this->generateUrl('admin_comments_list'));
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

            return $this->redirect($this->generateUrl('admin_comments_list'));
        }

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Comment data sent not valid.")
            );
            return $this->redirect(
                $this->generateUrl(
                    'admin_comment_show',
                    [ 'id' => $id ]
                )
            );
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

        return $this->redirect(
            $this->generateUrl(
                'admin_comment_show',
                [ 'id' => $id ]
            )
        );
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
        if ('POST' == $request->getMethod()) {
            $configs = $request->request->filter('configs', [], FILTER_SANITIZE_STRING);

            $defaultConfigs = [
                'moderation'      => false,
                'with_comments'   => false,
                'number_elements' => 10,
            ];

            $configs = array_merge($defaultConfigs, $configs);

            if ($this->sm->set('comments_config', $configs)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Settings saved.')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('There was an error while saving the settings')
                );
            }

            return $this->redirect($this->generateUrl('admin_comments_config'));
        } else {
            $configs = $this->sm->get('comments_config');

            return $this->render('comment/config.tpl', [ 'configs' => $configs ]);
        }
    }
}
