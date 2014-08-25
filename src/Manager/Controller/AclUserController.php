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
use Onm\Security\Acl;
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
                && !Acl::check('USER_UPDATE')
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
                            'manager_acl_user_show',
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
        $userId = $request->query->getDigits('id');

        if (!is_null($userId)) {
            $user = new \User();
            if ($user->delete($userId)) {
                $user->deleteMeta($userId);

                $request->getSession()->getFlashBag()->add(
                    'success',
                    sprintf(_('Successfully deleted user with id "%d".'), $userId)
                );
            }

            if (!$request->isXmlHttpRequest()) {
                return $this->redirect($this->generateUrl('manager_acl_user'));
            } else {
                return new Response('ok');
            }
        }
    }
}
