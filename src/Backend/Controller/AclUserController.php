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

use Common\Core\Annotation\Security;
use Common\ORM\Entity\User;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
        $instanceLevel = $this->get('setting_repository')->get('pass_level');
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
     * Disconnects from social account accounts.
     *
     * @param Request $request The request object.
     * @param integer $id      The user's id.
     *
     * @return Response The response object.
     */
    public function disconnectAction(Request $request, $id, $resource)
    {
        $user = $this->get('core.user');

        if (empty($user)) {
            return new Response();
        }

        unset($user->{$resource . '_id'});
        unset($user->{$resource . '_id'});
        unset($user->{$resource . '_email'});
        unset($user->{$resource . '_token'});
        unset($user->{$resource . '_realname'});

        $this->get('orm.manager')->persist($user);

        $this->dispatchEvent('social.disconnect', array('user' => $user));

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

    /**
     * Show a paginated list of backend users.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('acl/user/list.tpl');
    }

    /**
     * Shows the form for recovering the pass of a user and sends the mail to
     * the user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function recoverPasswordAction(Request $request)
    {
        // Setup view
        $this->view->assign('locales', $this->get('core.locale')->getLocales());
        $this->view->assign('locale', $this->get('core.locale')->getLocale());

        if ('POST' != $request->getMethod()) {
            return $this->render('login/recover_pass.tpl');
        } else {
            $email = $request->request->filter('email', null, FILTER_SANITIZE_EMAIL);
            $token = '';
            // Get user by email
            $em   = $this->get('orm.manager');
            $user = $em->getRepository('User')->findOneBy("email = '$email'");

            // If e-mail exists in DB
            if (!is_null($user->id)) {
                // Generate and update user with new token
                $token = md5(uniqid(mt_rand(), true));
                $user->merge([ 'token' => $token ]);
                $em->persist($user);

                $url = $this->generateUrl('admin_acl_user_reset_pass', array('token' => $token), true);

                $this->view->setCaching(0);

                $mailSubject = sprintf(
                    _('Password reminder for %s'),
                    $this->get('setting_repository')->get('site_title')
                );

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
                    ->setFrom([
                        'no-reply@postman.opennemas.com' => $this->get('setting_repository')->get('site_name')
                    ]);

                try {
                    $mailer = $this->get('mailer');
                    $mailer->send($message);

                    $this->get('application.log')->notice(
                        "Email sent. Backend restore user password (to: ".$user->email.")"
                    );

                    $this->view->assign([
                        'mailSent' => true,
                        'user' => $user
                    ]);
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
     * Regenerates the pass for a user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function regeneratePasswordAction(Request $request)
    {
        // Setup view
        $this->view->assign('locales', $this->get('core.locale')->getLocales());
        $this->view->assign('locale', $this->get('core.locale')->getLocale());

        $token = $request->query->filter('token', null, FILTER_SANITIZE_STRING);

        try {
            $em   = $this->get('orm.manager');
            $user = $em->getRepository('User')->findOneBy("token = '$token'");
        } catch (\Exception $e) {
            $user = null;
        }

        if ('POST' !== $request->getMethod()) {
            if (!is_object($user) || empty($user->id)) {
                $request->getSession()->getFlashBag()->add(
                    'error',
                    _('Unable to find the password reset request.  Please check the url we sent you in the email.')
                );

                return $this->redirect($this->generateUrl('admin_login'));
            }

            $this->view->assign('user', $user);
        } else {
            $password       = $request->request->filter('password', null, FILTER_SANITIZE_STRING);
            $passwordVerify = $request->request->filter('password-verify', null, FILTER_SANITIZE_STRING);

            if ($password == $passwordVerify && !empty($password) && !is_null($user)) {
                $user->merge([
                    'password' => md5($password),
                    'token'    => null
                ]);
                $em->persist($user);

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
     * Creates an user give some information.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('USER_MANAGER')
     *     and hasPermission('USER_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $data      = $request->request->all();
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('User');

        // Encode password if present
        if (array_key_exists('password', $data)) {
            if (empty($data['password'])) {
                unset($data['password']);
            }

            if (!empty($data['password'])) {
                $data['password'] = $this->get('onm_password_encoder')
                    ->encodePassword($data['password'], null);
            }
        }

        $user = new User($converter->objectify($data));

        // TODO: Remove when data supports empty values (when using SPA)
        $user->type = empty($user->type) ? 0 : $user->type;
        $user->url = empty($user->url) ? ' ' : $user->url;
        $user->bio = empty($user->bio) ? ' ' : $user->bio;

        try {
            $file = $request->files->get('avatar');

            // Upload user avatar if exists
            if (!empty($file)) {
                $user->avatar_img_id =
                    $this->createAvatar($file, \Onm\StringUtils::getTitle($user->name));
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

        return $this->redirect($this->generateUrl('admin_acl_user_create'));
    }

    /**
     * Shows the user information given its id
     *
     * This action is not mapped with Security annotation because it's
     * used in edit profile action that should be available to all users with
     * or without having users module activated.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function showAction($id)
    {
        $em = $this->get('orm.manager');

        if ($id === 'me') {
            $id = $this->getUser()->id;
        }

        if ($this->getUser()->id != $id
            && !$this->get('core.security')->hasPermission('USER_UPDATE')
        ) {
            throw new AccessDeniedException();
        }

        $user = $em->getRepository('User')->find($id);
        $user->eraseCredentials();

        // Fetch user photo if exists
        if (!empty($user->avatar_img_id)) {
            $user->photo = $this->get('entity_repository')
                ->find('Photo', $user->avatar_img_id);
        }

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

        $selected = [
            'categories' => array_filter($extra['categories'], function ($a) use ($user) {
                return in_array($a['id'], $user->categories);
            }),
            'user_groups' => array_filter($extra['user_groups'], function ($a) use ($user) {
                return in_array($a['id'], $user->fk_user_group);
            }),
        ];

        // Get available languages
        $languages = array_merge(
            [ 'default' => _('Default system language') ],
            $this->container->get('core.locale')->getLocales()
        );

        // Get minimum password level
        $defaultLevel  = $this->getParameter('password_min_level');
        $instanceLevel = $this->get('setting_repository')->get('pass_level');
        $minPassLevel  = $instanceLevel ? $instanceLevel : $defaultLevel;

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
                'user'           => $user,
                'user_groups'    => $userGroups,
                'languages'      => $languages,
                'categories'     => $categories,
                'selected'       => $selected,
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
     * Displays the facebook iframe to connect accounts.
     *
     * @param Request $request The request object.
     * @param integer $id      The user's id.
     *
     * @return Response The response object.
     */
    public function socialAction(Request $request, $id, $resource)
    {
        $template = 'acl/user/social.tpl';
        try {
            $user = $this->get('orm.manager')->getRepository('User')->find($id);
        } catch (\Exception $e) {
            $user = $this->get('orm.manager')->getRepository('User', 'manager')
                ->find($id);
        }

        $session  = $request->getSession();

        $session->set(
            '_security.backend.target_path',
            $this->generateUrl('admin_login_callback')
        );

        if (!$user) {
            return new Response();
        }

        $resourceId = $user->{$resource . '_id'};

        $connected = false;
        if ($resourceId) {
            $connected = true;
        }

        $resourceName = 'Twitter';

        if ($resource == 'facebook') {
            $resourceName = 'Facebook';
        }

        if ($request->get('style') && $request->get('style') == 'orb') {
            $template = 'acl/user/social_alt.tpl';
        }

        $this->dispatchEvent('social.connect', array('user' => $user));

        return $this->render($template, [
            'current_user_id' => $user->id,
            'connected'       => $connected,
            'resource_id'     => $resourceId,
            'resource'        => $resource,
            'resource_name'   => $resourceName,
            'user'            => $user,
        ]);
    }

    /**
     * Handles the update action for a user given its id.
     *
     * This action is not mapped with Security annotation because it's
     * used in edit profile action that should be available to all users with
     * or without having users module activated.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function updateAction(Request $request, $id)
    {
        if ($id != $this->getUser()->id
            && !$this->get('core.security')->hasPermission('USER_UPDATE')
        ) {
            throw new AccessDeniedException();
        }

        $data      = $request->request->all();
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('User');
        $user      = $em->getRepository('User')->find($id);

        // Encode password if present
        if (array_key_exists('password', $data)) {
            if (empty($data['password'])) {
                unset($data['password']);
            }

            if (!empty($data['password'])) {
                if (!empty($data['password'])) {
                    $data['password'] = $this->get('onm_password_encoder')
                        ->encodePassword($data['password'], null);
                }
            }
        }

        // TODO: Remove when data supports empty values (when using SPA)
        $user->categories    = [];
        $user->fk_user_group = [];

        $user->merge($converter->objectify($data));

        // TODO: Remove after check and update database schema
        $user->type = empty($user->type) ? 0 : $user->type;
        $user->url = empty($user->url) ? ' ' : $user->url;
        $user->bio = empty($user->bio) ? ' ' : $user->bio;

        try {
            $file = $request->files->get('avatar');

            if (!empty($file)) {
                $user->avatar_img_id =
                    $this->createAvatar($file, \Onm\StringUtils::getTitle($user->name));
            }

            $em->persist($user);

            // Clear caches
            $this->dispatchEvent('user.update', array('user' => $user));

            // Check if is an author and delete caches
            if (in_array('3', $user->fk_user_group)) {
                $this->dispatchEvent('author.update', array('id' => $user->id));
            }

            $request->getSession()->getFlashBag()->add('success', _('User data updated successfully.'));
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('admin_acl_user_show', array('id' => $user->id))
        );
    }

    /**
     * Creates an avatar for user from a file and a username.
     *
     * @param File   $file     The avatar file.
     * @param string $username The user's name.
     *
     * @return integer The avatar id.
     */
    protected function createAvatar($file, $username)
    {
        // Generate image path and upload directory
        $relativeAuthorImagePath ="/authors/".$username;
        $uploadDirectory =  MEDIA_IMG_PATH .$relativeAuthorImagePath;

        // Get original information of the uploaded/local image
        $originalFileName = $file->getBaseName();
        $fileExtension    = $file->guessExtension();

        // Generate new file name
        $currentTime = gettimeofday();
        $microTime   = intval(substr($currentTime['usec'], 0, 5));
        $newFileName = date("YmdHis").$microTime.".".$fileExtension;

        // Check upload directory
        if (!is_dir($uploadDirectory)) {
            \Onm\FilesManager::createDirectory($uploadDirectory);
        }

        // Upload file
        $file->move($uploadDirectory, $newFileName);

        // Get all necessary data for the photo
        $infor = new \MediaItem($uploadDirectory.'/'.$newFileName);
        $data = array(
            'title'       => $originalFileName,
            'name'        => $newFileName,
            'user_name'   => $newFileName,
            'path_file'   => $relativeAuthorImagePath,
            'nameCat'     => $username,
            'category'    => '',
            'created'     => $infor->atime,
            'changed'     => $infor->mtime,
            'size'        => round($infor->size/1024, 2),
            'width'       => $infor->width,
            'height'      => $infor->height,
            'type'        => $infor->type,
            'author_name' => '',
        );

        // Create new photo
        $photo = new \Photo();
        $photoId = $photo->create($data);

        return $photoId;
    }
}
