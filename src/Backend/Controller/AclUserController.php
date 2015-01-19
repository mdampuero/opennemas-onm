<?php
/**
 * Handles the system users
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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the system users
 *
 * @package Backend_Controllers
 **/
class AclUserController extends Controller
{
    /**
     * Show a paginated list of backend users
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('USER_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $userGroup = new \UserGroup();
        $groups    = $userGroup->find();

        $groupsOptions = array();
        foreach ($groups as $cat) {
            $groupsOptions[$cat->id] = $cat->name;
        }

        // Get max users from settings
        $maxUsers = s::get('max_users');
        // Check total allowed users before creating new one
        $createEnabled = true;
        if ($maxUsers > 0) {
            $createEnabled = \User::getTotalUsersRemaining($maxUsers);
        }

        if (!$createEnabled) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                _('You have reach the maximun users allowed. If you want to create more users, please contact us.')
            );
        }

        return $this->render(
            'acl/user/list.tpl',
            array(
                'user_groups'     => $groups,
                'groupsOptions'   => $groupsOptions,
                'createEnabled'   => $createEnabled,
            )
        );
    }

    /**
     * Shows the user information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('USER_UPDATE')")
     **/
    public function showAction(Request $request)
    {
        $session = $request->getSession();
        $session->set(
            '_security.backend.target_path',
            $this->generateUrl('admin_login_callback')
        );

        // User can modify his data
        $idRAW = $request->query->filter('id', '', FILTER_SANITIZE_STRING);
        if ($idRAW === 'me') {
            $id = $_SESSION['userid'];
        } else {
            $id = $request->query->getDigits('id');
        }

        // Check if the user is the same as the one that we want edit or
        // if we have permissions for editing other user information.
        if (array_key_exists('userid', $_SESSION) && $id != $_SESSION['userid']) {
            if (false === Acl::check('USER_UPDATE')) {
                throw new AccessDeniedException();
            }
        }

        $ccm = new \ContentCategoryManager();

        $user = $this->get('user_repository')->find($id);
        if (is_null($user->id)) {
            $request->getSession()->getFlashBag()->add(
                'error',
                sprintf(_("Unable to find the user with the id '%d'"), $id)
            );

            return $this->redirect($this->generateUrl('admin_acl_user'));
        }

        // Fetch user photo if exists
        if (!empty($user->avatar_img_id)) {
            $user->photo = $this->get('entity_repository')->find('Photo', $user->avatar_img_id);
        }

        $user->meta = $user->getMeta();

        if ($user->meta && array_key_exists('paywall_time_limit', $user->meta)) {
            $user->meta['paywall_time_limit'] = new \DateTime(
                $user->meta['paywall_time_limit'],
                new \DateTimeZone('UTC')
            );
        }

        $userGroup = new \UserGroup();
        $tree      = $ccm->getCategoriesTree();
        $languages = $this->container->getParameter('available_languages');
        $languages = array_merge(array('default' => _('Default system language')), $languages);

        // Get minimum password level
        $defaultLevel  = $this->container->getParameter('password_min_level');
        $instanceLevel = s::get('pass_level');
        $minPassLevel  = ($instanceLevel)? $instanceLevel: $defaultLevel;

        return $this->render(
            'acl/user/new.tpl',
            array(
                'user'                      => $user,
                'user_groups'               => $userGroup->find(),
                'languages'                 => $languages,
                'content_categories'        => $tree,
                'content_categories_select' => $user->getAccessCategoryIds(),
                'min_pass_level'            => $minPassLevel,
            )
        );
    }

    /**
     * Handles the update action for a user given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('USER_UPDATE')")
     **/
    public function updateAction(Request $request)
    {
        $userId = $request->query->getDigits('id');

        if ($userId != $_SESSION['userid']) {
            if (false === Acl::check('USER_UPDATE')) {
                throw new AccessDeniedException();
            }
        }

        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("The data send by the user is not valid.")
            );

