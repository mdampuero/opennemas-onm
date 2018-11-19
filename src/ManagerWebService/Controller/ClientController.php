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

use Common\Core\Annotation\Security;
use Common\ORM\Entity\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Handles the Client resource.
 */
class ClientController extends Controller
{
    /**
     * @api {delete} /clients/:id Delete a client
     * @apiName DeleteClient
     * @apiGroup Client
     *
     * @apiParam {Integer} id The client's id.
     *
     * @apiSuccess {String} message The success message.
     *
     * @Security("hasPermission('CLIENT_UPDATE')")
     */
    public function deleteAction($id)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');

        $client = $em->getRepository('client')->find($id);

        try {
            $em->remove($client, 'freshbooks');
        } catch (\Exception $e) {
            $this->get('error.log')->error($e->getMessage());
            $msg->add($e->getMessage(), 'error');
        }

        try {
            $em->remove($client, 'braintree');
        } catch (\Exception $e) {
            $this->get('error.log')->error($e->getMessage());
            $msg->add($e->getMessage(), 'error');
        }

        $em->remove($client, 'manager');

        $msg->add(_('Client deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * @api {delete} /clients/ Delete selected clients
     * @apiName DeleteClients
     * @apiGroup Client
     *
     * @apiParam {Integer} selected The clients ids.
     *
     * @apiSuccess {String} message The success message.
     *
     * @Security("hasPermission('CLIENT_UPDATE')")
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

        $clients = $em->getRepository('Client')->findBy($oql);

        $deleted = 0;
        foreach ($clients as $client) {
            try {
                $em->remove($client, 'freshbooks');
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }

            try {
                $em->remove($client, 'braintree');
            } catch (Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }

            try {
                $em->remove($client, 'manager');
                $deleted++;
            } catch (\Exception $e) {
                $msg->add($e->getMessage(), 'error');
            }
        }

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s clients deleted successfully'), $deleted),
                'success'
            );
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * @api {get} /clients List of clients
     * @apiName GetClients
     * @apiGroup Client
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
     * @apiSuccess {Array}   results The list of clients.
     *
     * @Security("hasPermission('CLIENT_LIST')")
     */
    public function listAction(Request $request)
    {
        $oql   = $request->query->get('oql', '');

        // Fix OQL for Non-MASTER users
        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $condition = sprintf('owner_id = %s ', $this->get('core.user')->id);

            $oql = $this->get('orm.oql.fixer')->fix($oql)
                ->addCondition($condition)->getOql();
        }

        $repository = $this->get('orm.manager')->getRepository('Client');
        $converter  = $this->get('orm.manager')->getConverter('Client');

        $ids     = [];
        $total   = $repository->countBy($oql);
        $clients = $repository->findBy($oql);

        $clients = array_map(function ($a) use ($converter, &$ids) {
            $ids[] = $a->id;

            return $converter->responsify($a);
        }, $clients);

        $extra = $this->getExtraData($ids);

        return new JsonResponse([
            'extra'   => $extra,
            'results' => $clients,
            'total'   => $total,
        ]);
    }

    /**
     * @api {get} /clients/new Get data to create a client.
     * @apiName NewClient
     * @apiGroup Client
     *
     * @apiSuccess {Array} client The client's data.
     *
     * @Security("hasPermission('CLIENT_CREATE')")
     */
    public function newAction()
    {
        return new JsonResponse([
            'extra'  => $this->getExtraData()
        ]);
    }

