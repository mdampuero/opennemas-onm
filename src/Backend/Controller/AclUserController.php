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

use Common\ORM\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Onm\Security\Acl;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

class AclUserController extends Controller
{
    /**
     * Shows the form to create a new user.
     */
    public function createAction()
    {
        $em = $this->get('orm.manager');

        $userGroups = $em->getRepository('UserGroup')->findBy();
        $categories = $this->get('category_repository')->findBy(
            'internal_category <> 0',
            'name ASC'
        );

        $extra['user_groups'] = array_map(function ($a) {
            return [ 'id' => $a->pk_user_group, 'name' => $a->name ];
        }, $userGroups);

        $extra['categories'] = array_map(function ($a) {
            return [ 'id' => $a->id, 'title' => $a->title ];
        }, $categories);

        array_unshift($extra['categories'], [ 'id' => 0, 'title' => _('Frontpage') ]);

        // Get available languages
        $languages = array_merge(
            [ 'default' => _('Default system language') ],
            $this->container->get('core.locale')->getLocales()
        );

        // Get minimum password level
        $defaultLevel  = $this->container->getParameter('password_min_level');
        $instanceLevel = s::get('pass_level');
        $minPassLevel  = ($instanceLevel)? $instanceLevel: $defaultLevel;


        $id = $this->get('core.instance')->getClient();

        if (!empty($id)) {
            try {
                $extra['client'] = $em->getRepository('Client')
                    ->find($id)->getData();
            } catch (\Exception $e) {
            }
        }

        $extra['countries'] = Intl::getRegionBundle()->getCountryNames();
        $extra['taxes']     = $this->get('vat')->getTaxes();

        return $this->render(
            'acl/user/new.tpl',
            array(
                'extra'          => $extra,
                'user_groups'    => $userGroups,
                'languages'      => $languages,
                'categories'     => $categories,
                'min_pass_level' => $minPassLevel,
                'gender_options' => [
                    ''       => _('Undefined'),
                    'male'   => _('Male'),
                    'female' => _('Female'),
                    'other'  => _('Other')
                ],
            )
        );
    }

    /**
     * Show a paginated list of backend users.
     *
     * @return Response The response object.
     *
     * @Security("has_role('USER_ADMIN')")
     * @CheckModuleAccess(module="USER_MANAGER")
     */
    public function listAction()
    {
        return $this->render('acl/user/list.tpl');
    }

