<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;
use Onm\Message as m;

/**
 * Handles the system manager users
 *
 * @package Manager_Controllers
 * @author OpenHost Developers <developers@openhost.es>
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
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

    /**
     * Show a paginated list of users
     *
     * @return Response the response object
     **/
    public function listAction(Request $request)
    {
        // TODO : Check Acl in manager
        // $this->checkAclOrForward('USER_ADMIN');

        $filter    = $request->query->get('filter', array());

        if (!$_SESSION['isMaster']) {
            $filter ['base'] = 'fk_user_group != 4';
        }

        $user      = new \User();
        $users     = $user->getUsers($filter, ' ORDER BY username ');

        $userGroup = new \UserGroup();
        $groups     = $userGroup->find();

        $groupsOptions = array();
        $groupsOptions[] = _('--All--');
        foreach ($groups as $cat) {
            $groupsOptions[$cat->id] = $cat->name;
        }

        return $this->render(
            'acl/user/list.tpl',
            array(
                'users'         => $users,
                'user_groups'   => $groups,
                'groupsOptions' => $groupsOptions,
            )
        );
    }

    /**
     * Shows the user information given its id
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

        // TODO : Check Acl in manager
        // // Check if the user is the same as the one that we want edit or
        // // if we have permissions for editting other user information.
        // if ($id != $_SESSION['userid']) {
        //     $this->checkAclOrForward('USER_UPDATE');
        // }

        $user = new \User($id);
        if (is_null($user->id)) {
            m::add(sprintf(_("Unable to find the user with the id '%d'"), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_acl_user'));
        }

        $user->meta = array();
        $user->meta['user_language'] = $user->getMeta('user_language') ?: 'default';

        $userGroup = new \UserGroup();
        $languages = $this->container->getParameter('available_languages');
        $languages = array_merge(array('default' => _('Default system language')), $languages);

        return $this->render(
            'acl/user/new.tpl',
            array(
                'user'                      => $user,
                'user_groups'               => $userGroup->find(),
                'languages'                 => $languages,
            )
        );
    }

    /**
     * Handles the update action for a user given its id
     *
     * After finish the task redirects the user to the proper place
     *
     * @return Response the response object
     **/
    public function updateAction(Request $request)
    {
        $userId = $request->query->getDigits('id');
        $action = $request->request->filter('action', 'update', FILTER_SANITIZE_STRING);

        // TODO : Check Acl in manager
        // if ($userId != $_SESSION['userid']) {
        //     $this->checkAclOrForward('USER_UPDATE');
        // }

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
            'id_user_group'   => $request->request->getDigits('id_user_group'),
            'ids_category'    => $request->request->get('ids_category'),
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
        } catch (FileException $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        if ($action == 'validate') {
            $redirectUrl = $this->generateUrl('manager_acl_user_show', array('id' => $userId));
        } else {
            // TODO : Check Acl in manager
            // If a regular user is upating him/her information
            // redirect to welcome page
            if (($userId == $_SESSION['userid'])
                && !\Acl::check('USER_UPDATE')
            ) {
                $redirectUrl = $this->generateUrl('manager_welcome');
            } else {
                $redirectUrl = $this->generateUrl('manager_acl_user');
            }
        }

        return $this->redirect($redirectUrl);
    }

    /**
     * Creates an user give some information
     *
     * @return string the response string
     **/
    public function createAction(Request $request)
    {
        // TODO : Check Acl in manager
        // $this->checkAclOrForward('USER_CREATE');

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
                'id_user_group'   => $request->request->getDigits('id_user_group'),
                'ids_category'    => $request->request->get('ids_category'),
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

        $userGroup = new \UserGroup();

        $languages = $this->container->getParameter('available_languages');
        $languages = array_merge(array('default' => _('Default system language')), $languages);

        return $this->render(
            'acl/user/new.tpl',
            array(
                'user'                      => $user,
                'user_groups'               => $userGroup->find(),
                'languages'                 => $languages,
            )
        );
    }

    /**
     * Deletes a user given its id
     *
     * @return string the response string
     **/
    public function deleteAction(Request $request)
    {
        // TODO : Check Acl in manager
        // $this->checkAclOrForward('USER_DELETE');

        $userId = $request->query->getDigits('id');

        if (!is_null($userId)) {
            $user = new \User();
            if ($user->delete($userId)) {
                $user->deleteMeta($userId);
                m::add(_('You have deleted selected user.'), m::SUCCESS);
            }
            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($this->generateUrl('manager_acl_user'));
            }
        }
    }

    /**
     * Deletes multiple users at once given their ids
     *
     * @return string the string resposne
     **/
    public function batchDeleteAction(Request $request)
    {
        // TODO : Check Acl in manager
        // $this->checkAclOrForward('USER_DELETE');

        $selected = $request->query->get('selected');

        if (count($selected) > 0) {
            $user = new \User();
            foreach ($selected as $userId) {
                if ($user->delete((int) $userId)) {
                    $user->deleteMeta((int) $userId);
                }
            }
            m::add(sprintf(_('You have deleted %d users.'), count($selected)), m::SUCCESS);
        } else {
            m::add(_('You haven\'t selected any user to delete.'), m::ERROR);
        }

        return $this->redirect($this->generateUrl('manager_acl_user'));
    }

    /**
     * Returns a connected users panel.
     * TODO: Not work in manager [ Nor developed ]
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
        $user = new \User();

        $settings = array(
            'default_language' => 'gl_ES',
        );

        $setted = $user->setMeta($_SESSION['userid'], $settings);
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
                return $this->redirect($this->generateUrl('manager_acl_user'));
            }
        }
    }
}
