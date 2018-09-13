<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use League\Csv\Writer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;

/**
 * Displays, saves, modifies and removes purchases.
 */
class PurchaseController extends Controller
{
    /**
     * @api {delete} /purchases/:id Delete a purchase
     * @apiName DeletePurchase
     * @apiGroup Purchase
     *
     * @apiSuccess {String} message The success message.
     *
     * @Security("hasPermission('PURCHASE_DELETE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $purchase = $em->getRepository('Purchase')->find($id);

        $em->remove($purchase);
        $msg->add(_('Purchase deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * @api {delete} /purchases Delete selected purchases
     * @apiName DeletePuchases
     * @apiGroup Purchase
     *
     * @apiParam {Integer} ids The clients ids.
     *
     * @apiSuccess {Object} The success message.
     *
     * @Security("hasPermission('PURCHASE_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids = $request->request->get('ids', []);
        $msg = $this->get('core.messenger');

        if (!is_array($ids) || empty($ids)) {
            $msg->add(_('Bad request'), 'error', 400);
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $em  = $this->get('orm.manager');
        $oql = sprintf('id in [%s]', implode(',', $ids));

        $purchases = $em->getRepository('Purchase')->findBy($oql);

        $deleted = 0;
        foreach ($purchases as $purchase) {
            try {
                $em->remove($purchase);
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s purchases deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * @api {get} /purchases.csv Returns the report with purchases.
     * @apiName ExportAll
     * @apiGroup Purchase
     *
     * @Security("hasPermission('PURCHASE_REPORT')")
     */
    public function exportAllAction()
    {
        $sql = 'SELECT purchase.id, client, internal_name as instance,'
            . ' contact_mail as instance_email, details as items,'
            . ' purchase.created, purchase.updated, step, method'
            . ' FROM purchase, instances'
            . ' WHERE instances.id = purchase.instance_id';

        $data = $this->get('orm.manager')->getConnection('manager')->fetchAll($sql);

        return $this->export($data, 'all');
    }

    /**
     * @api {get} /purchases/completed.csv Returns the report with uncompleted
     *                                     purchases.
     * @apiName ExportCompleted
     * @apiGroup Purchase
     *
     * @Security("hasPermission('PURCHASE_REPORT')")
     */
    public function exportCompletedAction()
    {
        $sql = 'SELECT purchase.id, client, internal_name as instance,'
            . ' contact_mail as instance_email, details as items,'
            . ' purchase.created, purchase.updated, step, method'
            . ' FROM purchase, instances'
            . ' WHERE instances.id = purchase.instance_id AND step = "done"';

        $data = $this->get('orm.manager')->getConnection('manager')->fetchAll($sql);

        return $this->export($data, 'completed');
    }

    /**
     * @api {get} /purchases/uncompleted.csv Returns the report with uncompleted
     *                                       purchases.
     * @apiName ExportUncompleted
     * @apiGroup Purchase
     *
     * @Security("hasPermission('PURCHASE_REPORT')")
     */
    public function exportUncompletedAction()
    {
        $sql = 'SELECT purchase.id, client, internal_name as instance,'
            . ' contact_mail as instance_email, details as items,'
            . ' purchase.created, purchase.updated, step, method'
            . ' FROM purchase, instances'
            . ' WHERE instances.id = purchase.instance_id AND step != "done"';

        $data = $this->get('orm.manager')->getConnection('manager')->fetchAll($sql);

        return $this->export($data, 'uncompleted');
    }

    /**
     * @api {get} /purchases/:id.pdf Get PDF for purchase
     * @apiName GetPurchasePDF
     * @apiGroup Purchase
     *
     * @apiParam {String} id  The purchase id.
     *
     * @Security("hasPermission('PURCHASE_REPORT')")
     */
    public function getPdfAction($id)
    {
        $em = $this->get('orm.manager');

        $purchase = $em->getRepository('Purchase')->find($id);

        $pdf = $em->getRepository('invoice', 'freshbooks')
            ->getPDF($purchase->invoice_id);

        $response = new Response($pdf);

        $response->headers->set('Content-Type', 'application/pdf');

        return $response;
    }

