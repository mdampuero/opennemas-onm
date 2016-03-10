<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <onm-devs@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Framework\ORM\Entity\Purchase;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Handles the Purchase resource.
 */
class PurchaseController extends Controller
{
    /**
     * @api {delete} /purchases/:id Delete a purchase
     * @apiName DeletePurchase
     * @apiGroup Purchase
     *
     * @apiSuccess {String} message The success message.
     */
    public function deleteAction($id)
    {
        $purchase = $this->get('orm.manager')
            ->getRepository('purchase', 'Database')
            ->find($id);

        $this->get('orm.manager')->remove($purchase, 'Database');

        return new JsonResponse(_('Purchased removed successfully'));
    }

    /**
     * @api {delete} /purchases/ Delete selected purchases
     * @apiName DeletePuchases
     * @apiGroup Purchase
     *
     * @apiParam {Integer} selected The clients ids.
     *
     * @apiSuccess {String} message The success message.
     */
    public function deleteSelectedAction(Request $request)
    {
        $error      = [];
        $messages   = [];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = 0;

        if (empty($selected) || !is_array($selected)) {
            $msg->add(
                _('Unable to find the instances for the given criteria'),
                'error',
                400
            );

            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em       = $this->get('orm.manager');
        $criteria = [ 'id' => [ [ 'value' => $selected, 'operator' => 'IN'] ] ];
        $purchases  = $em->getRepository('purchase', 'Database')->findBy($criteria);

        foreach ($purchases as $purchase) {
            try {
                $em->remove($purchase, 'Database');
                $updated++;
            } catch (EntityNotFoundException $e) {
                $error[]    = $purchase->id;
                $messages[] = [
                    'message' => sprintf(_('Unable to find the purchase with id "%s"'), $client->id),
                    'type'    => 'error'
                ];
            } catch (\Exception $e) {
                $error[]    = $purchase->id;
                $messages[] = [
                    'message' => _($e->getMessage()),
                    'type'    => 'error'
                ];
            }
        }

        if ($updated > 0) {
            $messages = [
                'message' => sprintf(_('%d purchases deleted successfully.'), $updated),
                'type'    => 'success'
            ];
        }

        // Return the proper status code
        if (count($error) > 0 && $updated > 0) {
            $statusCode = 207;
        } elseif (count($error) > 0) {
            $statusCode = 409;
        }

        return new JsonResponse(
            [ 'error' => $error, 'messages' => $messages ],
            $statusCode
        );
    }

    /**
     * @api {get} /purchases List of purchases
     * @apiName GetPurchases
     * @apiGroup Purchase
     *
     * @apiParam {String} client  The client's name or email.
     * @apiParam {String} from    The start date.
     * @apiParam {String} to      The finish date.
     * @apiParam {String} orderBy The values to sort by.
     * @apiParam {Number} epp     The number of elements per page.
     * @apiParam {Number} page    The current page.
     *
     * @apiSuccess {Integer} epp     The number of elements per page.
     * @apiSuccess {Integer} page    The current page.
     * @apiSuccess {Integer} total   The total number of elements.
     * @apiSuccess {Array}   results The list of purchases.
     */
    public function listAction(Request $request)
    {
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : [];
        $orderBy  = $request->query->filter('orderBy') ? : [];
        $extra    = $this->getTemplateParams();

        unset($extra['countries']);

        $order = [];
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        $repository = $this->get('orm.manager')
            ->getRepository('manager.purchase');

        if (array_key_exists('client', $criteria)) {
            $value = $criteria['client'][0]['value'];

            $criteria['client_id'] =
                $criteria['payment_id'] =
                $criteria['invoice_id'] = [
                    [ 'value' => $value, 'operator' => 'like' ]
                ];

            $criteria['union'] = 'OR';

            unset($criteria['name']);
        }

        if (array_key_exists('from', $criteria)) {
            $criteria['created'][] = [
                'value' => $criteria['from'][0]['value'] . ' 00:00:00',
                'operator' => '>='
            ];

            unset($criteria['from']);
        }

        if (array_key_exists('to', $criteria)) {
            $criteria['created'][] =
                [
                    'value' => $criteria['to'][0]['value'] . ' 23:59:59',
                    'operator' => '<='
                ];

            unset($criteria['to']);
        }

        if (array_key_exists('from', $criteria)
            && array_key_exists('to', $criteria)
        ) {
            $criteria['created']['union'] = 'AND';
        }

        $ids       = [];
        $purchases = $repository->findBy($criteria, $order, $epp, $page);
        $total     = $repository->countBy($criteria);

        // Clean purchases
        foreach ($purchases as &$purchase) {
            $purchase->client = $purchase->client->getData();

            $ids[]    = $purchase->instance_id;
            $purchase = $purchase->getData();
        }

        // Find instances by ids
        if (!empty($ids)) {
            $extra['instances'] = $this->get('instance_manager')->findBy([
                'id' => [ [ 'value' => $ids, 'operator' => 'IN' ] ]
            ]);
        }

        return new JsonResponse([
            'epp'     => $epp,
            'extra'   => $extra,
            'page'    => $page,
            'results' => $purchases,
            'total'   => $total,
        ]);
    }

    /**
     * @api {get} /purchases/:id Show a purchase
     * @apiName GetPurchase
     * @apiGroup Purchase
     *
     * @apiSuccess {Array} purchase The purchases.
     */
    public function showAction($id)
    {
        $purchase = $this->get('orm.manager')
            ->getRepository('manager.purchase', 'Database')
            ->find($id);

        $purchase->client = $purchase->client->getData();

        return new JsonResponse([
            'purchase' => $purchase->getData(),
            'extra'    => $this->getTemplateParams()
        ]);
    }

    /**
     * Returns an array with extra parameters for template.
     *
     * @return array Array of extra parameters for template.
     */
    protected function getTemplateParams()
    {
        $countries = Intl::getRegionBundle()
            ->getCountryNames(CURRENT_LANGUAGE_SHORT);

        asort($countries);

        return [
            'braintree'  => [
                'url'         => $this->getparameter('braintree.url'),
                'merchant_id' => $this->getparameter('braintree.merchant_id')
            ],
            'countries'  => $countries,
            'freshbooks' => [
                'url' => $this->getparameter('freshbooks.url')
            ]
        ];
    }
}
