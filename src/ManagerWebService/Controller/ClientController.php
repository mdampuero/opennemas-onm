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
     * Returns an array with extra parameters for template.
     *
     * @return array Array of extra parameters for template.
     */
    protected function getTemplateParams()
    {
        return [
            'countries' => Intl::getRegionBundle()->getCountryNames()
        ];
    }
}
