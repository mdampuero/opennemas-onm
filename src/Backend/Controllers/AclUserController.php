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
namespace Backend\Controllers;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the system users
 *
 * @package Backend_Controllers
 **/
class AclUserController extends Controller
{

    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
    }
    /**
     * Show a paginated list of backend users
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        $this->checkAclOrForward('USER_ADMIN');

        $page   =  $request->query->getDigits('page', 1);
        $filter = array(
            'name'  => $request->query->filter('name', ''),
            'group' => $request->query->getDigits('group', ''),
            'type'  => $request->query->getDigits('type', ''),
        );

        if (!$_SESSION['isMaster']) {
            $filter ['base'] = 'fk_user_group != 4';
        }

        $itemsPerPage = s::get('items_per_page') ?: 20;

        // Fetch users paginated and filtered
        $user           = new \User();
        $searchCriteria = $user->buildFilter($filter);
        $userManager    = $this->get('user_repository');
        $usersCount     = $userManager->count($searchCriteria);
        $users          = $userManager->findBy(
            $searchCriteria,
            'name',
            $itemsPerPage,
            $page
        );

        $er = $this->get('entity_repository');
        foreach ($users as &$user) {
            $user->photo = $er->find('Photo', $user->avatar_img_id);
        }

        $userGroup = new \UserGroup();
        $groups    = $userGroup->find();

        $groupsOptions = array();
        $groupsOptions[] = _('--All--');
        foreach ($groups as $cat) {
            $groupsOptions[$cat->id] = $cat->name;
        }

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 4,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $usersCount,
                'fileName'    => $this->generateUrl(
                    'admin_acl_user',
                    array(
                        'name'  => $filter['name'],
                        'group' => $filter['group'],
                        'type'  => $filter['type'],
                    )
                ).'&page=%d',
            )
        );

        return $this->render(
            'acl/user/list.tpl',
            array(
                'users'         => $users,
                'user_groups'   => $groups,
                'groupsOptions' => $groupsOptions,
                'pagination'    => $pagination,
            )
        );
    }

    /**
     * Shows the user information given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        //user can modify his data
        $idRAW = $request->query->filter('id', '', FILTER_SANITIZE_STRING);
        if ($idRAW === 'me') {
            $id = $_SESSION['userid'];
        } else {
            $id = $request->query->getDigits('id');
        }

        // Check if the user is the same as the one that we want edit or
        // if we have permissions for editting other user information.
        if ($id != $_SESSION['userid']) {
            $this->checkAclOrForward('USER_UPDATE');
        }

        $ccm = new \ContentCategoryManager();

        $user = new \User($id);
        if (is_null($user->id)) {
            m::add(sprintf(_("Unable to find the user with the id '%d'"), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_acl_user'));
        }

        // Fetch user photo if exists
        if (!empty($user->avatar_img_id)) {
            $user->photo = new \Photo($user->avatar_img_id);
        }

        $user->meta = $user->getMeta();

        if ($user->meta && array_key_exists('paywall_time_limit', $user->meta)) {
            $user->meta['paywall_time_limit'] = new \DateTime(
                $user->meta['paywall_time_limit'],
                new \DateTimeZone('UTC')
            );
        }

        $userGroup = new \UserGroup();
        $tree = $ccm->getCategoriesTree();
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
     **/
    public function updateAction(Request $request)
    {
        $userId = $request->query->getDigits('id');
        $action = $request->request->filter('action', 'update', FILTER_SANITIZE_STRING);

        if ($userId != $_SESSION['userid']) {
            $this->checkAclOrForward('USER_UPDATE');
        }

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
            'sessionexpire'   => $request->request->getDigits('sessionexpire'),
            'id_user_group'   => $request->request->get('id_user_group', array()),
            'ids_category'    => $request->request->get('ids_category', array()),
            'avatar_img_id'   => $request->request->filter('avatar', null, FILTER_SANITIZE_STRING),
        );

        $file = $request->files->get('avatar');
        $user = new \User($userId);

        try {
            // Upload user avatar if exists
            if (!is_null($file)) {
                $photoId = $user->uploadUserAvatar($file, \Onm\StringUtils::get_title($data['name']));
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

                m::add(_('User data updated successfully.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the user with that information'), m::ERROR);
            }
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        if ($action == 'validate') {
            $redirectUrl = $this->generateUrl('admin_acl_user_show', array('id' => $userId));
        } else {
            // If a regular user is upating him/her information redirect to welcome page
            if (($userId == $_SESSION['userid'])
                && !\Acl::check('USER_UPDATE')
            ) {
                $redirectUrl = $this->generateUrl('admin_welcome');
            } else {
                $redirectUrl = $this->generateUrl('admin_acl_user');
            }
        }

        return $this->redirect($redirectUrl);
    }

    /**
     * Creates an user give some information
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function createAction(Request $request)
    {
        $this->checkAclOrForward('USER_CREATE');

        $action = $request->request->filter('action', null, FILTER_SANITIZE_STRING);

        $user = new \User();

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
                'activated'       => 1,
                'type'            => $request->request->filter('type', '0', FILTER_SANITIZE_STRING),
                'deposit'         => 0,
                'token'           => null,
            );

            $file = $request->files->get('avatar');

            try {
                // Upload user avatar if exists
                if (!is_null($file)) {
                    $photoId = $user->uploadUserAvatar($file, \Onm\StringUtils::get_title($data['name']));
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

                    m::add(_('User created successfully.'), m::SUCCESS);

                    return $this->redirect(
                        $this->generateUrl(
                            'admin_acl_user_show',
                            array('id' => $user->id)
                        )
                    );
                } else {
                    m::add(_('Unable to create the user with that information'), m::ERROR);
                }
            } catch (\Exception $e) {
                m::add($e->getMessage(), m::ERROR);
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
     **/
    public function deleteAction(Request $request)
    {
        $this->checkAclOrForward('USER_DELETE');

        $userId = $request->query->getDigits('id');

        if (!is_null($userId)) {
            $user = new \User();
            $user->delete($userId);
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
     **/
    public function batchDeleteAction(Request $request)
    {
        $this->checkAclOrForward('USER_DELETE');

        $selected = $request->query->get('selected');

        if (count($selected) > 0) {
            $user = new \User();
            foreach ($selected as $userId) {
                $user->delete((int) $userId);
            }
            m::add(sprintf(_('You have deleted %d users.'), count($selected)), m::SUCCESS);
        } else {
            m::add(_('You haven\'t selected any user to delete.'), m::ERROR);
        }

        if (strpos($request->server->get('HTTP_REFERER'), 'users/frontend') !== false) {
            return $this->redirect($this->generateUrl('admin_acl_user_front'));
        } else {
            return $this->redirect($this->generateUrl('admin_acl_user'));
        }
    }

    /**
     * Returns a connected users panel
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function connectedUsersAction(Request $request)
    {
        $this->checkAclOrForward('BACKEND_ADMIN');

        $sessions = $GLOBALS['Session']->getSessions();

        return $this->render(
            'acl/panel/show_panel.ajax.html',
            array('users' => $sessions)
        );
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

        foreach ($request->query as $key => $value) {
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
                    $this->get('logger')->notice(
                        "Unable to send the recover password email for the "
                        ."user {$user->id}: ".$e->getMessage()
                    );

                    m::add(_('Unable to send your recover password email. Please try it later.'), m::ERROR);
                }

            } else {
                m::add(_('Unable to find an user with that email.'), m::ERROR);
            }

            // Display form
            return $this->render('login/recover_pass.tpl', array('token' => $token));
        }
    }

    /**
     * Shows the form for recovering the username of a user and
     * sends the mail to the user
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function recoverUsernameAction(Request $request)
    {
        // Setup view
        $this->view->assign('version', \Onm\Common\Version::VERSION);
        $this->view->assign('languages', $this->container->getParameter('available_languages'));
        $this->view->assign('current_language', \Application::$language);

        if ('POST' != $request->getMethod()) {
            return $this->render('login/recover_username.tpl');
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

                $tplMail = new \TemplateAdmin(TEMPLATE_ADMIN);
                $tplMail->caching = 0;

                $mailSubject = sprintf(_('Username reminder for %s'), s::get('site_title'));
                $mailBody = $tplMail->fetch(
                    'login/emails/recoverusername.tpl',
                    array(
                        'user' => $user,
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

                    $url = $this->generateUrl('admin_login_form', array(), true);

                    $this->view->assign(
                        array(
                            'mailSent' => true,
                            'user' => $user,
                            'url' => $url
                        )
                    );
                } catch (\Exception $e) {
                    // Log this error
                    $this->get('logger')->notice(
                        "Unable to send the recover password email for the "
                        ."user {$user->id}: ".$e->getMessage()
                    );

                    m::add(_('Unable to send your recover username email. Please try it later.'), m::ERROR);
                }

            } else {
                m::add(_('Unable to find an user with that email.'), m::ERROR);
            }

            // Display form
            return $this->render('login/recover_username.tpl');
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
                m::add(
                    _(
                        'Unable to find the password reset request. '
                        .'Please check the url we sent you in the email.'
                    ),
                    m::ERROR
                );
                $this->view->assign('userNotValid', true);
            } else {
                $this->view->assign(
                    array(
                        'user' => $user
                    )
                );
            }
        } else {
            $password       = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
            $passwordVerify = $request->request->filter('password-verify', null, FILTER_SANITIZE_STRING);

            if ($password == $passwordVerify && !empty($password) && !is_null($user)) {
                $user->updateUserPassword($user->id, $password);
                $user->updateUserToken($user->id, null);

                m::add(_('Password successfully updated'), m::SUCCESS);

                return $this->redirect($this->generateUrl('admin_login_form'));

            } elseif ($password != $passwordVerify) {
                m::add(_('Password and confirmation must be equal.'), m::ERROR);
            } else {
                m::add(
                    _(
                        'Unable to find the password reset request. '
                        .'Please check the url we sent you in the email.'
                    ),
                    m::ERROR
                );
            }

        }

        return $this->render('login/regenerate_pass.tpl', array('token' => $token, 'user' => $user));

    }
}
