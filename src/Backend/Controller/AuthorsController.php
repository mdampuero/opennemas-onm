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

use Common\Core\Annotation\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the system users
 *
 * @package Backend_Controllers
 **/
class AuthorsController extends Controller
{
    /**
     * Show a list of opinion authors.
     *
     * @Security("hasPermission('AUTHOR_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('authors/list.tpl');
    }

    /**
     * Shows the author information given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasPermission('AUTHOR_UPDATE')")
     */
    public function showAction(Request $request, $id)
    {
        try {
            $user = $this->get('orm.manager')
                ->getRepository('User', 'instance')
                ->find($id);
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_("Unable to find the author with the id '%d'"), $id)
            );

            return $this->redirect($this->generateUrl('backend_authors_list'));
        }

        // Fetch user photo if exists
        if (!empty($user->avatar_img_id)) {
            $user->photo = $this->get('entity_repository')
                ->find('Photo', $user->avatar_img_id);
        }

        return $this->render('authors/new.tpl', [ 'user' => $user ]);
    }

    /**
     * Creates an author give some information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasPermission('AUTHOR_CREATE')")
     */
    public function createAction(Request $request)
    {
        $user = new \User();

        return $this->render('authors/new.tpl', array('user' => $user));
    }

    /**
     * Saves a new author.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function saveAction(Request $request)
    {
        $data = array(
            'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
            'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
            'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'id_user_group'   => array(3),
            'ids_category'    => array(),
            'activated'       => 0,
            'type'            => 1,
            'token'           => null,
        );

        // Generate username and password from real name
        $data['username'] = strtolower(str_replace('-', '.', \Onm\StringUtils::getTitle($data['name'])));
        $data['password'] = md5($data['name']);

        $file = $request->files->get('avatar');

        try {
            $user = new \User();
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
                $meta['is_blog'] = (empty($meta['is_blog'])) ? 0 : 1;
                $meta['inrss']   = (empty($meta['inrss'])) ? 0 : 1;
                foreach ($meta as $key => $value) {
                    $user->setMeta(array($key => $value));
                }

                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Author created successfully.')
                );

                return $this->redirect(
                    $this->generateUrl(
                        'backend_author_show',
                        array('id' => $user->id)
                    )
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to create the author with that information')
                );
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('backend_authors_list')
        );
    }

    /**
     * Handles the update action for an author given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasPermission('AUTHOR_UPDATE')")
     */
    public function updateAction(Request $request, $id)
    {
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

        $user->merge($converter->objectify($data));

        // TODO: Remove after check and update database schema
        $user->type = empty($user->type) ? 0 : $user->type;
        $user->url = empty($user->url) ? ' ' : $user->url;
        $user->bio = empty($user->bio) ? ' ' : $user->bio;

        $user->inrss   = $user->inrss === 'on' ? true : false;
        $user->is_blog = $user->is_blog === 'on' ? true : false;

        try {
            $file = $request->files->get('avatar');

            if (!empty($file)) {
                $user->avatar_img_id =
                    $this->createAvatar($file, \Onm\StringUtils::getTitle($user->name));
            }

            $em->persist($user);

            // Clear caches
            $this->get('core.dispatcher')->dispatch('user.update', array('user' => $user));

            // Check if is an author and delete caches
            if (in_array('3', $user->fk_user_group)) {
                $this->get('core.dispatcher')->dispatch('author.update', array('id' => $user->id));
            }

            $request->getSession()->getFlashBag()->add('success', _('Author updated successfully.'));
        } catch (\Exception $e) {
            $request->getSession()->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('backend_author_show', array('id' => $user->id))
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