    /**
     * Shows the user information given its id
     *
     * This action is not mapped with CheckModuleAccess annotation because it's
     * used in edit profile action that should be available to all users with
     * or without having users module activated.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $request->getSession()->set(
            '_security.backend.target_path',
            $this->generateUrl('admin_login_callback')
        );

        // User can modify his data
        $id = $request->query->filter('id', '', FILTER_SANITIZE_STRING);

        if ($id === 'me') {
            $id = $this->getUser()->id;
        }

        // Check if the user is the same as the one that we want edit or
        // if we have permissions for editing other user information.
        if ($this->getUser()->id != $id && Acl::check('USER_UPDATE') === false) {
            throw new AccessDeniedException();
        }

        $ccm  = new \ContentCategoryManager();
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

        // Get all categories
        $allcategorys = $this->get('category_repository')->findBy(
            'internal_category <> 0',
            'name ASC'
        );

        // Add Frontpage to available categories
        $frontpage = new \ContentCategory();
        $frontpage->id = 0;
        $frontpage->title = _('Frontpage');
        $frontpage->pk_fk_content_category = 0;
        array_unshift($allcategorys, $frontpage);

        // Get available languages
        $languages    = $this->container->getParameter('core.locale.available');
        $languages    = array_merge(array('default' => _('Default system language')), $languages);

        // Get minimum password level
        $defaultLevel  = $this->container->getParameter('password_min_level');
        $instanceLevel = s::get('pass_level');
        $minPassLevel  = ($instanceLevel)? $instanceLevel: $defaultLevel;

        // Get selected groups
        $userGroup    = new \UserGroup();
        $selectedGroups = [];
        foreach ($userGroup->find() as $group) {
            if (in_array($group->id, $user->id_user_group)) {
                $selectedGroups[] = $group;
            }
            if (in_array(4, $user->id_user_group)) {
                $selectedGroups[] = $group;
            }
        }

        // Add Frontpage to selected categories if is in users privileges
        if (in_array(0, $user->getAccessCategoryIds())) {
            $user->accesscategories[0]->id = 0;
            $user->accesscategories[0]->title = _('Frontpage');
            $user->accesscategories[0]->pk_fk_content_category = 0;
        }

        $user->eraseCredentials();

        $id = $this->get('core.instance')->getClient();

        if (!empty($id)) {
            try {
                $extra['client'] = $this->get('orm.manager')
                    ->getRepository('manager.client', 'Database')
                    ->find($id)->getData();
            } catch (\Exception $e) {
            }
        }

        $extra['countries'] = Intl::getRegionBundle()->getCountryNames();
        $extra['taxes']     = $this->get('vat')->getTaxes();

        return $this->render(
            'acl/user/new.tpl',
            array(
                'extra'                     => $extra,
                'user'                      => $user,
                'user_groups'               => $userGroup->find(),
                'selected_groups'           => $selectedGroups,
                'languages'                 => $languages,
                'content_categories'        => $allcategorys,
                'content_categories_select' => $user->accesscategories,
                'min_pass_level'            => $minPassLevel,
                'gender_options'            => [
                    ''       => _('Undefined'),
                    'male'   => _('Male'),
                    'female' => _('Female'),
                    'other'  => _('Other')
                ],
            )
        );
    }

    /**
     * Handles the update action for a user given its id
     *
     * This action is not mapped with CheckModuleAccess annotation because it's
     * used in edit profile action that should be available to all users with
     * or without having users module activated.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     **/
    public function updateAction(Request $request)
    {
        $userId = $request->query->getDigits('id');
        if ($userId != $this->getUser()->id) {
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
            'activated'       => (int) $request->request->filter('activated', 0, FILTER_SANITIZE_STRING),
            'type'            => (int) $request->request->filter('type', 1, FILTER_SANITIZE_STRING),
            'sessionexpire'   => $request->request->getDigits('sessionexpire'),
            'id_user_group'   => $request->request->get('id_user_group', $user->id_user_group),
            'ids_category'    => $request->request->get('ids_category'),
            'avatar_img_id'   => $request->request->filter('avatar', null, FILTER_SANITIZE_STRING),
        );

        if (false === Acl::check('USER_UPDATE')) {
            $data['activated'] = $user->activated;
            $data['type'] = $user->type;
        }

        $file = $request->files->get('avatar');

        // Get max users from settings
        $maxUsers = s::get('max_users');
        // Check total activated users remaining before updating
        $updateEnabled = true;
        if ($data['activated'] == '1' && $maxUsers > 0) {
            $updateEnabled = \User::getTotalActivatedUsersRemaining($maxUsers + $user->activated);
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

                    if ($user->id == $this->getUser()->id) {
                        $request->getSession()->set('user_language', $meta['user_language']);
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
     * Creates an user give some information.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("has_role('USER_CREATE')")
     * @CheckModuleAccess(module="USER_MANAGER")
     */
    public function saveAction(Request $request)
    {
        $data      = $request->request->all();
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('User');

        // Encode password if present
        if (array_key_exists('password', $data) && !empty($data['password'])) {
            $data['password'] = md5($data['password']);
        }

        $user = new User($converter->objectify($data));

        try {
            $file = $request->files->get('avatar');

            // Upload user avatar if exists
            if (!empty($file)) {
                $photoId = $user->createAvatar($file, \Onm\StringUtils::getTitle($user->name));
                $user->avatar_img_id = $photoId;
            }

            $em->persist($user);

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
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add(
                'error',
                $e->getMessage()
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_acl_user_show', [ 'id' => $user->id ])
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
     *
     * @CheckModuleAccess(module="USER_MANAGER")
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
     *
     * @CheckModuleAccess(module="USER_MANAGER")
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
     * This action is not mapped with CheckModuleAccess annotation because it's
     * used in edit profile action that should be available to all users with
     * or without having users module activated.
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function setMetaAction(Request $request)
    {
        $user = new \User($this->getUser()->id);

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
     *
     * @CheckModuleAccess(module="USER_MANAGER")
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
        $this->view->assign('languages', $this->container->getParameter('core.locale.available'));
        $this->view->assign('current_language', \Application::$language);

        if ('POST' != $request->getMethod()) {
            return $this->render('login/recover_pass.tpl');
        } else {
            $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);
            $token = '';
            // Get user by email
            $user = new \User();
            $user->findByEmail($email);

            // If e-mail exists in DB
            if (!is_null($user->id)) {
                // Generate and update user with new token
                $token = md5(uniqid(mt_rand(), true));
                $user->updateUserToken($user->id, $token);

                $url = $this->generateUrl('admin_acl_user_reset_pass', array('token' => $token), true);

                $this->view->setCaching(0);

                $mailSubject = sprintf(_('Password reminder for %s'), s::get('site_title'));
                $mailBody = $this->renderView(
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
        $this->view->assign('languages', $this->container->getParameter('core.locale.available'));
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
        $template = 'acl/user/social.tpl';

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

        if ($request->get('style') && $request->get('style') == 'orb') {
            $template = 'acl/user/social_alt.tpl';
        }

        $this->dispatchEvent('social.disconnect', array('user' => $user));

        return $this->render(
            $template,
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
     * Disconnects from social account accounts.
     *
     * @param  Request  $request The request object.
     * @param  integer  $id      The user's id.
     * @return Response          The response object.
     */
    public function disconnectAction(Request $request, $id, $resource)
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
                [
                    'id'       => $id,
                    'resource' => $resource,
                    'style'    => $request->get('style')
                ]
            )
        );
    }
}
