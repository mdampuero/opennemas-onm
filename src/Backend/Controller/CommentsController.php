<?php
/**
 * Handles the actions for comments
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for comments
 *
 * @package Backend_Controllers
 **/
class CommentsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return Response the response object
     **/
    public function init()
    {
        $this->statuses = [
            [ 'title' => _('All'), 'value' => -1 ],
            [ 'title' => _('Accepted'), 'value' => \Comment::STATUS_ACCEPTED ],
            [ 'title' => _('Rejected'), 'value' => \Comment::STATUS_REJECTED ],
            [ 'title' => _('Pending'), 'value' => \Comment::STATUS_PENDING ],
        ];

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
        $commentSystem = s::get('comment_system');

        switch ($commentSystem) {
            case 'onm':
                return $this->redirect($this->generateUrl('admin_comments_list'));
                break;

            case 'disqus':
                return $this->redirect($this->generateUrl('admin_comments_disqus'));
                break;

            case 'facebook':
                return $this->redirect($this->generateUrl('admin_comments_facebook'));
                break;

            default:
                return $this->render('comment/select_module.tpl');
                break;
        }

        return $this->render('comment/select_module.tpl');
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
                $this->get('setting_repository')->set('comment_system', 'onm');
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Now you are using the Opennemas comment system.")
                );
                return $this->redirect($this->generateUrl('admin_comments_config'));
                break;

            case 'disqus':
                return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
                break;

            case 'facebook':
                $this->get('setting_repository')->set('comment_system', 'facebook');
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _("Now you are using the Facebook comment system.")
                );
                return $this->redirect($this->generateUrl('admin_comments_facebook_config'));
                break;

            case 'reset':
                return $this->render('comment/select_module.tpl');
                break;

            default:
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Comment data sent not valid.")
                );
                return $this->redirect($this->generateUrl('admin_comments'));
                break;
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
        $disqusShortName = s::get('disqus_shortname');
        $disqusSecretKey = s::get('disqus_secret_key');

        // Check if module is configured, if not redirect to configuration form
        if (!$disqusShortName || !$disqusSecretKey) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please provide your Disqus configuration to start to use your Disqus Comments module')
            );

            return $this->redirect($this->generateUrl('admin_comments_disqus_config'));
        }

        return $this->render(
            'comment/disqus/list.tpl',
            array(
                'disqus_shortname'  => $disqusShortName,
                'disqus_secret_key' => $disqusSecretKey,
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
    public function configDisqusAction(Request $request)
    {
        if ($request->getMethod() != 'POST') {
            $disqusShortName = s::get('disqus_shortname');
            $disqusSecretKey = s::get('disqus_secret_key');

            return $this->render(
                'comment/disqus/config.tpl',
                array(
                    'shortname' => $disqusShortName,
                    'secretKey' => $disqusSecretKey,
                )
            );
        } else {
            $shortname = $request->request->filter('shortname', null, FILTER_SANITIZE_STRING);
            $secretKey = $request->request->filter('secret_key', null, FILTER_SANITIZE_STRING);

            if (s::set('disqus_shortname', $shortname) && s::set('disqus_secret_key', $secretKey)) {
                s::set('comment_system', 'disqus');
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
        $fbSettings = s::get('facebook');

        return $this->render(
            'comment/facebook/list.tpl',
            array(
                'fb_app_id'  => $fbSettings['api_key'],
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
    public function configFacebookAction(Request $request)
    {
        $fbSettings = s::get('facebook');

        if ($request->getMethod() != 'POST') {
            $fbAppId = $fbSettings['api_key'];

            return $this->render(
                'comment/facebook/config.tpl',
                array(
                    'fb_app_id'  => $fbAppId,
                )
            );
        } else {
            $fbAppId    = $request->request->filter('facebook', null, FILTER_SANITIZE_STRING);
            $fbSettings = array_merge($fbSettings, $fbAppId);

            if (s::set('facebook', $fbSettings)) {
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
            array(
                'statuses' => $this->statuses,
            )
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
        $id = $request->query->getDigits('id');
        $comment = new \Comment($id);

        if (!is_null($comment->id)) {
            $comment->content = new \Content($comment->content_id);

            return $this->render(
                'comment/new.tpl',
                array('comment' => $comment)
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
            return $this->redirect($this->generateUrl('admin_comment_show', array('id' => $id)));
        }

        $data = array(
            'status' => $request->request->filter('status'),
            'body'   => $request->request->filter('body', ''),
        );

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

        return $this->redirect($this->generateUrl('admin_comment_show', array('id' => $id)));

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
            $configs = $request->request->filter('configs', array(), FILTER_SANITIZE_STRING);

            $defaultConfigs = array(
                'moderation'      => false,
                'with_comments'   => false,
                'number_elements' => 10,
            );
            $configs = array_merge($defaultConfigs, $configs);

            if (s::set('comments_config', $configs)) {
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
            $configs = s::get('comments_config');

            return $this->render('comment/config.tpl', array('configs' => $configs,));
        }
    }
}
