<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BackendWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class ContentController extends Controller
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function listAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }


        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $em = $this->get('entity_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'extra'             => $this->loadExtraData($results),
                'page'              => $page,
                'results'           => $results,
                'total'             => $total,
            )
        );
    }

    /**
     * Deletes a content.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function sendToTrashAction($id, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                $content->delete($id);
                $success[] = array(
                    'id'      => $id,
                    'message' => _('Item deleted successfully'),
                    'type'    => 'success'
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => _('Unable to delete item with id "' . $id . '"'),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => _('Unable to find item with id "' . $id . '"'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors),
            )
        );
    }

    /**
     * Deletes multiple contents at once give them ids.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchSendToTrashAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->delete($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => _('Unable to delete item with id "' . $id . '"'),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => _('Unable to find item with id "' . $id . '"'),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => _(count($updated) . ' item(s) deleted successfully'),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Restores a content.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function restoreFromTrashAction($id, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                $content->restoreFromTrash($id);
                $success[] = array(
                    'id'      => $id,
                    'message' => _('Item restored successfully'),
                    'type'    => 'success'
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => _('Unable to restore the item with id "' . $id . '"'),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => _('Unable to find the item with id "' . $id . '"'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Deletes multiple contents at once give them ids.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchRestoreFromTrashAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->restoreFromTrash($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => _('Unable to restore from trash the item with id "' . $id . '"'),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => _('Unable to find item with id "' . $id . '"'),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => _(count($updated) . ' item(s) restored successfully'),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors)
            )
        );
    }

    /**
     * Deletes a content.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function removePermanentlyAction($id, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                $content->remove($id);
                $success[] = array(
                    'id'      => $id,
                    'message' => _('Item removed permanently successfully'),
                    'type'    => 'success'
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => _('Unable to remove permanently the item with id "' . $id . '"'),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => _('Unable to find the item with id "' . $id . '"'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Deletes multiple contents at once give them ids.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchRemovePermanentlyAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->remove($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => _('Unable to remove permanently the item with id "' . $id . '"'),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => _('Unable to find item with id "' . $id . '"'),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => _(count($updated) . ' item(s) removed successfully'),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'message'  => array_merge($success, $errors)
            )
        );
    }

    /**
     * Toggles content available property.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function setContentStatusAction(Request $request, $id, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $status  = $request->request->getDigits('status');

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content   = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->set_available($status, $this->getUser()->id);

            $status = $content->available;
            $success[] = array(
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            );
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => _('Unable to find item with id "' . $id . '"'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'content_status' => $status,
                'messages'       => array_merge($success, $errors)
            )
        );
    }

    /**
     * Updates contents available property.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchSetContentStatusAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $available = $request->request->get('available');
        $ids       = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->set_available(
                            $available,
                            $this->getUser()->id
                        );

                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => _('Unable to update item with id "' . $id . '"'),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => _('Unable to find item with id "' . $id . '"'),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => _(count($updated) . ' item(s) updated successfully'),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors)
            )
        );
    }

    /**
     * Toggles content favorite property.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function toggleFavoriteAction($id, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em       = $this->get('entity_repository');
        $errors   = array();
        $favorite = null;
        $success  = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->toggleFavorite();

            $favorite = $content->favorite;
            $success[] = array(
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            );
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => _('Unable to find item with id "' . $id . '"'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'favorite' => $favorite,
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Toggles content in_home property.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function toggleInHomeAction($id, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $inHome  = null;
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->toggleInHome();

            $inHome = $content->in_home;
            $success[] = array(
                'id'      => $id,
                'message' => _('Item updated successfully'),
                'type'    => 'success'
            );
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => _('Unable to find item with id "' . $id . '"'),
                'type'    => 'error'
            );
        }

        return new JsonResponse(
            array(
                'in_home'  => $inHome,
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Updates contents in_home property.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function batchToggleInHomeAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $inHome = $request->request->get('in_home');
        $ids       = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->set_in_home(
                            $inHome,
                            $this->getUser()->id
                        );

                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => _('Unable to update item with id "' . $id . '"'),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => _('Unable to find item with id "' . $id . '"'),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => _(count($updated) . ' item(s) updated successfully'),
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages' => array_merge($success, $errors)
            )
        );
    }

    /**
     * Save positions for widget.
     *
     * @param  Request $request the request object
     * @return Response the response object
     */
    public function savePositionsAction(Request $request, $contentType)
    {
        list($hasRoles, $required) = $this->hasRoles(__FUNCTION__, $contentType);

        if (!$hasRoles) {
            $roles = '';
            foreach ($required as $role) {
                $roles .= $role;
            }
            $roles = rtrim($roles, ',');

            return new JsonResponse(
                array(
                    'messages' => array(
                        array(
                            'id'      => '500',
                            'type'    => 'error',
                            'message' => 'Access denied (' . $roles . ')'
                        )
                    )
                )
            );
        }

        $errors    = array();
        $positions = $request->request->get('positions');
        $success   = array();
        $updated   = 0;

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $file= new \Attachment($id);

                if ($file->setPosition($pos)) {
                    $updated[] = $id;
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => 'Unable to save position for item with id "' . $id . '"',
                        'type'    => 'error'
                    );
                }

                $pos += 1;
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $id,
                'message' => 'Positions saved successfully',
                'type'    => 'success'
            );
        }

        return new JsonResponse(
            array(
                'messages'  => array_merge($success, $errors)
            )
        );
    }

    /**
     * Checks if the current user has roles to execute the required action.
     *
     * @param  string  $action      Required action.
     * @param  string  $contentType Content type name.
     * @return boolean              [description]
     */
    private function hasRoles($action, $contentType)
    {
        $required = array();
        $roles    = $this->getUser()->getRoles();
        $types    = array();

        // Add all admin roles for generic list (trash,)
        if ($contentType == 'content') {
            $type = [ 'advertisement', 'album', 'article', 'book', 'cover',
                'file', 'letter', 'opinion', 'photo', 'poll', 'special',
                'static', 'video', 'widget',
            ];
        } else {
            $type[] = $contentType;
        }

        foreach ($types as $type) {
            $required[] = strtoupper($type) . '_ADMIN';
        }

        switch ($action) {
            case 'batchSendToTrashAction':
            case 'sendToTrashAction':
            case 'batchRemovePermanentlyAction':
            case 'removePermanentlyAction':
                $required[] = strtoupper($contentType) . '_DELETE';
                break;
            case 'batchSetContentStatusAction':
            case 'setContentStatusAction':
                $required[] = strtoupper($contentType) . '_AVAILABLE';
                break;
            case 'batchToggleFavoriteAction': // Not implemented
            case 'toggleFavoriteAction':
                $required[] = strtoupper($contentType) . '_FAVORITE';
                break;
            case 'batchToggleInHomeAction':
            case 'toggleInHomeAction':
                $required[] = strtoupper($contentType) . '_HOME';
                break;
        }

        return array(
            empty(array_diff($required, $roles)),
            array_diff($required, $roles)
        );
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param  array $contents Array of contents.
     * @return array           Array of extra data.
     */
    private function loadExtraData($contents)
    {
        $extra = array();

        $ids = array();

        foreach ($contents as $content) {
            $ids[] = $content->fk_author;
            $ids[] = $content->fk_publisher;
            $ids[] = $content->fk_user_last_editor;
        }
        $ids = array_unique($ids);

        if (($key = array_search(0, $ids)) !== false) {
            unset($ids[$key]);
        }

        $users = $this->get('user_repository')->findMulti($ids);

        $extra['authors'] = array();
        foreach ($users as $user) {
            $extra['authors'][$user->id] = $user->eraseCredentials();
        }


        return $extra;
    }
}
