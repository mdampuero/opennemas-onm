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

        // Remove content type to search all contents
        if ($contentType == 'content') {
            unset($search['content_type_name']);
        }

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'page'              => $page,
                'results'           => $results,
                'total'             => $total
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
    public function deleteAction($id, $contentType)
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
    public function batchDeleteAction(Request $request, $contentType)
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
     * Toggles content available property.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function toggleAvailableAction($id, $contentType)
    {
        $available = null;
        $em        = $this->get('entity_repository');
        $errors    = array();
        $success   = array();

        $content   = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->toggleAvailable();

            $available = $content->available;
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
                'available' => $available,
                'errors'    => $errors,
                'success'   => $success
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
    public function batchToggleAvailableAction(Request $request, $contentType)
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
}
