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
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
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
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $user = new \User($id);
        if (is_null($user->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_("Unable to find the author with the id '%d'"), $id)
            );

            return $this->redirect($this->generateUrl('admin_authors'));
        }

        // Fetch user photo if exists
        if (!empty($user->avatar_img_id)) {
            $user->photo = new \Photo($user->avatar_img_id);
        }

        $user->meta = $user->getMeta();
        if (array_key_exists('is_blog', $user->meta)) {
            $user->is_blog = $user->meta['is_blog'];
        } else {
            $user->is_blog = 0;
        }

        return $this->render('authors/new.tpl', array('user' => $user));
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

        if ($request->getMethod() == 'POST') {
            $data = array(
                'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
                'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'sessionexpire'   => 60,
                'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
                'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
                'id_user_group'   => array(3),
                'ids_category'    => array(),
                'activated'       => 0,
                'type'            => 1,
                'deposit'         => 0,
                'token'           => null,
            );

            // Generate username and password from real name
            $data['username'] = strtolower(str_replace('-', '.', \Onm\StringUtils::getTitle($data['name'])));
            $data['password'] = md5($data['name']);

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
                            'admin_author_show',
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
        }

        return $this->render('authors/new.tpl', array('user' => $user));
    }

    /**
     * Handles the update action for an author given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasPermission('AUTHOR_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $userId = $request->query->getDigits('id');

        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("The data send by the user is not valid.")
            );

            return $this->redirect(
                $this->generateUrl('admin_author_show', array('id' => $userId))
            );
        }

        $user   = new \User($userId);

        $accessCategories = array();
        foreach ($user->accesscategories as $key => $value) {
            $accessCategories[] = (int)$value->pk_content_category;
        }

        $data = array(
            'id'              => $userId,
            'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
            'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
            'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'type'            => $user->type,
            'sessionexpire'   => 60,
            'id_user_group'   => $user->id_user_group,
            'ids_category'    => $accessCategories,
            'activated'       => $user->activated,
            'avatar_img_id'   => $request->request->filter('avatar', null, FILTER_SANITIZE_STRING),
            'username'        => $user->username,
        );

        $file = $request->files->get('avatar');

        // Generate username and password from real name
        if (empty($data['username'])) {
            $data['username'] = strtolower(str_replace('-', '.', \Onm\StringUtils::getTitle($data['name'])));
        }

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
                $meta['is_blog'] = (empty($meta['is_blog'])) ? 0 : 1;
                $meta['inrss']   = (empty($meta['inrss'])) ? 0 : 1;
                foreach ($meta as $key => $value) {
                    $user->setMeta(array($key => $value));
                }

                // TODO: Use remove when merging ONM-1655
                $this->get('cache.manager')->getConnection('instance')
                    ->delete('user-' . $user->id);

                // Clear caches
                dispatchEventWithParams('author.update', array('id' => $userId));

                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Author updated successfully.')
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to update the author with that information')
                );
            }
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect(
            $this->generateUrl('admin_author_show', array('id' => $userId))
        );
    }
}
