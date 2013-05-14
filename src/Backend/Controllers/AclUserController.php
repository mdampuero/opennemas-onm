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

        $filter = $request->query->get('filter', array());

        if (!$_SESSION['isMaster']) {
            $filter ['base'] = 'fk_user_group != 4';
        }

        $user      = new \User();
        $users     = $user->getUsers($filter, ' ORDER BY login ');

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

        $user->meta = $user->getMeta();

        if (array_key_exists('paywall_time_limit', $user->meta)) {
            $user->meta['paywall_time_limit'] = new \DateTime(
                $user->meta['paywall_time_limit'],
                new \DateTimeZone('UTC')
            );
        }

        $userGroup = new \UserGroup();
        $tree = $ccm->getCategoriesTree();
        $languages = $this->container->getParameter('available_languages');
        $languages = array_merge(array('default' => _('Default system language')), $languages);


        return $this->render(
            'acl/user/new.tpl',
            array(
                'user'                      => $user,
                'user_groups'               => $userGroup->find(),
                'languages'                 => $languages,
                'content_categories'        => $tree,
                'content_categories_select' => $user->getAccessCategoryIds(),
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
            'login'           => $request->request->filter('login', null, FILTER_SANITIZE_STRING),
            'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
            'password'        => $request->request->filter('password', null, FILTER_SANITIZE_STRING),
            'passwordconfirm' => $request->request->filter('passwordconfirm', null, FILTER_SANITIZE_STRING),
            'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'type'            => $request->request->filter('type', '0', FILTER_SANITIZE_STRING),
            'sessionexpire'   => $request->request->getDigits('sessionexpire'),
            'id_user_group'   => $request->request->getDigits('id_user_group'),
            'ids_category'    => $request->request->get('ids_category'),
        );

        // TODO: validar datos
        $user = new \User($userId);
        $user->update($data);

        $userLanguage = $request->request->filter('user_language', 'default', FILTER_SANITIZE_STRING);
        $user->setMeta(array('user_language' => $userLanguage));

        $paywallTimeLimit = $request->request->filter('meta[paywall_time_limit]', '', FILTER_SANITIZE_STRING);
        if (!is_null($paywallTimeLimit) && !empty($paywallTimeLimit)) {
            $time = \DateTime::createFromFormat('Y-m-d H:i:s', $paywallTimeLimit);
            $time->setTimeZone(new \DateTimeZone('UTC'));

            $user->setMeta(array('paywall_time_limit' => $time->format('Y-m-d H:i:s')));
        }

        if ($user->id == $_SESSION['userid']) {
            $_SESSION['user_language'] = $userLanguage;
        }

        m::add(_('User data updated successfully.'), m::SUCCESS);
        if ($action == 'validate') {
            $redirectUrl = $this->generateUrl('admin_acl_user_show', array('id' => $userId));
        } else {
            // If a regular user is upating him/her information
            // redirect to welcome page
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
                'login'           => $request->request->filter('login', null, FILTER_SANITIZE_STRING),
                'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
                'password'        => $request->request->filter('password', null, FILTER_SANITIZE_STRING),
                'passwordconfirm' => $request->request->filter('passwordconfirm', null, FILTER_SANITIZE_STRING),
                'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'sessionexpire'   => $request->request->getDigits('sessionexpire'),
                'id_user_group'   => $request->request->getDigits('id_user_group'),
                'ids_category'    => $request->request->get('ids_category'),
                'authorize'       => 1,
                'type'            => $request->request->filter('type', '0', FILTER_SANITIZE_STRING),
                'deposit'         => 0,
                'token'           => null,
            );

            try {
                if ($user->create($data)) {
                    $userLanguage = $request->request->filter('user_language', 'default', FILTER_SANITIZE_STRING);
                    $user->setMeta(array('user_language' => $userLanguage));
                    $paywallTimeLimit = $request->request->filter(
                        'meta[paywall_time_limit]',
                        '',
                        FILTER_SANITIZE_STRING
                    );
                    if (!is_null($paywallTimeLimit) && !empty($paywallTimeLimit)) {
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
        $user = new \User();

        $settings = array(
            'default_language' => 'gl_ES',
            'test2' => 1
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

            if ($user->authorize == 1) {
                $user->unauthorizeUser($userId);
            } else {
                $user->authorizeUser($userId);
            }

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($this->generateUrl('admin_acl_user'));
            }
        }
    }
}
