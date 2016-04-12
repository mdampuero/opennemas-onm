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

use Backend\Annotation\CheckModuleAccess;
use Framework\Import\Synchronizer\Synchronizer;
use Framework\Import\Repository\LocalRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;

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
     * @CheckModuleAccess(module="NEWS_AGENCY_IMPORTER")
     * @Security("has_role('IMPORT_ADMIN')")
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

        $em         = $this->get('entity_repository');
        $repository = new LocalRepository();

        $imported = [];
        foreach ($ids as $value) {
            $resource = $repository->find($value['source'], $value['id']);

            $criteria = [
                'urn_source' => [
                    [ 'value' => $resource->urn, 'operator' => '=' ]
                ]
            ];

            $content = $em->findOneBy($criteria, []);

            if (empty($content)) {
                $imported[] = $this->import(
                    $value['id'],
                    $value['source'],
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

            if ($type === 'opinion') {
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
     * @CheckModuleAccess(module="NEWS_AGENCY_IMPORTER")
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
        foreach ($elements as $element) {
            $urns[] = $element->urn;

            foreach ($element->related as $id) {
                if (!array_key_exists($id, $related)) {
                    $resource     = $repository->find($element->source, $id);
                    $related[$id] = $resource;
                    $urns[]       = $resource->urn;
                }
            }
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

        $extra = array_merge(
            [ 'imported' => $imported, 'related' => $related ],
            $this->getTemplateParams()
        );

        return new JsonResponse([
            'epp'     => $epp,
            'page'    => $page,
            'results' => $elements,
            'total'   => $total,
            'extra'   => $extra
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

        // Check last synchronization
        $syncParams = array('cache_path' => CACHE_PATH);
        $synchronizer = new Synchronizer($syncParams);
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
            return [ 'name' => $category->title, 'value' => $category->id ];
        }, $categories);

        // Get servers
        $params['servers'] = $this->get('setting_repository')
            ->get('news_agency_config');

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

    /**
     * Returns the author id for a resource.
     *
     * @param array    $server   The server configuration.
     * @param Resource $resource The resource.
     *
     * @return integer The author id for the resource.
     */
    public function getAuthor($server, $resource)
    {
        if (empty($servers)
            || !array_key_exists('author', $servers)
            || $server['author'] !== '1'
            || $resource->agency_name !== 'Opennemas'
        ) {
            return 0;
        }

        $author = $resource->author;

        if (!is_object($author)) {
            return 0;
        }

        $data = get_object_vars($author);

        // Set user as deactivated author without privileges
        $data['activated'] = 0;
        $data['id_user_group'] = ['3'];
        $data['accesscategories'] = [];

        // Create author
        $user = new \User();

        if (array_key_exists('email', $data)) {
            $um = $this->get('user_repository');
            $user = $um->findOneBy();

            if (empty($user)) {
                return $user->id;
            }
        }

        if (!$user->create($data)) {
            return 0;
        }

        // Write in log
        $logger = $this->get('application.log');
        $logger->info(
            'User ' . $data['username'] . ' was created from importer by user '
            . $_SESSION['username'] . ' (' . $_SESSION['userid'] . ')'
        );

        // Set user meta if exists
        if ($author->meta) {
            $meta = get_object_vars($author->meta);
            $user->setMeta($meta);
        }

        if (!$author->photo) {
            return $user->id;
        }

        $cm       = new \ContentManager();
        $photoRaw = $cm->getUrlContent($author->photo);

        if (!$photoRaw) {
            return $author->id;
        }

        // Create author photo
        $localImageDir  = MEDIA_IMG_PATH . $author->photo->path_file;
        $localImagePath = MEDIA_IMG_PATH . $author->photo->path_img;

        if (!is_dir($localImageDir)) {
            \Onm\FilesManager::createDirectory($localImageDir);
        }

        if (file_exists($localImagePath)) {
            unlink($localImagePath);
        }

        file_put_contents($localImagePath, $photoRaw);

        // Get all necessary data for the photo
        $info = new \MediaItem($localImagePath);
        $data = array(
            'title'       => $author->photo->name,
            'name'        => $author->photo->name,
            'user_name'   => $author->photo->name,
            'path_file'   => $author->photo->path_file,
            'namecat'     => $author->username,
            'category'    => '',
            'created'     => $info->atime,
            'changed'     => $info->mtime,
            'date'        => $info->mtime,
            'size'        => round($info->size/1024, 2),
            'width'       => $info->width,
            'height'      => $info->height,
            'type'        => $info->type,
            'type_img'    => substr($author->photo->name, -3),
            'media_type'  => 'image',
            'author_name' => $author->username,
        );

        $photo   = new \Photo();
        $photoId = $photo->create($data);

        $data['avatar_img_id'] = $photoId;
        unset($data['password']);

        $user->update($data);

        return $user->id;
    }

    /**
     * Get the most similar opennemas category basing on the external category.
     *
     * @param string $category The resource category name.
     *
     * @return integer The category id
     *
     * @CheckModuleAccess(module="NEWS_AGENCY_IMPORTER")
     * @Security("has_role('IMPORT_ADMIN')")
     */
    private function getSimilarCategoryIdForElement($originalCategory)
    {
        $ccm = \ContentCategoryManager::get_instance();
        $categories = $ccm->findAll();

        $prevPoint = 1000;
        $finalCategory = 0;
        foreach ($categories as $category) {
            $categoryName = strtolower(utf8_decode($category->title));
            $lev          = levenshtein($originalCategory, $categoryName);

            if ($lev < 2  && $lev < $prevPoint) {
                $prevPoint     = $lev;
                $finalCategory = $category->id;
            }
        }

        return $finalCategory;
    }

    /**
     * Basic logic to import an element
     *
     * @param string $id        The resource id.
     * @param string $source    The resource source.
     * @param string $category  The category to import to.
     * @param string $type      The type to import to.
     * @param string $author    The author id.
     * @param string $activated The activated flag value.
     */
    private function import($id, $source, $category = null, $type = null, $author = null, $activated = 0)
    {
        $repository = new LocalRepository();
        $resource   = $repository->find($source, $id);

        if ((is_null($category) || empty($category)) && !empty($resource->category)) {
            $category = $this->getSimilarCategoryIdForElement($resource->category);
        }

        if (empty($category)) {
            $category = 20;
        }

        $sm = $this->get('setting_repository');

        // Check comments
        $comments = 1;
        $config   = $sm->get('comments_config');

        if (!empty($config) && array_key_exists('with_comments', $config)) {
            $comments = $config['with_comments'];
        }

         // Get server
        $servers = $sm->get('news_agency_config');
        $server = $servers[$source];

        $data = [
            'category'       => $category,
            'content_status' => $activated,
            'frontpage'      => 0,
            'in_home'        => 0,
            'metadata'       => \Onm\StringUtils::getTags($resource->title),
            'title'          => $resource->title,
            'title_int'      => $resource->title,
            'with_comment'   => $comments,
            'subtitle'       => $resource->pretitle,
            'agency'         => $server['agency_string'],
            'fk_author'      => (isset($authorId) ? $authorId : 0),
            'summary'        => $resource->summary,
            'body'           => $resource->body,
            'fk_publisher'   => $_SESSION['userid'],
            'img1'           => 0,
            'img1_footer'    => '',
            'img2'           => 0,
            'img2_footer'    => '',
            'fk_video'       => 0,
            'footer_video'   => '',
            'fk_video2'       => 0,
            'footer_video2'   => '',
            'urn_source'     => $resource->urn,
        ];

        if ($resource->agency_name != 'EuropaPress') {
            $data['agency'] = $resource->agency_name;
        }

        // Check photos and videos for articles and opinions
        if ($resource->type === 'text') {
            $em = $this->get('entity_repository');

            foreach ($resource->related as $id) {
                $related = $repository->find($source, $id);

                if (!empty($related)) {
                    $criteria = [
                        'urn_source' => [
                            [ 'value' => $related->urn, 'operator' => '=' ]
                        ]
                    ];

                    $content = $em->findOneBy($criteria, []);

                    if (!empty($content)
                        && $content->content_type_name === 'photo'
                    ) {
                        if (!array_key_exists('img1', $data)
                            || empty($data['img1'])
                        ) {
                            $data['img1']        = $content->pk_content;
                            $data['img1_footer'] = $content->description;
                        }

                        // Add as inner image if no image or if it is equals to img1
                        if (!array_key_exists('img2', $data)
                            || empty($data['img2'])
                            || $data['img1'] == $data['img2']
                        ) {
                            $data['img2']        = $content->pk_content;
                            $data['img2_footer'] = $content->description;
                        }
                    }

                    if (!empty($content)
                        && $type === 'article'
                        && $content->content_type_name === 'video'
                    ) {
                        if (!array_key_exists('fk_video', $data)) {
                            $data['fk_video']        = $content->pk_content;
                            $data['footer_video'] = $content->description;
                        }

                        // Add as inner image if no image or if it is equals to video1
                        if (!array_key_exists('fk_video2', $data)
                            || $data['fk_video'] == $data['fk_video2']
                        ) {
                            $data['fk_video2']        = $content->pk_content;
                            $data['footer_video2'] = $content->description;
                        }
                    }
                }
            }
        }

        if ($resource->type === 'photo') {
            $filePath = realpath(
                $repository->syncPath . DS . $source . DS . $resource->file_name
            );

            $data = [
                'title'             => $resource->title,
                'description'       => $resource->summary,
                'local_file'        => $filePath,
                'fk_category'       => 0,
                'category_name'     => '',
                'metadata'          => \Onm\StringUtils::getTags($resource->title),
                'original_filename' => $resource->file_name,
                'urn_source'        => $resource->urn,
            ];
        }

        $data['fk_author'] = $this->getAuthor($server, $resource);

        $target = 'Article';

        if ($resource->type === 'text') {
            if ($type === 'opinion') {
                $data['fk_author']    = $author;
                $data['type_opinion'] = 0;

                if ($author == 1 || $author == 2) {
                    $data['type_opinion'] = $author;
                }

                $target = 'Opinion';
            }
        }

        if ($resource->type === 'photo') {
            $photo = new \Photo();
            $id = $photo->createFromLocalFile($data);

            return $id;
        }

        $content = new $target();
        $content->create($data);

        return $content->id;
    }
}
