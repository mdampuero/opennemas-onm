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
use Common\Core\Controller\Controller;
use Common\ORM\Core\Exception\EntityNotFoundException;
use Common\ORM\Entity\User;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Intl\Intl;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class UserController extends Controller
{
    /**
     * Shows the form to create a new user.
     */
    public function createAction()
    {
        $em = $this->get('orm.manager');

        $userGroups = $em->getRepository('UserGroup')->findBy('type = 0');
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
            $this->container->get('core.locale')->getAvailableLocales()
        );

        // Get minimum password level
        $defaultLevel  = $this->container->getParameter('password_min_level');
        $instanceLevel = $this->get('setting_repository')->get('pass_level');
        $minPassLevel  = ($instanceLevel) ? $instanceLevel : $defaultLevel;

        $id = $this->get('core.instance')->getClient();

        if (!empty($id)) {
            try {
                $extra['client'] = $em->getRepository('Client')
                    ->find($id)->getData();
            } catch (\Exception $e) {
                $extra['client'] = null;
            }
        }

        $extra['countries'] = Intl::getRegionBundle()->getCountryNames();
        $extra['taxes']     = $this->get('vat')->getTaxes();
        $extra['settings']  = $em->getDataSet('Settings', 'instance')
            ->get('user_settings', []);

        return $this->render('user/item.tpl', [
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
        ]);
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

        $this->get('core.dispatcher')->dispatch('social.disconnect', ['user' => $user]);

        return $this->redirect($this->generateUrl('backend_user_social', [
            'id'       => $id,
            'resource' => $resource,
            'style'    => $request->get('style')
        ]));
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
        return $this->render('user/list.tpl');
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
                $data['password'] = $this->get('core.security.encoder.password')
                    ->encodePassword($data['password'], null);
            }
        }

        $user = new User($converter->objectify($data));

        // TODO: Remove when data supports empty values (when using SPA)
        $user->type = empty($user->type) ? 0 : $user->type;
        $user->url  = empty($user->url) ? ' ' : $user->url;
        $user->bio  = empty($user->bio) ? ' ' : $user->bio;

        try {
            // Check if the user is already registered
            $em->getRepository('User')->findOneBy(
                'name ~ "' . $data['username'] . '" or email ~ "' . $data['email'] . '"'
            );

            throw new \Exception(_('The email address or user name is already in use.'));
        } catch (\Exception $e) {
        }

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

            return $this->redirect($this->generateUrl('backend_user_show', [
                'id' => $user->id
            ]));
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('backend_user_create'));
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
    public function showAction(Request $request)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('User');
        $id        = $request->query->filter('id', '', FILTER_SANITIZE_STRING);

        if ($id === 'me') {
            $id = $this->getUser()->id;
        }

        if ($this->getUser()->id != $id
            && !$this->get('core.security')->hasPermission('USER_UPDATE')
        ) {
            throw new AccessDeniedException();
        }

        try {
            $user = $em->getRepository('User')->find($id);
            $user->eraseCredentials();
        } catch (EntityNotFoundException $e) {
            $request->getSession()->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the user with the id "%d"'), $id)
            );
            return $this->redirect($this->generateUrl('backend_users_list'));
        }

        if (!empty($user->paywall_time_limit)
            && is_object($user->paywall_time_limit)
        ) {
            $user->paywall_time_limit =
                $user->paywall_time_limit->format('Y-m-d H:i:s');
        }

        // Fetch user photo if exists
        if (!empty($user->avatar_img_id)) {
            $user->photo = $this->get('entity_repository')
                ->find('Photo', $user->avatar_img_id);
        }

        // TODO: Remove the pk_user_group condition when implementing ticket ONM-1660
        $userGroups = $em->getRepository('UserGroup')->findBy('type = 0 and pk_user_group != 4');
        $categories = $this->get('category_repository')->findBy(
            'internal_category <> 0',
            'name ASC'
        );

        $extra['categories'] = array_map(function ($category) {
            return [
                'title' => $this->get('data.manager.filter')
                    ->set($category->title)->filter('localize')->get(),
                'id' => $category->id
            ];
        }, $categories);

        $extra['user_groups'] = array_map(function ($a) {
            return [ 'id' => $a->pk_user_group, 'name' => $a->name ];
        }, $userGroups);

        $extra['language_data'] = $this->getLocaleData('frontend');

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
            $this->container->get('core.locale')->getAvailableLocales()
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
                $extra['client'] = null;
            }
        }

        $extra['countries'] = Intl::getRegionBundle()->getCountryNames();
        $extra['taxes']     = $this->get('vat')->getTaxes();

        // TODO: Remove when using new ORM to responsify objects
        if (is_object($user->paywall_time_limit)) {
            $user->paywall_time_limit =
                $user->paywall_time_limit->format('Y-m-d H:i:s');
        }

        // TODO: Remove when using new ORM to responsify objects
        if (is_object($user->paywall_time_limit)) {
            $user->terms_accepted =
                $user->terms_accepted->format('Y-m-d H:i:s');
        }

        return $this->render('user/item.tpl', [
            'extra'          => $extra,
            'user'           => $converter->responsify($user),
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
            ]
        ]);
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

        $session = $request->getSession();

        $session->set(
            '_security.backend.target_path',
            $this->generateUrl('core_authentication_complete')
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

        $this->get('core.dispatcher')->dispatch('social.connect', ['user' => $user]);

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
                    $data['password'] = $this->get('core.security.encoder.password')
                        ->encodePassword($data['password'], null);
                }
            }
        }

        // TODO: Hack for activated and type flags
        $data['type']      = !array_key_exists('type', $data) ? 1 : 0;
        $data['activated'] = !array_key_exists('activated', $data) ? 0 : 1;

        $user->merge($converter->objectify($data));

        // TODO: Remove after check and update database schema
        $user->type = empty($user->type) ? 0 : $user->type;
        $user->url  = empty($user->url) ? ' ' : $user->url;
        $user->bio  = empty($user->bio) ? ' ' : $user->bio;

        try {
            // Check if the user is already registered
            $users = array_filter($em->getRepository('User')->findBy(
                'name ~ "' . $data['username'] . '" or email ~ "' . $data['email'] . '"'
            ), function ($element) use ($user) {
                return $user->id !== $element->id;
            });

            if (count($users) > 0) {
                throw new \Exception(_('The email address or user name is already in use.'));
            }

            $file = $request->files->get('avatar');

            // avatar: null = new, 0 = removed, 1 = empty/unchanged
            if (empty($request->get('avatar'))) {
                $this->removeAvatar($user);
                $user->avatar_img_id = null;
            }

            if (!empty($file)) {
                $user->avatar_img_id =
                    $this->createAvatar($file, \Onm\StringUtils::getTitle($user->name));
            }

            $em->persist($user);

            // Clear caches
            $this->get('core.dispatcher')->dispatch('user.update', ['user' => $user]);

            // Check if is an author and delete caches
            if (in_array('3', $user->fk_user_group)) {
                $this->get('core.dispatcher')->dispatch('author.update', ['id' => $user->id]);
            }

            $request->getSession()->getFlashBag()->add('success', _('User data updated successfully.'));
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('backend_user_show', ['id' => $user->id])
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
        $relativeAuthorImagePath = "/authors/" . $username;
        $uploadDirectory         = MEDIA_IMG_PATH . $relativeAuthorImagePath;

        // Get original information of the uploaded/local image
        $originalFileName = $file->getBaseName();
        $fileExtension    = $file->guessExtension();

        // Generate new file name
        $currentTime = gettimeofday();
        $microTime   = intval(substr($currentTime['usec'], 0, 5));
        $newFileName = date("YmdHis") . $microTime . "." . $fileExtension;

        // Check upload directory
        if (!is_dir($uploadDirectory)) {
            \Onm\FilesManager::createDirectory($uploadDirectory);
        }

        // Upload file
        $file->move($uploadDirectory, $newFileName);

        // Get all necessary data for the photo
        $infor = new \MediaItem($uploadDirectory . '/' . $newFileName);
        $data  = [
            'title'       => $originalFileName,
            'name'        => $newFileName,
            'user_name'   => $newFileName,
            'path_file'   => $relativeAuthorImagePath,
            'nameCat'     => $username,
            'category'    => '',
            'created'     => $infor->atime,
            'changed'     => $infor->mtime,
            'size'        => round($infor->size / 1024, 2),
            'width'       => $infor->width,
            'height'      => $infor->height,
            'type'        => $infor->type,
            'author_name' => '',
        ];

        // Create new photo
        $photo   = new \Photo();
        $photoId = $photo->create($data);

        return $photoId;
    }

    /**
     * Removes the user's avatar.
     *
     * @param User $user The user object.
     */
    protected function removeAvatar($user)
    {
        $data = $user->getStored();

        if (!array_key_exists('avatar_img_id', $data)
            || empty($data['avatar_img_id'])
        ) {
            return;
        }

        $avatar = $this->get('entity_repository')
            ->find('Photo', $data['avatar_img_id']);

        $path = MEDIA_IMG_PATH . $avatar->path_img;
        $fs   = new Filesystem();

        if ($fs->exists($path)) {
            $fs->remove($path);
        }
    }
}
