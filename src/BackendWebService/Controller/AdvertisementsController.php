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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

class AdvertisementsController extends ContentController
{
    /**
     * Deletes multiple advertisements at once give them ids
     *
     * @param  Request $request the request object
     * @return Response         the response object
     *
     * @Security("has_role('ADVERTISEMENT_DELETE')")
     */
    public function batchDeleteAction(Request $request)
    {
        $status  = 'OK';
        $errors  = array();
        $success = array();

        $ids = $request->request->get('ids');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $advertisement = new \Advertisement($id);

                if (!is_null($advertisement->id)) {
                    try {
                        $advertisement->remove($id);
                        $success[] = _('Advertisement deleted successfully.');
                    } catch (Exception $e) {
                        $errors[] = sprintf(_('Unable to delete the advertisement "%s".'), $advertisement->name);
                    }
                }
            }
        }

        return new JsonResponse(array('status' => $status, 'errors' => $errors, 'success' => $success));
    }

    /**
     * Deletes a advertisement.
     *
     * @param  integer      $id Menu id.
     * @return JsonResponse     The response of the current action.
     *
     * @Security("has_role('ADVERTISEMENT_DELETE')")
     */
    public function deleteAction($id)
    {
        $status  = 'ERROR';
        $message = _('You must give an id for delete the advertisement.');

        $advertisement = new \Advertisement($id);

        if (!is_null($id)) {
            try {
                $advertisement->remove($id);

                $status  = 'OK';
                $message = _('Advertisement deleted successfully.');
            } catch (Exception $e) {
                // Continue
            }
        }

        return new JsonResponse(array('status' => $status, 'message' => $message));
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request The request with the search parameters.
     * @return JsonResponse          The response in JSON format.
     *
     * @Security("has_role('ADVERTISEMENT_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $elementsPerPage = $request->request->getDigits('elements_per_page', 10);
        $page            = $request->request->getDigits('page', 1);
        $search          = $request->request->get('search');
        $sortBy          = $request->request->filter('sort_by', null, FILTER_SANITIZE_STRING);
        $sortOrder       = $request->request->filter('sort_order', 'asc', FILTER_SANITIZE_STRING);

        $positionManager = $this->container->get('instance_manager')
            ->current_instance->theme->getAdsPositionManager();
        $map = $positionManager->getAllAdsPositions();

        $em = $this->get('advertisement_repository');

        $order = null;
        if ($sortBy) {
            $order = '`' . $sortBy . '` ' . $sortOrder;
        }

        $results = $em->findBy($search, $order, $elementsPerPage, $page);
        $total   = $em->countBy($search);

        return new JsonResponse(
            array(
                'elements_per_page' => $elementsPerPage,
                'map'               => $map,
                'page'              => $page,
                'results'           => $results,
                'total'             => $total
            )
        );
    }

    /**
     * Toggles advertisement availability.
     *
     * @param  integer      $id advertisement id.
     * @return JsonResponse     The response of the current action.
     *
     * @Security("has_role('ADVERTISEMENT_AVAILA')")
     */
    public function toggleAvailableAction($id)
    {
        $status  = 'ERROR';
        $message = _('You must give an id for delete the advertisement.');

        $em     = $this->get('entity_repository');
        $advertisement = $em->find('advertisement', $id);

        if (!$advertisement) {
            $message = sprintf(_('Unable to find advertisement with id "%d"'), $id);
        } else {
            $advertisement->toggleAvailable();

            $status  = 'OK';
            $message = sprintf(_('Successfully changed availability for "%s" advertisement'), $advertisement->title);
        }

        return new JsonResponse(
            array(
                'status' => $status,
                'message' => $message,
                'available' => $advertisement->available
            )
        );
    }

    /**
     * Deletes multiple advertisements at once give them ids
     *
     * @param  Request $request the request object
     * @return Response         the response object
     *
     * @Security("has_role('ADVERTISEMENT_AVAILA')")
     */
    public function batchToggleAvailableAction(Request $request)
    {
        $status  = 'OK';
        $errors  = array();
        $success = array();

        $ids       = $request->request->get('ids');
        $available = $request->request->get('available');

        if (is_array($ids) && count($ids) > 0) {
            foreach ($ids as $id) {
                $advertisement = new \Advertisement($id);

                if (!is_null($advertisement->id)) {
                    try {
                        $advertisement->set_available($available, $_SESSION['userid']);
                        $success[] = sprintf(_('Successfully changed availability for "%s" advertisement'), $advertisement->title);
                    } catch (Exception $e) {
                        $errors[] = sprintf(_('Unable to change the advertisement availability for "%s" advertisement'), $advertisement->name);
                    }
                }
            }
        }

        return new JsonResponse(array('status' => $status, 'errors' => $errors, 'success' => $success));
    }
}
