<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Framework\Import\Synchronizer\Synchronizer;
use Framework\Import\Repository\LocalRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the news agency module
 *
 * @package Backend_Controllers
 */
class NewsAgencyController extends Controller
{
    /**
     * Common code for all the actions
     */
    public function init()
    {
        $this->syncFrom = [
            '3600'      => sprintf(_('%d hour'), 1),
            '10800'     => sprintf(_('%d hours'), 3),
            '21600'     => sprintf(_('%d hours'), 6),
            '43200'     => sprintf(_('%d hours'), 12),
            '86400'     => _('1 day'),
            '172800'    => sprintf(_('%d days'), 2),
            '259200'    => sprintf(_('%d days'), 3),
            '345600'    => sprintf(_('%d days'), 4),
            '432000'    => sprintf(_('%d days'), 5),
            '518400'    => sprintf(_('%d days'), 6),
            '604800'    => sprintf(_('%d week'), 1),
            '1209600'   => sprintf(_('%d weeks'), 2),
            'no_limits' => _('No limit'),
        ];

        // Check if module is configured, if not redirect to configuration form
        $servers = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('news_agency_config');

        if (is_null($servers)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                _('Please provide your source server configuration to start to use your Importer module')
            );
        }
    }

    /**
     * Shows the list of downloaded newsfiles from Efe service
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('news_agency/list.tpl');
    }

    /**
     * Performs the files synchronization with the external server.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_ADMIN')")
     */
    public function syncAction()
    {
        $servers = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('news_agency_config');

        $tpl    = $this->get('view')->getBackendTemplate();
        $path   = $this->getParameter('core.paths.cache') . DS
            . $this->get('core.instance')->internal_name;
        $logger = $this->get('error.log');

        $synchronizer = new Synchronizer($path, $tpl, $logger);

        try {
            $synchronizer->syncMultiple($servers);
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('backend_news_agency'));
    }
}