    /**
     * @api {get} /purchases List of purchases
     * @apiName GetPurchases
     * @apiGroup Purchase
     *
     * @apiParam {String} oql The OQL query.
     *
     * @apiSuccess {Integer} total   The total number of elements.
     * @apiSuccess {Array}   results The list of purchases.
     *
     * @Security("hasPermission('PURCHASE_LIST')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        $repository = $this->get('orm.manager')->getRepository('Purchase');
        $converter  = $this->get('orm.manager')->getConverter('Purchase');

        $total     = $repository->countBy($oql);
        $purchases = $repository->findBy($oql);

        $purchases = array_map(function ($a) use ($converter, &$ids) {
            if (!empty($a->instance_id)) {
                $ids[] = $a->instance_id;
            }

            return $converter->responsify($a->getData());
        }, $purchases);

        $extra = $this->getExtraData();

        // Find instances by ids
        if (!empty($ids)) {
            $oql = sprintf('id in [%s]', implode(',', array_unique($ids)));

            $items = $this->get('orm.manager')
                ->getRepository('Instance')
                ->findBy($oql);

            $extra['instances'] = [];
            foreach ($items as $item) {
                $extra['instances'][$item->id] = $item->internal_name;
            }
        }

        return new JsonResponse([
            'extra'   => $extra,
            'results' => $purchases,
            'total'   => $total,
        ]);
    }

    /**
     * @api {get} /purchases/:id Show a purchase
     * @apiName GetPurchase
     * @apiGroup Purchase
     *
     * @apiSuccess {Array} The purchases.
     *
     * @Security("hasPermission('PURCHASE_UPDATE')")
     */
    public function showAction($id)
    {
        $em        = $this->get('orm.manager');
        $converter = $em->getConverter('Purchase');
        $purchase  = $em->getRepository('Purchase', 'manager')->find($id);

        // Remove payment line from purchase
        array_pop($purchase->details);

        $converter = $em->getConverter('Instance');
        $instance  = $em->getRepository('Instance')->find($purchase->instance_id);

        return new JsonResponse([
            'purchase' => $converter->responsify($purchase->getData()),
            'instance' => $converter->responsify($instance),
            'extra'    => $this->getExtraData()
        ]);
    }

    /**
     * Returns an array with extra parameters for template.
     *
     * @return array Array of extra parameters for template.
     */
    protected function getExtraData()
    {
        $countries = Intl::getRegionBundle()
            ->getCountryNames($this->get('core.locale')->getLocaleShort());

        asort($countries);

        return [
            'braintree'  => [
                'url'         => $this->getparameter('braintree.url'),
                'merchant_id' => $this->getparameter('braintree.merchant_id')
            ],
            'countries'  => $countries,
            'freshbooks' => [
                'url' => $this->getparameter('freshbooks.url')
            ],
            'steps'      => [
                [ 'id' => null, 'name' => _('All') ],
                [ 'id' => 'cart', 'name' => _('Cart') ],
                [ 'id' => 'billing', 'name' => _('Billing') ],
                [ 'id' => 'payment', 'name' => _('Payment') ],
                [ 'id' => 'summary', 'name' => _('Summary') ],
                ['id' => 'done', 'name' => _('Done') ]
            ]
        ];
    }

    /**
     * Returns a response with CSV from data.
     *
     * @param array  $data The data to export.
     * @param string $name The name to use in the exported file.
     *
     * @return Response The response object.
     */
    protected function export($data, $name)
    {
        $data = array_map(function ($a) {
            $name  = '';
            $email = '';
            if (array_key_exists('client', $a) && !empty($a['client'])) {
                $client = unserialize($a['client']);

                $name  = $client['last_name'] . ', ' . $client['first_name'];
                $email = $client['email'];
            }

            if (array_key_exists('items', $a) && !empty($a['items'])) {
                $items = unserialize($a['items']);
                $items = array_map(function ($a) {
                    return $a['description'];
                }, $items);

                if ($a['method'] === 'CreditCard') {
                    array_pop($items);
                }

                $a['items'] = implode(', ', $items);
            }

            return [
                'id'             => $a['id'],
                'client'         => $name,
                'instance'       => $a['instance'],
                'client_email'   => $email,
                'instance_email' => $a['instance_email'],
                'items'          => $a['items'],
                'created'        => $a['created'],
                'updated'        => $a['updated'],
                'step'           => $a['step']
            ];
        }, $data);

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setEncodingFrom('utf-8');
        $writer->insertOne([
            'id', 'client', 'instance', 'client_email', 'instance_email',
            'items', 'created', 'updated', 'step'
        ]);

        $writer->insertAll($data);

        $response = new Response();
        $response->setContent($writer);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename=report-' . $name . '-purchases.csv');
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