            return $this->redirect($this->generateUrl('admin_acl_user_show', array('id' => $userId)));
        }

        $user = new \User($userId);

        $data = array(
            'id'              => $userId,
            'username'        => $request->request->filter('login', null, FILTER_SANITIZE_STRING),
            'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
            'password'        => $request->request->filter('password', null, FILTER_SANITIZE_STRING),
            'passwordconfirm' => $request->request->filter('passwordconfirm', null, FILTER_SANITIZE_STRING),
            'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
            'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'type'            => $request->request->filter('type', '0', FILTER_SANITIZE_STRING),
            'activated'       => $request->request->filter('activated', '1', FILTER_SANITIZE_STRING),
            'sessionexpire'   => $request->request->getDigits('sessionexpire'),
            'id_user_group'   => $request->request->get('id_user_group', $user->id_user_group),
            'ids_category'    => $request->request->get('ids_category'),
            'avatar_img_id'   => $request->request->filter('avatar', null, FILTER_SANITIZE_STRING),
        );

        $file = $request->files->get('avatar');

        // Get max users from settings
        $maxUsers = s::get('max_users');
        // Check total activated users remaining before updating
        $updateEnabled = true;
        if ($data['activated'] == '1' && $maxUsers > 0) {
            $updateEnabled = \User::getTotalActivatedUsersRemaining($maxUsers);
        }

        if ($updateEnabled) {
            try {
                // Upload user avatar if exists
                if (!is_null($file)) {
                    $photoId = $user->uploadUserAvatar($file, \Onm\StringUtils::getTitle($data['name']));
                    $data['avatar_img_id'] = $photoId;
                } elseif (($data['avatar_img_id']) == 1) {
                    $data['avatar_img_id'] = $user->avatar_img_id;
                }

                // Process data
                if ($user->update($data)) {
                    // Set all usermeta information (twitter, rss, language)
                    $meta = $request->request->get('meta');
                    foreach ($meta as $key => $value) {
                        $user->setMeta(array($key => $value));
                    }

                    // Set usermeta paywall time limit
                    $paywallTimeLimit = $request->request->filter('paywall_time_limit', '', FILTER_SANITIZE_STRING);
                    if (!empty($paywallTimeLimit)) {
                        $time = \DateTime::createFromFormat('Y-m-d H:i:s', $paywallTimeLimit);
                        $time->setTimeZone(new \DateTimeZone('UTC'));

                        $user->setMeta(array('paywall_time_limit' => $time->format('Y-m-d H:i:s')));
                    }

                    if ($user->id == $_SESSION['userid']) {
                        $_SESSION['user_language'] = $meta['user_language'];
                    }

                    // Clear caches
                    $this->dispatchEvent('user.update', array('user' => $user));
                    // Check if is an author and delete caches
                    if (in_array('3', $data['id_user_group'])) {
                        $this->dispatchEvent('author.update', array('id' => $userId));
                    }

                    $request->getSession()->getFlashBag()->add('success', _('User data updated successfully.'));
                } else {
                    $request->getSession()->getFlashBag()->add(
                        'error',
                        _('Unable to update the user with that information')
                    );
                }
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add('error', $e->getMessage());
            }
        } else {
            $request->getSession()->getFlashBag()->add(
                'error',
                _('Unable to change user backend access. You have reached the max number of users.')
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_acl_user_show', array('id' => $userId))
        );
    }

    /**
     * Creates an user give some information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('USER_CREATE')")
     **/
    public function createAction(Request $request)
    {
        $user = new \User();

        // Get max users from settings
        $maxUsers = s::get('max_users');
        // Check total allowed users before creating new one
        $createEnabled = true;
        if ($maxUsers > 0) {
            $createEnabled = \User::getTotalUsersRemaining($maxUsers);
        }

        if (!$createEnabled) {
            $request->getSession()->getFlashBag()->add(
                'notice',
                _('You have reach the maximun users allowed. If you want to create more users, please contact us.')
            );

            return $this->redirect($this->generateUrl('admin_acl_user'));
        }

        if ($request->getMethod() == 'POST') {
            $data = array(
                'username'        => $request->request->filter('login', null, FILTER_SANITIZE_STRING),
                'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
                'password'        => $request->request->filter('password', null, FILTER_SANITIZE_STRING),
                'passwordconfirm' => $request->request->filter('passwordconfirm', null, FILTER_SANITIZE_STRING),
                'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'sessionexpire'   => $request->request->getDigits('sessionexpire'),
                'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
                'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
                'id_user_group'   => $request->request->get('id_user_group', array()),
                'ids_category'    => $request->request->get('ids_category', array()),
                'activated'       => $request->request->filter('activated', '1', FILTER_SANITIZE_STRING),
                'type'            => $request->request->filter('type', '0', FILTER_SANITIZE_STRING),
                'deposit'         => 0,
                'token'           => null,
            );

            $file = $request->files->get('avatar');

            try {
                // Upload user avatar if exists
                if (!is_null($file)) {
                    $photoId = $user->uploadUserAvatar($file, \Onm\StringUtils::getTitle($data['name']));
                    $data['avatar_img_id'] = $photoId;
                } else {
                    $data['avatar_img_id'] = 0;
                }

                if ($user->create($data)) {
                    // Set all usermeta information (twitter, rss, language)
                    $meta = $request->request->get('meta');
                    foreach ($meta as $key => $value) {
                        $user->setMeta(array($key => $value));
                    }

                    // Set usermeta paywall time limit
                    $paywallTimeLimit = $request->request->filter('paywall_time_limit', '', FILTER_SANITIZE_STRING);
                    if (!empty($paywallTimeLimit)) {
                        $time = \DateTime::createFromFormat('Y-m-d H:i:s', $paywallTimeLimit);
                        $time->setTimeZone(new \DateTimeZone('UTC'));

                        $user->setMeta(array('paywall_time_limit' => $time->format('Y-m-d H:i:s')));
                    }

                    $request->getSession()->getFlashBag()->add(
                        'success',
                        _('User created successfully.')
                    );

                    return $this->redirect(
                        $this->generateUrl(
                            'admin_acl_user_show',
                            array('id' => $user->id)
                        )
                    );
                } else {
                    $request->getSession()->getFlashBag()->add(
                        'error',
                        _('Unable to create the user with that information')
                    );
                }
            } catch (\Exception $e) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    $e->getMessage()
                );
            }
        }

        $ccm = \ContentCategoryManager::get_instance();
        $userGroup = new \UserGroup();
        $tree = $ccm->getCategoriesTree();

        $languages = $this->container->getParameter('available_languages');
        $languages = array_merge(array('default' => _('Default system language')), $languages);

        return $this->render(
            'acl/user/new.tpl',
            array(
                'user'                      => $user,
                'user_groups'               => $userGroup->find(),
                'content_categories'        => $tree,
                'languages'                 => $languages,
                'content_categories_select' => $user->getAccessCategoryIds(),
            )
        );
    }

    /**
     * Deletes a user given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('USER_DELETE')")
     **/
    public function deleteAction(Request $request)
    {
        $userId = $request->query->getDigits('id');

        if (!is_null($userId)) {
            $user = new \User();
            $user->delete($userId);

            $request->getSession()->getFlashBag()->add(
                'success',
                sprintf(_('Successfully deleted user with id "%d".'), $userId)
            );

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($this->generateUrl('admin_acl_user'));
            } else {
                return new Response('ok');
            }
        }
    }

    /**
     * Deletes multiple users at once given their ids
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('USER_DELETE')")
     **/
    public function batchDeleteAction(Request $request)
    {
        $selected = $request->query->get('selected');

        if (count($selected) > 0) {
            $user = new \User();
            foreach ($selected as $userId) {
                $user->delete((int) $userId);
            }

            $request->getSession()->getFlashBag()->add(
                'success',
                sprintf(_('You have deleted %d users.'), count($selected))
            );
        } else {
            $request->getSession()->getFlashBag()->add(
                'error',
                _('You haven\'t selected any user to delete.')
            );
        }

        if (strpos($request->server->get('HTTP_REFERER'), 'users/frontend') !== false) {
            return $this->redirect($this->generateUrl('admin_acl_user_front'));
        } else {
            return $this->redirect($this->generateUrl('admin_acl_user'));
        }
    }

    /**
     * Sets a user configuration given the meta key and the meta value
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function setMetaAction(Request $request)
    {
        $user = new \User($_SESSION['userid']);

        foreach (array_keys($request->query) as $key) {
            if (!preg_match('@^_@', $key)) {
                $settings[$key] = $request->query->filter($key, null, FILTER_SANITIZE_STRING);
            }
        }

        $setted = $user->setMeta($settings);
        if ($setted) {
            $message = 'Done';
            $httpCode = 200;
        } else {
            $message = 'Failed';
            $httpCode = 500;
        }

        return new Response($message, $httpCode);
    }

    /**
     * Toogle an user state to enabled/disabled
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("has_role('USER_ADMIN')")
     **/
    public function toogleEnabledAction(Request $request)
    {
        $userId = $request->query->getDigits('id');

        if (!is_null($userId)) {
            $user = new \User($userId);

            if ($user->activated == 1) {
                $user->deactivateUser($userId);
            } else {
                $user->activateUser($userId);
            }

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($this->generateUrl('admin_acl_user'));
            }
        }
    }


    /**
     * Shows the form for recovering the pass of a user and
     * sends the mail to the user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function recoverPasswordAction(Request $request)
    {
        // Setup view
        $this->view->assign('version', \Onm\Common\Version::VERSION);
        $this->view->assign('languages', $this->container->getParameter('available_languages'));
        $this->view->assign('current_language', \Application::$language);

        if ('POST' != $request->getMethod()) {
            return $this->render('login/recover_pass.tpl');
        } else {
            $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);

            // Get user by email
            $user = new \User();
            $user->findByEmail($email);

            // If e-mail exists in DB
            if (!is_null($user->id)) {
                // Generate and update user with new token
                $token = md5(uniqid(mt_rand(), true));
                $user->updateUserToken($user->id, $token);

                $url = $this->generateUrl('admin_acl_user_reset_pass', array('token' => $token), true);

                $tplMail = new \TemplateAdmin(TEMPLATE_ADMIN);
                $tplMail->caching = 0;

                $mailSubject = sprintf(_('Password reminder for %s'), s::get('site_title'));
                $mailBody = $tplMail->fetch(
                    'login/emails/recoverpassword.tpl',
                    array(
                        'user' => $user,
                        'url'  => $url,
                    )
                );

                //  Build the message
                $message = \Swift_Message::newInstance();
                $message
                    ->setSubject($mailSubject)
                    ->setBody($mailBody, 'text/plain')
                    ->setTo($user->email)
                    ->setFrom(array('no-reply@postman.opennemas.com' => s::get('site_name')));

                try {
                    $mailer = $this->get('mailer');
                    $mailer->send($message);

                    $this->view->assign(
                        array(
                            'mailSent' => true,
                            'user' => $user
                        )
                    );
                } catch (\Exception $e) {
                    // Log this error
                    $this->get('application.log')->notice(
                        "Unable to send the recover password email for the "
                        ."user {$user->id}: ".$e->getMessage()
                    );

                    $request->getSession()->getFlashBag()->add(
                        'error',
                        _('Unable to send your recover password email. Please try it later.')
                    );
                }

            } else {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    _('Unable to find an user with that email.')
                );
            }

            // Display form
            return $this->render('login/recover_pass.tpl', array('token' => $token));
        }
    }

    /**
     * Regenerates the pass for a user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function regeneratePasswordAction(Request $request)
    {
        // Setup view
        $this->view->assign('version', \Onm\Common\Version::VERSION);
        $this->view->assign('languages', $this->container->getParameter('available_languages'));
        $this->view->assign('current_language', \Application::$language);

        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        $user = new \User();
        $user = $user->findByToken($token);

        if ('POST' !== $request->getMethod()) {
            if (empty($user->id)) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    _('Unable to find the password reset request.  Please check the url we sent you in the email.')
                );

                $this->view->assign('userNotValid', true);
                return $this->redirect($this->generateUrl('admin_login'));
            }

            $this->view->assign('user', $user);
        } else {
            $password       = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
            $passwordVerify = $request->request->filter('password-verify', null, FILTER_SANITIZE_STRING);

            if ($password == $passwordVerify && !empty($password) && !is_null($user)) {
                $user->updateUserPassword($user->id, $password);
                $user->updateUserToken($user->id, null);

                $request->getSession()->getFlashBag()->add('success', _('Password successfully updated'));

                return $this->redirect($this->generateUrl('admin_login'));

            } elseif ($password != $passwordVerify) {
                $request->getSession()->getFlashBag()->add('error', _('Password and confirmation must be equal.'));
            } else {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    _('Unable to find the password reset request.  Please check the url we sent you in the email.')
                );

                return $this->redirect($this->generateUrl('admin_login'));
            }

        }

        return $this->render('login/regenerate_pass.tpl', array('token' => $token, 'user' => $user));
    }

    /**
     * Displays the facebook iframe to connect accounts.
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The user's id.
     * @return Response          The response object.
     */
    public function socialAction(Request $request, $id, $resource)
    {
        $user = $this->get('user_repository')->find($id);

        $session = $request->getSession();
        $session->set(
            '_security.backend.target_path',
            $this->generateUrl('admin_login_callback')
        );

        if (!$user) {
            return new Response();
        }

        $resourceId = $user->getMeta($resource . '_id');

        $connected = false;
        if ($resourceId) {
            $connected = true;
        }

        if ($resource == 'facebook') {
            $resourceName = 'Facebook';
        } else {
            $resourceName = 'Twitter';
        }

        $this->dispatchEvent('social.disconnect', array('user' => $user));

        return $this->render(
            'acl/user/social.tpl',
            array(
                'current_user_id' => $this->getUser()->id,
                'connected'       => $connected,
                'resource_id'     => $resourceId,
                'resource'        => $resource,
                'resource_name'   => $resourceName,
                'user'            => $user,
            )
        );
    }

    /**
     * Disconnects the accounts.
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The user's id.
     * @return Response          The response object.
     */
    public function disconnectAction($id, $resource)
    {
        $user = $this->get('user_repository')->find($id);

        if (!$user) {
            return new Response();
        }

        $resourceId = $user->deleteMetaKey($user->id, $resource . '_id');
        $resourceId = $user->deleteMetaKey($user->id, $resource . '_email');
        $resourceId = $user->deleteMetaKey($user->id, $resource . '_token');
        $resourceId = $user->deleteMetaKey($user->id, $resource . '_realname');

        $this->dispatchEvent('social.connect', array('user' => $user));

        return $this->redirect(
            $this->generateUrl(
                'admin_acl_user_social',
                array('id' => $id, 'resource' => $resource)
            )
        );
    }
}
