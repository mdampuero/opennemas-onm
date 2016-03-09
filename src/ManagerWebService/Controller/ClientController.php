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
     * @api {delete} /clients/:id Delete a client
     * @apiName DeleteClient
     * @apiGroup Client
     *
     * @apiParam {Integer} id The client's id.
     *
     * @apiSuccess {String} message The success message.
     */
    public function deleteAction($id)
    {
        $client = $this->get('orm.manager')
            ->getRepository('client', 'Database')
            ->find($id);

        $this->get('orm.manager')->remove($client, 'FreshBooks');
        $this->get('orm.manager')->remove($client, 'Braintree');
        $this->get('orm.manager')->remove($client, 'Database');

        return new JsonResponse(_('Client removed successfully'));
    }

    /**
     * @api {delete} /clients/:id Delete a client
     * @apiName DeleteClient
     * @apiGroup Client
     *
     * @apiParam {Integer} id The client's id.
     *
     * @apiSuccess {String} message The success message.
     */
    public function deleteSelectedAction(Request $request)
    {
        $error      = [];
        $messages   = [];
        $selected   = $request->request->get('selected', null);
        $statusCode = 200;
        $updated    = [];

         if (!is_array($selected)
            || (is_array($selected) && count($selected) == 0)
        ) {
            return new JsonResponse(
                _('Unable to find the instances for the given criteria'),
                404
            );
        }

        $em       = $this->get('orm.manager');
        $criteria = [ 'id' => [ [ 'value' => $selected, 'operator' => 'IN'] ] ];
        $clients  = $em->getRepository('client', 'Database')->findBy($criteria);

        foreach ($clients as $client) {
            try {
                $em->remove($client, 'FreshBooks');
                $em->remove($client, 'Braintree');
                $em->remove($client, 'Database');
                $updated++;
            } catch (EntityNotFoundException $e) {
                $error[]    = $client->id;
                $messages[] = [
                    'message' => sprintf(_('Unable to find the client with id "%s"'), $client->id),
                    'type'    => 'error'
                ];
            } catch (\Exception $e) {
                $error[]    = $client->id;
                $messages[] = [
                    'message' => _($e->getMessage()),
                    'type'    => 'error'
                ];
            }
        }

        if (count($updated) > 0) {
            $messages = [
                'message' => sprintf(_('%s clients deleted successfully.'), count($updated)),
                'type'    => 'success'
            ];
        }

        // Return the proper status code
        if (count($error) > 0 && count($updated) > 0) {
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

        $countries = Intl::getRegionBundle()
            ->getCountryNames(CURRENT_LANGUAGE_SHORT);

        $order = [];
        foreach ($orderBy as $value) {
            $order[$value['name']] = $value['value'];
        }

        if (array_key_exists('name', $criteria)) {
            $value = $criteria['name'][0]['value'];

            $criteria['first_name'] =
                $criteria['last_name'] =
                $criteria['email'] =
                $criteria['address'] =
                $criteria['city'] =
                $criteria['state'] = [
                    [ 'value' => $value, 'operator' => 'like' ]
                ];

            $key = array_search(str_replace('%', '', $value), $countries);

            if (!empty($key)) {
                $criteria['country'] = [
                    [ 'value' => $key, 'operator' => 'like' ]
                ];
            }

            $criteria['union'] = 'OR';

            unset($criteria['name']);
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

        $response =  new JsonResponse(_('Client saved successfully'), 201);

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