    /**
     * @api {post} /clients Creates a client
     * @apiName GetClient
     * @apiGroup Client
     *
     * @apiParam {String} first_name  The client's first name.
     * @apiParam {String} last_name   The client's last name.
     * @apiParam {String} company     The client's company.
     * @apiParam {String} vat_number  The client's VAT number.
     * @apiParam {String} email       The client's email.
     * @apiParam {String} phone       The client's phone.
     * @apiParam {String} address     The client's address.
     * @apiParam {String} postal code The client's postal code.
     * @apiParam {String} city        The client's city.
     * @apiParam {String} state       The client's state.
     * @apiParam {String} country     The client's country.
     *
     * @apiSuccess {String} message The success message.
     *
     * @Security("hasPermission('CLIENT_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Client')
            ->objectify($request->request->all());

        // Add current user as owner if current user is a PARTNER
        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $data['owner_id'] = $this->get('core.user')->id;
        }

        $client = new Client($data);

        $em->persist($client, 'freshbooks');
        $em->persist($client, 'braintree');
        $em->persist($client, 'manager');

        $msg->add(_('Client saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());

        // Add permanent URL for the current notification
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'manager_ws_client_show',
                [ 'id' => $client->id ]
            )
        );

        return $response;
    }

    /**
     * @api {get} /clients/:id Get a client
     * @apiName GetClient
     * @apiGroup Client
     *
     * @apiParam {Integer} id The client's id.
     *
     * @apiSuccess {Array} client The client's data.
     *
     * @Security("hasPermission('CLIENT_UPDATE')")
     */
    public function showAction($id)
    {
        $client = $this->get('orm.manager')
            ->getRepository('Client')
            ->find($id);

        return new JsonResponse([
            'client' => $client->getData(),
            'extra'  => $this->getExtraData()
        ]);
    }

    /**
     * @api {put} /clients/:id Get a client
     * @apiName GetClient
     * @apiGroup Client
     *
     * @apiParam {Integer} id The client's id.
     * @apiParam {String}  first_name  The client's first name.
     * @apiParam {String}  last_name   The client's last name.
     * @apiParam {String}  company     The client's company.
     * @apiParam {String}  vat_number  The client's VAT number.
     * @apiParam {String}  email       The client's email.
     * @apiParam {String}  phone       The client's phone.
     * @apiParam {String}  address     The client's address.
     * @apiParam {String}  postal code The client's postal code.
     * @apiParam {String}  city        The client's city.
     * @apiParam {String}  state       The client's state.
     * @apiParam {String}  country     The client's country.
     *
     * @apiSuccess {String} message The success message.
     *
     * @Security("hasPermission('CLIENT_UPDATE')")
     */
    public function updateAction($id, Request $request)
    {
        $em   = $this->get('orm.manager');
        $msg  = $this->get('core.messenger');
        $data = $em->getConverter('Client')
            ->objectify($request->request->all());

        // Add current user as owner if current user is a PARTNER
        if (!$this->get('core.security')->hasPermission('MASTER')) {
            $data['owner_id'] = $this->get('core.user')->id;
        }

        $client = $em->getRepository('client')->find($id);
        $client->setData($data);

        $em->persist($client, 'freshbooks');
        $em->persist($client, 'braintree');
        $em->persist($client, 'manager');

        $msg->add(_('Client saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns an array with extra parameters for template.
     *
     * @param array $ids The list of client ids.
     *
     * @return array Array of extra parameters for template.
     */
    protected function getExtraData($ids = [])
    {
        $extra = [
            'braintree'  => [
                'url'         => $this->getparameter('braintree.url'),
                'merchant_id' => $this->getparameter('braintree.merchant_id')
            ],
            'freshbooks' => [
                'url' => $this->getparameter('freshbooks.url')
            ],
        ];

        $extra['countries'] = $this->get('core.geo')->getCountries();
        $extra['provinces'] = $this->get('core.geo')->getRegions('ES');

        $users = $this->get('orm.manager')->getRepository('User', 'manager')
            ->findBy();

        $extra['users'] = [
            [ 'id' => null, 'name' => _('Select an user...') ]
        ];

        foreach ($users as $user) {
            $extra['users'][] = [ 'id' => $user->id, 'name' => $user->name ];
        }

        if (empty($ids)) {
            return $extra;
        }

        $instances = $this->get('orm.manager')->getRepository('Instance')
            ->findBy(sprintf('client in ["%s"]', implode('", "', $ids)));

        $extra['instances'] = [];
        foreach ($instances as $instance) {
            $extra['instances'][$instance->getClient()][] =
                [ 'id' => $instance->id, 'name' => $instance->internal_name ];
        }

        return $extra;
    }
}
