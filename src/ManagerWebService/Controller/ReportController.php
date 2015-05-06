<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use League\Csv\Writer;
use Onm\Framework\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    /**
     * Returns the list of users as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $results = [
            [
                'id'          => 'not-used',
                'title'       => _('Instances not used in the last month'),
                'description' =>  _('Report with the list of instances not used in the last month')
            ],
            [
                'id'          => 'last-created',
                'title'       => _('Instances created in the last month'),
                'description' =>  _('Report with the list of new instances created in last month')
            ]
        ];

        return new JsonResponse([ 'results'  => $results ]);
    }

    /**
     * Returns a CSV file with the information for the selected report.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function exportAction(Request $request)
    {
        $id = $request->query->get('id');
        $im = $this->get('instance_manager');

        switch ($id) {
            case 'not-used':
                $instances = $im->findNotUsedInstances();
                break;

            case 'last-created':
                $instances = $im->findLastCreatedInstances();
                break;

            default:
                return new JsonResponse([], 404);
                break;
        }

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setEncodingFrom('utf-8');
        $headers = ['id', 'name', 'contact', 'domains', 'created', 'last_activity'];
        $writer->insertOne($headers);

        $data = [];
        foreach ($instances as $instance) {
            $data[] = [
                $instance->id,
                $instance->name,
                $instance->contact_mail,
                implode(',', $instance->domains),
                $instance->created,
                $instance->last_login
            ];
        }

        $writer->insertAll($data);

        $fileName = 'opennemas-instances-' . $id.'-'.date("Y_m_d_His").'.csv';
        $response = new Response();
        $response->setContent($writer);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Description', 'Submissions Export');
        $response->headers->set('Content-Disposition', 'attachment; filename='.$fileName);
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
