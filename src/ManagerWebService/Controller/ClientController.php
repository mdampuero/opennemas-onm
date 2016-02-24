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

use Framework\ORM\Entity\Client;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Intl;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Handles the Client resource.
 */
class ClientController extends Controller
{
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
     */
    public function listAction(Request $request)
    {
        $q        = $request->query->filter('q');
        $epp      = $request->query->getDigits('epp', 10);
        $page     = $request->query->getDigits('page', 1);
        $criteria = $request->query->filter('criteria') ? : [];
        $orderBy  = $request->query->filter('orderBy') ? : [];
        $extra    = $this->getTemplateParams();

        $order = [];
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        $repository = $this->get('orm.manager')
            ->getRepository('manager.client', 'Database');

        $ids       = [];
        $clients = $repository->findBy($criteria, $order, $epp, $page);
        $total     = $repository->countBy($criteria);

        // Clean clients
        foreach ($clients as &$client) {
            $ids[]    = $client->instance_id;
            $client = $client->getData();
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
     */
    public function newAction()
    {
        return new JsonResponse([
            'extra'  => $this->getTemplateParams()
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
     */
    public function saveAction(Request $request)
    {
        $client = new Client($request->request->all());

        $this->get('orm.manager')->persist($client, 'FreshBooks');
        $this->get('orm.manager')->persist($client, 'Braintree');
        $this->get('orm.manager')->persist($client, 'Database');

        return new JsonResponse(_('Client saved successfully'));
    }

    /**
     * @api {get} /clients/:id Get a client
     * @apiName GetClient
     * @apiGroup Client
     *
     * @apiParam {Integer} id The client's id.
     *
     * @apiSuccess {Array} client The client's data.
     */
    public function showAction($id)
    {
        $client = $this->get('orm.manager')
            ->getRepository('client', 'Database')
            ->find($id);

        return new JsonResponse([
            'client' => $client->getData(),
            'extra'  => $this->getTemplateParams()
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
     */
    public function updateAction($id, Request $request)
    {
        $client = $this->get('orm.manager')
            ->getRepository('client', 'Database')
            ->find($id);

        foreach ($request->request as $key => $value) {
            $client->{$key} = $value;
        }

        $this->get('orm.manager')->persist($client, 'FreshBooks');
        $this->get('orm.manager')->persist($client, 'Braintree');
        $this->get('orm.manager')->persist($client, 'Database');

        return new JsonResponse(_('Client saved successfully'));
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

        sort($countries);

        return [ 'countries' => $countries ];
    }
}
