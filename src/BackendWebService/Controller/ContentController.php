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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                $content->delete($id);
                $success[] = array(
                    'id'   => $id,
                    'text' => _('Item deleted successfully')
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'   => $id,
                    'text' => _('Unable to delete item with id "$id"')
                );
            }
        } else {
            $errors[] = array(
                'id'   => $id,
                'text' => _('Unable to find item with id "$id"')
            );
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->delete($id);
                        $success[] = array(
                            'id'   => $id,
                            'text' => _('Selected items deleted successfully')
                        );
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'   => $id,
                            'text' => _('Unable to delete item with id "$id"')
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'   => $id,
                        'text' => _('Unable to find item with id "$id"')
                    );
                }
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                $content->restoreFromTrash($id);
                $success[] = array(
                    'id'   => $id,
                    'text' => _('Item restored successfully')
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'   => $id,
                    'text' => _('Unable to restore the item with id "$id"')
                );
            }
        } else {
            $errors[] = array(
                'id'   => $id,
                'text' => _('Unable to find the item with id "$id"')
            );
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->restoreFromTrash($id);
                        $success[] = array(
                            'id'   => $id,
                            'text' => _('Selected items restored from trash successfully')
                        );
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'   => $id,
                            'text' => _('Unable to restore from trash the item with id "$id"')
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'   => $id,
                        'text' => _('Unable to find item with id "$id"')
                    );
                }
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                $content->remove($id);
                $success[] = array(
                    'id'   => $id,
                    'text' => _('Item removed permanently successfully')
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'   => $id,
                    'text' => _('Unable to remove permanently the item with id "$id"')
                );
            }
        } else {
            $errors[] = array(
                'id'   => $id,
                'text' => _('Unable to find the item with id "$id"')
            );
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->remove($id);
                        $success[] = array(
                            'id'   => $id,
                            'text' => _('Selected items restored from trash successfully')
                        );
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'   => $id,
                            'text' => _('Unable to restore from trash the item with id "$id"')
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'   => $id,
                        'text' => _('Unable to find item with id "$id"')
                    );
                }
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $status  = $request->request->getDigits('status');

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content   = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->set_available($status, $this->getUser()->id);

            $status = $content->available;
            $success[] = array(
                'id'   => $id,
                'text' => _('Item updated successfully')
            );
        } else {
            $errors[] = array(
                'id'   => $id,
                'text' => _('Unable to find item with id "$id"')
            );
        }

        return new JsonResponse(
            array(
                'content_status' => $status,
                'errors'         => $errors,
                'success'        => $success
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

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

                        $success[] = array(
                            'id'   => $id,
                            'text' => _('Selected items updated successfully')
                        );
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'   => $id,
                            'text' => _('Unable to update item with id "$id"')
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'   => $id,
                        'text' => _('Unable to find item with id "$id"')
                    );
                }
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
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
        $em       = $this->get('entity_repository');
        $errors   = array();
        $favorite = null;
        $success  = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->toggleFavorite();

            $favorite = $content->favorite;
            $success[] = array(
                'id'   => $id,
                'text' => _('Item updated successfully')
            );
        } else {
            $errors[] = array(
                'id'   => $id,
                'text' => _('Unable to find item with id "$id"')
            );
        }

        return new JsonResponse(
            array(
                'errors'   => $errors,
                'favorite' => $favorite,
                'success'  => $success,
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $inHome  = null;
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->toggleInHome();

            $inHome = $content->in_home;
            $success[] = array(
                'id'   => $id,
                'text' => _('Item updated successfully')
            );
        } else {
            $errors[] = array(
                'id'   => $id,
                'text' => _('Unable to find item with id "$id"')
            );
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'in_home' => $inHome,
                'success' => $success
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
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

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

                        $success[] = array(
                            'id'   => $id,
                            'text' => _('Selected items updated successfully')
                        );
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'   => $id,
                            'text' => _('Unable to update item with id "$id"')
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'   => $id,
                        'text' => _('Unable to find item with id "$id"')
                    );
                }
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
            )
        );
    }

    /**
     * Save positions for widget.
     *
     * @param  Request $request the request object
     * @return Response the response object
     */
    public function savePositionsAction(Request $request)
    {
        $errors    = array();
        $positions = $request->request->get('positions');
        $success   = array();

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $file= new \Attachment($id);

                if ($file->setPosition($pos)) {
                    $success[] = array(
                        'id' => $id,
                        'message' => 'Position saved successfully'
                    );
                } else {
                    $errors[] = array(
                        'id' => $id,
                        'message' => 'Unable to save position'
                    );
                }

                $pos += 1;
            }
        }

        return new JsonResponse(
            array(
                'errors'  => $errors,
                'success' => $success
            )
        );
    }

    /**
     * Checks if the current user has roles to execute the required action.
     *
     * @param  string  $contentType Content type name.
     * @param  string  $action      Required action.
     * @return boolean              [description]
     */
    private function hasRoles($contentType, $action)
    {
        $roles    = $this->getUser()->getRoles();
        $required = array();

        $required[] = strtoupper($contentType) . '_ADMIN';

        switch ($action) {
            case 'batchDelete':
            case 'delete':
                $required[] = strtoupper($contentType) . '_DELETE';
                break;
            case 'batchToggleAvailable':
            case 'toggleAvailable':
                $required[] = strtoupper($contentType) . '_AVAILABLE';
                break;
            case 'batchToggleFavorite': // Not implemented
            case 'toggleFavorite':
                $required[] = strtoupper($contentType) . '_FAVORITE';
                break;
            case 'batchToggleInHome':
            case 'toggleInHome':
                $required[] = strtoupper($contentType) . '_HOME';
                break;
        }

        return empty(array_diff($required, $roles));
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
