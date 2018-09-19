<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Framework\Import\Compiler\Compiler;
use Framework\Import\Repository\LocalRepository;
use Framework\Import\ServerFactory;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class NewsAgencyServerController extends Controller
{
    /**
     * Tries to connect to the server basing on the parameters.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function checkAction(Request $request)
    {
        $server = [
            'password'  => $request->query->get('password'),
            'url'       => $request->query->get('url'),
            'username'  => $request->query->get('username'),
            'sync_from' => ''
        ];

        $sf = new ServerFactory($this->get('view')->getBackendTemplate());

        try {
            $sf->get($server);
        } catch (\Exception $e) {
            return new JsonResponse([
                'message' => _('Unable to connect to the server'),
                'type'    => 'error'
            ]);
        }

        return new JsonResponse([
            'message' => _('Server connection success!'),
            'type'    => 'success'
        ]);
    }

    /**
     * Clean files for a server.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_NEWS_AGENCY_CONFIG')")
     */
    public function cleanAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $servers = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('news_agency_config');

        if (!array_key_exists($id, $servers)) {
            return new JsonResponse([
                'messages' => [
                    'message' => sprintf(
                        _('Source identifier "%d" not valid'),
                        $id
                    ),
                    'type' => 'error'
                ]
            ], 400);
        }

        $messages = [];
        $status   = 200;
        try {
            $repository = new LocalRepository();
            $compiler   = new Compiler($repository->syncPath);

            $compiler->cleanCompileForServer($id);
            $compiler->cleanSourceFilesForServer($id);

            $messages[] = [
                'message' => _('Files for server deleted successfully.'),
                'type'    => 'success'
            ];
        } catch (\Exception $e) {
            $status = 200;

            $messages[] = [
                'message' => $e->getMessage(),
                'type'    => 'error'
            ];
        }

        return new JsonResponse([ 'messages' => $messages ], $status);
    }

    /**
     * Returns a list of servers.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')")
     */
    public function listAction()
    {
        $servers = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('news_agency_config');

        return new JsonResponse([
            'extra'   => [ 'sync_from' => $this->syncFrom ],
            'total'   => count($servers),
            'results' => array_values($servers),
        ]);
    }

    /**
     * Toogle an server state to enabled/disabled
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_NEWS_AGENCY_CONFIG')")
     */
    public function toggleAction(Request $request, $id)
    {
        $status  = $request->request->get('activated');
        $ds      = $this->get('orm.manager')->getDataSet('Settings');
        $servers = $ds->get('news_agency_config');

        $servers[$id]['activated'] = $status;

        $repository = new LocalRepository();
        $compiler   = new Compiler($repository->syncPath);
        $compiler->cleanCompileForServer($id);

        $ds->set('news_agency_config', $servers);

        return new JsonResponse([
            'activated' => $status,
            'messages'       => [
                [
                    'message' => _('Server updated successfully'),
                    'type'    => 'success'
                ]
            ]
        ]);
    }

    /**
     * Deletes a server.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_NEWS_AGENCY_CONFIG')")
     */
    public function deleteAction(Request $request)
    {
        $id      = $request->query->getDigits('id');
        $ds      = $this->get('orm.manager')->getDataSet('Settings');
        $servers = $ds->get('news_agency_config');

        if (!array_key_exists($id, $servers)) {
            return new JsonResponse([
                'messages' => [
                    'message' => sprintf(
                        _('Source identifier "%d" not valid'),
                        $id
                    ),
                    'type' => 'error'
                ]
            ]);
        }

        $messages = [];
        try {
            $repository = new LocalRepository();
            $compiler   = new Compiler($repository->syncPath);

            $compiler->cleanCompileForServer($id);
            $compiler->cleanSourceFilesForServer($id);

            unset($servers[$id]);

            $ds->set('news_agency_config', $servers);

            $messages[] = [
                'message' => _('News agency server deleted.'),
                'type'    => 'success'
            ];
        } catch (\Exception $e) {
            $messages[] = [
                'message' => $e->getMessage(),
                'type'    => 'error'
            ];
        }

        return new JsonResponse([ 'messages' => $messages ]);
    }

    /**
     * Returns the list of hours.
     *
     * @return array The list of hours.
     */
    protected function getSyncFrom()
    {
        return [
            '3600'         => sprintf(_('%d hour'), '1'),
            '10800'         => sprintf(_('%d hours'), '3'),
            '21600'         => sprintf(_('%d hours'), '6'),
            '43200'         => sprintf(_('%d hours'), '12'),
            '86400'         => _('1 day'),
            '172800'        => sprintf(_('%d days'), '2'),
            '259200'        => sprintf(_('%d days'), '3'),
            '345600'        => sprintf(_('%d days'), '4'),
            '432000'        => sprintf(_('%d days'), '5'),
            '518400'        => sprintf(_('%d days'), '6'),
            '604800'        => sprintf(_('%d week'), '1'),
            '1209600'       => sprintf(_('%d weeks'), '2'),
            'no_limits'     => _('No limit'),
        ];
    }
}
