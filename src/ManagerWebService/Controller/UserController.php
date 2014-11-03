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
        $success = false;
        $message = array();

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

                $success = true;
                $message = array(
                    'id'   => $user->id,
                    'type' => 'success',
                    'text' => _('User saved successfully')
                );
            } else {
                $message = array(
                    'type' => 'error',
                    'text' => _('Unable to create the user with that information')
                );
            }
        } catch (\Exception $e) {
            $message = array(
                'type' => 'error',
                'text' => $e->getMessage()
            );
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
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
        $um      = $this->get('user_repository');
        $message = array();
        $success = false;

        $user = $um->find($id);

        if ($user) {
            $user->delete($id);
            $user->deleteMeta($id);

            $success = true;
            $message = array(
                'type' => 'success',
                'text' => _('User deleted successfully')
            );
        } else {
            $message = array(
                'type' => 'error',
                'text' =>  sprintf(_('Unable to delete the user with the id "%d"'), $id),
            );
        }

        return new JsonResponse(
            array(
                'success' => $success,
                'message' => $message
            )
        );
    }

    /**
     * Deletes the selected instances.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function deleteSelectedAction(Request $request)
    {
        $messages = array();
        $success  = false;
        $updated  = 0;

        $selected  = $request->request->get('selected', null);

        if (is_array($selected) && count($selected) > 0) {
            $um = $this->get('user_repository');

            foreach ($selected as $id) {
                $user = $um->find($id);

                if ($user) {
                    $user->delete($id);
                    $user->deleteMeta($id);

                    $updated++;
                } else {
                    $messages[] = array(
                        'type' => 'error',
                        'text' =>  sprintf(_('Unable to delete the user with the id "%d"'), $id),
                    );
                }
            }
        }

        if (count($updated) > 0) {
            $success = true;

            array_unshift(
                $messages,
                array(
                    'text' => sprintf(_('%s users deleted successfully.'), count($updated)),
                    'type' => 'success'
                )
            );
        }

        return new JsonResponse(
            array(
                'success'  => $success,
                'messages' => $messages
            )
        );
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
        $epp      = $request->request->getDigits('epp', 10);
        $page     = $request->request->getDigits('page', 1);
        $criteria = $request->request->filter('criteria') ? : array();
        $orderBy  = $request->request->filter('orderBy') ? : array();

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

        return new JsonResponse(
            array(
                'epp'      => $epp,
                'template' => array(
                    'groups' => $groups
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
                'data'     => null,
                'template' => $this->templateParams()
            )
        );
    }

    /**
     * Enables/disables an user given its id.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function setEnabledAction(Request $request, $id)
    {
        $success  = false;
        $activated = $request->request->getDigits('enabled');
        $message   = array();

        $user = $this->get('user_repository')->find($id);
        if ($user) {
            try {
                if ($activated) {
                    $user->activateUser($id);
                } else {
                    $user->deactivateUser($id);
                }

                $success = true;
                $message = array(
                    'text'      => _('User updated successfully.'),
                    'type'      => 'success'
                );
            } catch (Exception $e) {
                $message = array(
                    'text' => sprintf(_('Error while updating user with id "%s"'), $id),
                    'type' => 'error'
                );
            }
        } else {
            $message = array(
                'text' => sprintf(_('Unable to find the user with id "%s"'), $id),
                'type' => 'error'
            );
        }

        return new JsonResponse(
            array(
                'success'   => $success,
                'activated' => $activated,
                'message'   => $message
            )
        );
    }

    /**
     * Set the activated flag for instances in batch.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function setEnabledSelectedAction(Request $request)
    {
        $messages = array();
        $success  = false;
        $updated  = 0;

        $selected  = $request->request->get('selected', null);
        $activated = $request->request->getDigits('enabled', 0);

        if (is_array($selected) && count($selected) > 0) {
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

                        $updated++;
                    } catch (Exception $e) {
                        $messages[] = array(
                            'text' => sprintf(_('Error while updating user with id "%s"'), $id),
                            'type' => 'error'
                        );
                    }
                } else {
                    $messages[] = array(
                        'text' => sprintf(_('Unable to find the user with id "%s"'), $id),
                        'type' => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success = true;

            array_unshift(
                $messages,
                array(
                    'text' => sprintf(_('%s users updated successfully.'), $updated),
                    'type' => 'success'
                )
            );
        }

        return new JsonResponse(
            array(
                'success'  => $success,
                'messages' => $messages
            )
        );
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
        $user->eraseCredentials();

        return new JsonResponse(
            array(
                'data'     => $user,
                'template' => $this->templateParams()
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

        return new JsonResponse(
            array(
                'data'     => $user,
                'template' => $this->templateParams()
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
        $success = false;
        $message = array();

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

                $this->dispatchEvent('user.update', array('id' => $id));

                if ($id == $this->getUser()->id) {
                    $request->getSession()->set('user_language', $user->getMeta('user_language'));
                }

                $success = true;
                $message = array(
                    'type' => 'success',
                    'text' => _('User saved successfully')
                );
            } else {
                $message = array(
                    'type' => 'error',
                    'text' => _('Unable to update the user with that information')
                );
            }
        } catch (FileException $e) {
            $message = array(
                'type' => 'error',
                'text' => $e->getMessage()
            );
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
        $languages = $this->container->getParameter('available_languages');
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
