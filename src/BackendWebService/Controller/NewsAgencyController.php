<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace BackendWebService\Controller;

use Common\Core\Annotation\Security;
use Framework\Import\Repository\LocalRepository;
use Framework\Import\Synchronizer\Synchronizer;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Controller for News Agency listing
 */
class NewsAgencyController extends Controller
{
    /**
     * Imports the article information given a newfile filename
     *
     * @param Request $request the request object
     *
     * @return Response The response object
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_ADMIN')")
     */
    public function importAction(Request $request)
    {
        $author    = $request->request->get('author', null, FILTER_SANITIZE_STRING);
        $category  = $request->request->get('category', null, FILTER_SANITIZE_STRING);
        $ids       = $request->request->get('ids');
        $type      = $request->request->get('type', null, FILTER_SANITIZE_STRING);
        $edit      = $request->request->get('edit');
        $activated = 1;

        if ($edit) {
            $activated = 0;
        }

        $importer = $this->get('news_agency.importer');
        $servers  = $this->get('setting_repository')->get('news_agency_config');

        $em         = $this->get('entity_repository');
        $repository = new LocalRepository();

        $imported = [];
        foreach ($ids as $value) {
            $resource = $repository->find($value['source'], $value['id']);
            $importer->configure($servers[$value['source']]);

            $criteria = [
                'urn_source' => [
                    [ 'value' => $resource->urn, 'operator' => '=' ]
                ]
            ];

            $content = $em->findOneBy($criteria, []);

            if (empty($content)) {
                $imported[] = $importer->import(
                    $resource,
                    $category,
                    $type,
                    $author,
                    $activated
                );
            }
        }

        $response = new JsonResponse([
            'messages' => [
                [
                    'message' => sprintf(
                        _('%d contents imported successfully'),
                        count($imported)
                    ),
                    'type' => 'success'
                ]
            ]
        ], 201);

        if ($edit) {
            $route = 'admin_article_show';

            if ($type === 'Opinion') {
                $route = 'admin_opinion_show';
            }

            $response->headers->add(
                [
                    'location' => $this->generateUrl(
                        $route,
                        [ 'id' => $imported[count($imported) - 1] ]
                    )
                ]
            );
        }

        return $response;
    }

    /**
     * Returns a list of contents ready to import.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')")
     */
    public function listAction(Request $request)
    {
        $page   = $request->request->getDigits('page', 1);
        $search = $request->request->get('search');
        $epp    = $request->request->getDigits('elements_per_page', 10);

        $source = $title = '.*';
        $type   = 'text';

        if (is_array($search)) {
            if (array_key_exists('source', $search)) {
                $source = $search['source'][0]['value'];
            }

            if (array_key_exists('title', $search)) {
                $title = $search['title'][0]['value'];
            }

            if (array_key_exists('type', $search)) {
                $type = $search['type'][0]['value'];
            }
        }

        $criteria = [ 'source' => $source, 'title'  => $title, 'type' => $type ];

        $repository = new LocalRepository();

        $total    = $repository->countBy($criteria);
        $elements = $repository->findBy($criteria, $epp, $page);

        $related = [];
        $urns    = [];
        foreach ($elements as &$element) {
            $urns[] = $element->urn;

            foreach ($element->related as &$id) {
                if (!array_key_exists($id, $related)) {
                    $resource = $repository->find($element->source, $id);

                    if (empty($resource)) {
                        $id = null;
                    }

                    if (!empty($resource)) {
                        $related[$id] = $resource;
                        $urns[]       = $resource->urn;
                    }
                }
            }

            $element->related = array_filter($element->related, function ($a) {
                return !empty($a);
            });
        }

        $imported = [];

        if (!empty($urns)) {
            $em = $this->get('entity_repository');

            $criteria = [
                'urn_source' => [ [ 'value' => $urns, 'operator' => 'IN' ] ]
            ];

            $contents = $em->findBy($criteria, []);

            foreach ($contents as $content) {
                $imported[] = $content->urn_source;
            }
        }

        $timezone  = $this->container->get('core.locale')->getTimeZone();

        $extra = array_merge([
            'imported' => $imported,
            'related' => $related,
            'timezone' => $timezone->getName(),
        ], $this->getTemplateParams());

        return new JsonResponse([
            'epp'     => $epp,
            'page'    => $page,
            'results' => $elements,
            'total'   => $total,
            'extra'   => $extra,
        ]);
    }

    /**
     * Returns the image content given an URL.
     *
     * @param string $source The source id.
     * @param string $id     The resource id.
     *
     * @return Response The response object.
     */
    public function showImageAction($source, $id)
    {
        $repository = new LocalRepository();
        $resource   = $repository->find($source, $id);

        if (empty($resource) || $resource->type !== 'photo') {
            return new Response('Image not found', 404);
        }

        $path = $repository->syncPath . DS . $source . DS . $resource->file_name;

        if (!file_exists($path)) {
            return new Response('Image not found', 404);
        }

        $content = @file_get_contents($path);

        return new Response(
            $content,
            200,
            [ 'content-type' => $resource->image_type ]
        );
    }

    /**
     * Returns a list of parameters for template.
     *
     * @return array The parameters for template.
     */
    private function getTemplateParams()
    {
        $params = [];

        $path = $this->getParameter('core.paths.cache') .  DS
            . $this->get('core.instance')->internal_name;
        $tpl  = $this->get('view')->getBackendTemplate();
        $logger = $this->get('error.log');

        // Check last synchronization
        $synchronizer        = new Synchronizer($path, $tpl, $logger);
        $minutesFromLastSync = $synchronizer->minutesFromLastSync();

        if ($minutesFromLastSync > 0) {
            $params['last_sync'] = sprintf(
                _('Last sync was %d minutes ago.'),
                $minutesFromLastSync
            );
        }

        // Get categories
        $this->ccm  = \ContentCategoryManager::get_instance();

        $categories = array_filter($this->ccm->findAll(), function ($category) {
            return $category->internal_category == '1';
        });

        $params['categories'] = array_map(function ($category) {
            return [
                'name' => $this->get('data.manager.filter')
                    ->set($category->title)->filter('localize')->get(),
                'value' => $category->id
            ];
        }, $categories);

        // Get servers
        $params['servers'] = $this->get('setting_repository')->get('news_agency_config');

        if (!is_array($params['servers'])) {
            $params['servers'] = array();
        }

        // Build sources select options
        $params['sources'] = [ [ 'name' => _('All'), 'value' => '' ] ];

        foreach ($params['servers'] as $server) {
            if ($server['activated']) {
                $params['sources'][] = [
                    'name' => $server['name'],
                    'value' => $server['id']
                ];
            }
        }

        $params['type'] = [
            [ 'name' => _('Text'), 'value' => 'text' ],
            [ 'name' => _('Photo'), 'value' => 'photo' ]
        ];

        $authors = \User::getAllUsersAuthors();
        $params['authors'] = [];

        foreach ($authors as $author) {
            $params['authors'][] = [
                'name' => $author->name,
                'value' => $author->id
            ];
        }

        return $params;
    }
}
