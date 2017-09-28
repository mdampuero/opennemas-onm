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

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        $this->hasRoles(__FUNCTION__, $contentType);

        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);
        $page            = $request->query->getDigits('page', 1);
        $search          = $request->query->get('search');
        $sortBy          = $request->query->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->query->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $em = $this->get('entity_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $results = \Onm\StringUtils::convertToUtf8($results);
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
     * Returns a list of contents in home in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function listHomeAction(Request $request, $contentType)
    {
        $this->hasRoles(__FUNCTION__, $contentType);

        $em = $this->get('entity_repository');

        $order  = '`position` asc';
        $search = [
            'content_type_name' => [ [ 'value' => $contentType ] ],
            'in_home' => [ [ 'value' => 1 ] ],
        ];

        $results = $em->findBy($search, $order);
        $results = \Onm\StringUtils::convertToUtf8($results);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'extra'             => $this->loadExtraData($results),
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
        $this->hasRoles(__FUNCTION__, $contentType);

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
                    && !$content->isOwner($this->getUser()->id)
                ) {
                    $errors[] = [
                        'error',
                        _("You don't have enough privileges for modify this content.")
                    ];
                } else {
                    $content->delete($id);
                    $success[] = array(
                        'id'      => $id,
                        'message' => _('Item deleted successfully'),
                        'type'    => 'success'
                    );
                }
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to delete the item with id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
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
        $this->hasRoles(__FUNCTION__, $contentType);

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
                        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
                            && !$content->isOwner($this->getUser()->id)
                        ) {
                            $errors[] = [
                                'error',
                                sprintf(
                                    _("You don't have enough privileges to delete the content with id %s."),
                                    $content->id
                                )
                            ];
                        } else {
                            $content->delete($id);
                            $updated[] = $id;
                        }
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Unable to delete the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) deleted successfully'), count($updated)),
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
        $this->hasRoles(__FUNCTION__, $contentType);

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
                    'message' => sprintf(_('Unable to restore the item with id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
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
        $this->hasRoles(__FUNCTION__, 'trash');

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
                            'message' => sprintf(_('Unable to restore from trash the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(_('%d item(s) restored successfully'), count($updated)),
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
        $this->hasRoles(__FUNCTION__, 'trash');

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            try {
                $content->remove($id);
                $success[] = array(
                    'id'      => $id,
                    'message' => _('Item permanently removed successfully'),
                    'type'    => 'success'
                );
            } catch (Exception $e) {
                $errors[] = array(
                    'id'      => $id,
                    'message' => sprintf(_('Unable to remove permanently the item with id "%d"'), $id),
                    'type'    => 'error'
                );
            }
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%s"'), $id),
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
        $this->hasRoles(__FUNCTION__, $contentType);

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
                            'message' => sprintf(_('Unable to remove permanently the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(
                    _('%d item(s) permanently removed successfully'),
                    count($updated)
                ),
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
     * Removes contents from trash.
     *
     * @Security("hasExtension('TRASH_MANAGER')
     *     and hasPermission('TRASH_ADMIN')")
     */
    public function emptyTrashAction()
    {
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $contents = $this->get('entity_repository')->findBy([
            'in_litter' => [
                [
                    'operator' => '=',
                    'value'    => '1'
                ]
            ]
        ]);

        if (is_array($contents) && count($contents) > 0) {
            foreach ($contents as $content) {
                $id = $content->id;
                if (!is_null($content->id)) {
                    try {
                        $content->remove($id);
                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Unable to remove permanently the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $updated,
                'message' => sprintf(
                    _('%d item(s) permanently removed successfully'),
                    count($updated)
                ),
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
     * Toggles content available property.
     *
     * @param  integer      $id          Content id.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function setContentStatusAction(Request $request, $id, $contentType)
    {
        $this->hasRoles(__FUNCTION__, $contentType);

        $status  = $request->request->getDigits('value');
        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->setAvailable($status, $this->getUser()->id);

            if ($status) {
                $message = _('Item published successfully');
            } else {
                $message = _('Item unpublished successfully');
            }

            $status    = $content->content_status;
            $success[] = array(
                'id'      => $id,
                'message' => $message,
                'type'    => 'success'
            );
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
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
        $this->hasRoles(__FUNCTION__, $contentType);

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $available = $request->request->get('value');
        $ids       = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->setAvailable(
                            $available,
                            $this->getUser()->id
                        );

                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Unable to update the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            if ($available) {
                $message = sprintf(_('%d item(s) published successfully'), count($updated));
            } else {
                $message = sprintf(_('%d item(s) unpublished successfully'), count($updated));
            }

            $success[] = array(
                'id'      => $updated,
                'message' => $message,
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
        $this->hasRoles(__FUNCTION__, $contentType);

        $em       = $this->get('entity_repository');
        $errors   = array();
        $favorite = null;
        $success  = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->toggleFavorite();

            $favorite = $content->favorite;

            if ($favorite) {
                $message = _('Item added to favorites successfully');
            } else {
                $message = _('Item removed from favorites successfully');
            }

            $success[] = array(
                'id'      => $id,
                'message' => $message,
                'type'    => 'success'
            );
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%s"'), $id),
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
        $this->hasRoles(__FUNCTION__, $contentType);

        $em      = $this->get('entity_repository');
        $errors  = array();
        $inHome  = null;
        $success = array();

        $content = $em->find(\classify($contentType), $id);

        if (!is_null($content->id)) {
            $content->toggleInHome();

            if ($content->in_home) {
                $message = _('Item added to home successfully');
            } else {
                $message = _('Item removed from home successfully');
            }

            $inHome    = $content->in_home;
            $success[] = array(
                'id'      => $id,
                'message' => $message,
                'type'    => 'success'
            );
        } else {
            $errors[] = array(
                'id'      => $id,
                'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
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
        $this->hasRoles(__FUNCTION__, $contentType);

        $em      = $this->get('entity_repository');
        $errors  = array();
        $success = array();
        $updated = array();

        $inHome = $request->request->get('value');
        $ids    = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $content = $em->find(\classify($contentType), $id);

                if (!is_null($content->id)) {
                    try {
                        $content->setInHome(
                            $inHome,
                            $this->getUser()->id
                        );

                        $updated[] = $id;
                    } catch (Exception $e) {
                        $errors[] = array(
                            'id'      => $id,
                            'message' => sprintf(_('Unable to update the item with id "%d"'), $id),
                            'type'    => 'error'
                        );
                    }
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to find the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }
            }
        }

        if ($updated > 0) {
            if ($inHome) {
                $message = sprintf(_('%d item(s) added to home successfully'), count($updated));
            } else {
                $message = sprintf(_('%d item(s) removed from home successfully'), count($updated));
            }

            $success[] = array(
                'id'      => $updated,
                'message' => $message,
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
        $this->hasRoles(__FUNCTION__, $contentType);

        $errors    = array();
        $positions = $request->request->get('positions');
        $success   = array();
        $updated   = array();

        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $contentType = \classify($contentType);
                $file        = new $contentType($id);

                if ($file->setPosition($pos)) {
                    $updated[] = $id;
                } else {
                    $errors[] = array(
                        'id'      => $id,
                        'message' => sprintf(_('Unable to save the position for the item with id "%d"'), $id),
                        'type'    => 'error'
                    );
                }

                $pos += 1;
            }
        }

        if ($updated > 0) {
            $success[] = array(
                'id'      => $id,
                'message' => _('Positions saved successfully'),
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
    protected function hasRoles($action, $contentType)
    {
        $permissions = [];
        $types[]     = $contentType;

        // Add all admin permissions for generic list (trash,)
        if ($contentType == 'content') {
            $types = [ 'advertisement', 'album', 'article', 'book', 'cover',
                'file', 'letter', 'opinion', 'photo', 'poll', 'special',
                'static', 'trash', 'video', 'widget',
            ];
        }

        $permissions = array_map(function ($a) {
            return strtoupper($a) . '_ADMIN';
        }, $types);

        switch ($action) {
            case 'batchSendToTrashAction':
            case 'sendToTrashAction':
                $permissions[] = strtoupper($contentType) . '_DELETE';
                break;
            case 'batchSetContentStatusAction':
            case 'setContentStatusAction':
                $permissions[] = strtoupper($contentType) . '_AVAILABLE';
                break;
            case 'batchToggleFavoriteAction': // Not implemented
            case 'toggleFavoriteAction':
                $permissions[] = strtoupper($contentType) . '_FAVORITE';
                break;
            case 'batchToggleInHomeAction':
            case 'toggleInHomeAction':
                $permissions[] = strtoupper($contentType) . '_HOME';
                break;
        }

        $msg      = $this->get('core.messenger');
        $security = $this->get('core.security');
        foreach ($permissions as $permission) {
            if (!$security->hasPermission($permission)) {
                $msg->add(sprintf(_('Access denied (%s)'), $permission), 'error', 500);

                return new JsonResponse($msg->getMessages(), $msg->getCode());
            }
        }
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param  array $contents Array of contents.
     * @return array           Array of extra data.
     */
    protected function loadExtraData($contents)
    {
        if (empty($contents)) {
            return [];
        }

        $extra      = [];
        $ids        = [];
        $contentIds = [];

        foreach ($contents as $content) {
            $ids[] = $content->fk_author;
            $ids[] = $content->fk_publisher;
            $ids[] = $content->fk_user_last_editor;

            $contentIds[] = $content->id;
        }

        // Fetch all content views at once
        //$vm = $this->get('content_views_repository');
        //$extra['views'] = $vm->getViews($contentIds);

        $ids = array_unique($ids);

        if (($key = array_search(0, $ids)) !== false) {
            unset($ids[$key]);
        }

        if (($key = array_search(null, $ids)) !== false) {
            unset($ids[$key]);
        }

        $extra['authors'] = [];
        if (!empty($ids)) {
            $converter = $this->get('orm.manager')->getConverter('User');
            $users     = $this->get('orm.manager')->getRepository('User')
                ->findBy(sprintf('id in [%s]', implode(',', $ids)));

            foreach ($users as $user) {
                $user->eraseCredentials();

                $extra['authors'][$user->id] = $converter->responsify($user->getData());
            }
        }

        $ccm                 = \ContentCategoryManager::get_instance();
        $categories          = $ccm->findAll();
        $extra['categories'] = [];
        $fm                  = $this->get('data.manager.filter');
        $categories          = $fm->set($categories)->filter('localize', [
            'keys' => \ContentCategory::getMultiLanguageFields(),
            'locale' => $this->getLocaleData('frontend')['default']
        ])->get();

        foreach ($categories as $category) {
            $extra['categories'][$category->id] = $category->name;
        }

        return $extra;
    }
}
