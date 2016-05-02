<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Onm\Framework\Controller\Controller;

class UserController extends Controller
{
    /**
     * Creates a new user.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function createAction(Request $request)
    {
        $user = new \User();

        $data = array(
            'username'        => $request->request->filter('username', null, FILTER_SANITIZE_STRING),
            'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
            'password'        => $request->request->filter('password', null, FILTER_SANITIZE_STRING),
            'passwordconfirm' => $request->request->filter('rpassword', null, FILTER_SANITIZE_STRING),
            'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'sessionexpire'   => $request->request->getDigits('sessionexpire'),
            'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
            'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'id_user_group'   => $request->request->get('id_user_group') ? : array(),
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

                $response = new JsonResponse(_('User saved successfully'), 201);

                // Add permanent URL for the current instance
                $response->headers->set(
                    'Location',
                    $this->generateUrl(
                        'manager_ws_user_show',
                        [ 'id' => $user->id ]
                    )
                );

                return $response;
            } else {
                return new JsonResponse(
                    _('Unable to create the user with that information'),
                    400
                );
            }
        } catch (\Exception $e) {
            return new JsonResponse(
                _('Unable to create the user with that information'),
                409
            );
        }
    }

    /**
     * Deletes an user.
     *
     * @param integer $id The user's id.
     *
     * @return JsonResponse The response object.
     */
    public function deleteAction($id)
    {
        $um   = $this->get('user_repository');
        $user = $um->find($id);

        if ($user) {
            $user->delete($id);
            $user->deleteMeta($id);

            return new JsonResponse(_('User deleted successfully'));
        } else {
            return new JsonResponse(
                sprintf(_('Unable to delete the user with the id "%d"'), $id),
                404
            );
        }
    }

    /**
     * Deletes the selected users.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteSelectedAction(Request $request)
    {
        $messages   = [ 'errors' => [], 'success' => [] ];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

        if (!is_array($selected)
            || (is_array($selected) && count($selected) == 0)
        ) {
            return new JsonResponse(
                _('Unable to find users for the given criteria'),
                404
            );
        }

        $um   = $this->get('user_repository');

        foreach ($selected as $id) {
            $user = $um->find($id);

            if ($user) {
                $user->delete($id);
                $user->deleteMeta($id);

                $updated[] = $id;
            } else {
                $messages['errors'][] = array(
                    'error' => sprintf(_('Unable to delete the user with the id "%d"'), $id),
                );
            }
        }

        if (count($updated) > 0) {
            $messages['success'] = [
                'ids'     => $updated,
                'message' => sprintf(_('%s users deleted successfully.'), count($updated)),
            ];
        }

        // Return the proper status code
        if (count($messages['errors']) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($messages['errors']) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse($messages, $statusCode);
    }

    /**
     * Returns the list of users as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : array();
        $orderBy  = $request->query->filter('orderBy') ? : array();

        $order = array();
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        $um    = $this->get('user_repository');
        $users = $um->findBy($criteria, $order, $epp, $page);
        $total = $um->countBy($criteria);

        $userGroups = $this->get('usergroup_repository')->findBy();

        foreach ($users as &$user) {
            $user->eraseCredentials();
        }

        $groups = array();
        foreach ($userGroups as $group) {
            $groups[$group->id] = $group;
        }

        $flatGroups = array_values($groups);
        array_unshift($flatGroups, [ 'id' => null, 'name' => _('All') ]);

        return new JsonResponse(
            array(
                'epp'      => $epp,
                'extra'    => array(
                    'flatGroups' => $flatGroups,
                    'groups'     => $groups
                ),
                'page'     => $page,
                'results'  => $users,
                'total'    => $total,
            )
        );
    }

    /**
     * Returns the data to create a new user.
     *
     * @return JsonResponse The response object.
     */
    public function newAction()
    {
        return new JsonResponse(
            array(
                'user'  => null,
                'extra' => $this->templateParams()
            )
        );
    }

