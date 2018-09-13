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

use Common\Core\Annotation\Security;
use League\Csv\Writer;
use Common\Core\Controller\Controller;
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
     *
     * @Security("hasPermission('REPORT_LIST')")
     */
    public function listAction()
    {
        $results = [
            [
                'id'          => 'not-used',
                'title'       => _('Instances not used in last month'),
                'description' =>  _('Generates a report listing instances that were not used in the last month.')
            ],
            [
                'id'          => 'last-created',
                'title'       => _('Instances created in the last month'),
                'description' => _('Generates a report listing instances created in the last month.')
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
     *
     * @Security("hasPermission('REPORT_DOWNLOAD')")
     */
    public function exportAction(Request $request)
    {
        $id         = $request->query->get('id');
        $repository = $this->get('orm.manager')->getRepository('Instance');

        switch ($id) {
            case 'not-used':
                $instances = $repository->findNotUsedInstances();
                break;

            case 'last-created':
                $instances = $repository->findLastCreatedInstances();
                break;

            default:
                return new JsonResponse([], 404);
                break;
        }

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $writer->setDelimiter(';');
        $writer->setInputEncoding('utf-8');
        $headers = ['id', 'name', 'contact', 'domains', 'created', 'last_activity'];
        $writer->insertOne($headers);

        $data = [];
        foreach ($instances as $instance) {
            $data[] = [
                $instance->id,
                $instance->name,
                $instance->contact_mail,
                implode(',', $instance->domains),
                !empty($instance->created) ? $instance->created->format('Y-m-d H:i:s') : '',
                !empty($instance->last_login) ? $instance->last_login->format('Y-m-d H:i:s') : ''
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