    /**
     * Updated some user properties.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchAction(Request $request, $id)
    {
        $success  = false;
        $activated = $request->request->getDigits('activated');
        $message   = array();

        $user = $this->get('user_repository')->find($id);
        if ($user) {
            try {
                if ($activated) {
                    $user->activateUser($id);
                } else {
                    $user->deactivateUser($id);
                }

                return new JsonResponse(_('User updated successfully.'));
            } catch (Exception $e) {
                return new JsonResponse(
                    sprintf(_('Error while updating user with id "%s"'), $id),
                    409
                );
            }
        } else {
            return new JsonResponse(
                sprintf(_('Unable to find the user with id "%s"'), $id),
                404
            );
        }
    }

    /**
     * Set the activated flag for users in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function patchSelectedAction(Request $request)
    {
        $messages   = [ 'errors' => [], 'success' => [] ];
        $selected   = $request->request->get('selected', null);
        $activated = $request->request->getDigits('activated', 0);
        $statusCode = 200;
        $updated    = [];

        if (is_array($selected) && count($selected) == 0) {
            return new JsonResponse(
                _('Unable to find the users for the given criteria'),
                404
            );
        }

        $um = $this->get('user_repository');

        foreach ($selected as $id) {
            $user = $um->find($id);

            if ($user) {
                try {
                    if ($activated) {
                        $user->activateUser($id);
                    } else {
                        $user->deactivateUser($id);
                    }

                    $updated[] = $id;
                } catch (Exception $e) {
                    $messages['errors'][] = [
                        'id'    => $id,
                        'error' => sprintf(_('Error while updating user with id "%s"'), $id),
                    ];
                }
            } else {
                $messages['errors'][] = [
                    'id'    => $id,
                    'error' => sprintf(_('Unable to find the user with id "%s"'), $id),
                ];
            }
        }

        if (count($updated) > 0) {
            $messages['success'] = [
                'ids'     => $updated,
                'message' => sprintf(_('%s users updated successfully.'), count($updated))
            ];
        }

        if (count($messages['errors']) > 0 && count($updated) > 0) {
            $statusCode = 207;
        } elseif (count($messages['errors']) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse($messages, $statusCode);
    }

    /**
     * Returns an user as JSON.
     *
     * @param integer $id The user's id.
     *
     * @return JsonResponse The response object.
     */
    public function showAction($id)
    {
        $user = $this->get('user_repository')->find($id);

        if (!$user) {
            return new JsonResponse(
                sprintf(_('Unable to find the user with id "%s"'), $id),
                404
            );
        }

        $user->eraseCredentials();

        return new JsonResponse(
            array(
                'user'  => $user,
                'extra' => $this->templateParams()
            )
        );
    }

    /**
     * Returns the current user as JSON.
     *
     * @return JsonResponse The response object.
     */
    public function showMeAction()
    {
        $id = $this->getUser()->id;

        $user = $this->get('user_repository')->find($id);

        if (!$user) {
            return new JsonResponse(_('Unable to find the current user'), 404);
        }

        $user->eraseCredentials();

        return new JsonResponse(
            array(
                'user'  => $user,
                'extra' => $this->templateParams()
            )
        );
    }

    /**
     * Updates an user.
     *
     * @param Request $request The request object.
     * @param integer $id      The user's id.
     *
     * @return Response The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $data = array(
            'id'              => $id,
            'username'        => $request->request->filter('username', null, FILTER_SANITIZE_STRING),
            'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
            'password'        => $request->request->filter('password', null, FILTER_SANITIZE_STRING),
            'passwordconfirm' => $request->request->filter('rpassword', null, FILTER_SANITIZE_STRING),
            'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
            'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'type'            => $request->request->filter('type', '0', FILTER_SANITIZE_STRING),
            'sessionexpire'   => $request->request->getDigits('sessionexpire'),
            'id_user_group'   => $request->request->get('id_user_group') ? : array(),
            'ids_category'    => $request->request->get('ids_category'),
            'activated'       => $request->request->filter('activated', 0, FILTER_SANITIZE_STRING),
            'avatar_img_id'   => $request->request->filter('avatar', null, FILTER_SANITIZE_STRING),
        );

        $file = $request->files->get('avatar');
        $user = new \User($id);

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
                if ($meta) {
                    foreach ($meta as $key => $value) {
                        $user->setMeta(array($key => $value));
                    }
                }

                // Set usermeta paywall time limit
                $paywallTimeLimit = $request->request->filter('paywall_time_limit', '', FILTER_SANITIZE_STRING);
                if (!empty($paywallTimeLimit)) {
                    $time = \DateTime::createFromFormat('Y-m-d H:i:s', $paywallTimeLimit);
                    $time->setTimeZone(new \DateTimeZone('UTC'));

                    $user->setMeta(array('paywall_time_limit' => $time->format('Y-m-d H:i:s')));
                }

                $this->dispatchEvent('user.update', array('user' => $user));

                if ($id == $this->getUser()->id) {
                    $request->getSession()->set('user_language', $user->getMeta('user_language'));
                }

                return new JsonResponse(_('User saved successfully'));
            } else {
                return new JsonResponse(
                    _('Unable to update the user with that information'),
                    400
                );
            }
        } catch (FileException $e) {
            return new JsonResponse($e->getMessage(), 409);
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function templateParams()
    {
        $groups = $this->get('usergroup_repository')->findBy();
        $languages = $this->container->getParameter('core.locale.available');
        $languages = array_merge(
            array('default' => _('Default system language')),
            $languages
        );

        return array(
            'groups'    => $groups,
            'languages' => $languages
        );
    }
}
