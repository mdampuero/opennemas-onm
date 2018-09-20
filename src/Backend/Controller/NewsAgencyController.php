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
use Onm\Settings as s;

/**
 * Handles the actions for the news agency module
 *
 * @package Backend_Controllers
 */
class NewsAgencyController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     */
    public function init()
    {
        $this->syncFrom = [
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

        // Check if module is configured, if not redirect to configuration form
        if (is_null(s::get('news_agency_config'))) {
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
     * Shows the category form to pick a category under where to import the new
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_ADMIN')")
     */
    public function selectCategoryWhereToImportAction(Request $request)
    {
        $id       = $request->query->filter('id', null, FILTER_SANITIZE_STRING);
        $category = $request->query->filter('category', null, FILTER_SANITIZE_STRING);
        $sourceId = $request->query->getDigits('source_id');

        if (empty($id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('The article you want to import doesn\'t exists.')
            );

            $this->redirect($this->generateUrl('backend_news_agency'));
        }

        $repository = new \Onm\Import\Repository\LocalRepository();
        $element    = $repository->findByFileName($sourceId, $id);

        $ccm              = \ContentCategoryManager::get_instance();
        $parentCategories = $ccm->getArraysMenu();

        // If the element has a original category that matches an existing category
        // in the newspaper redirect it to the import action with that category
        $targetCategory = $this->getSimilarCategoryIdForElement($element);
        if (!empty($targetCategory)) {
            return $this->redirect($this->generateUrl(
                'backend_news_agency_import',
                [
                    'source_id' => $sourceId,
                    'id'        => $id,
                    'category'  => $targetCategory,
                ]
            ));
        }

        return $this->render(
            'news_agency/import_select_category.tpl',
            [
                'id'           => $id,
                'source_id'    => $sourceId,
                'article'      => $element,
                'subcat'       => $parentCategories[1],
                'allcategorys' => $parentCategories[0],
            ]
        );
    }

    /**
     * Get the most similar category based on category metadata of element
     *
     * @param Object $element the element object
     *
     * @return int Category id
     *
     * @Security("hasExtension('NEWS_AGENCY_IMPORTER')
     *     and hasPermission('IMPORT_ADMIN')")
     */
    public function getSimilarCategoryIdForElement($element)
    {
        $finalCategory = 0;
        if (is_array($element->getMetaData()) &&
            array_key_exists('category', $element->getMetaData())
        ) {
            $originalCategory     = utf8_decode($element->getMetaData()['category']);
            $originalCategoryTemp = strtolower($originalCategory);

            $ccm        = \ContentCategoryManager::get_instance();
            $categories = $ccm->findAll();

            $prevPoint     = 1000;
            $finalCategory = null;
            foreach ($categories as $category) {
                $categoryName = strtolower(utf8_decode($category->title));
                $lev          = levenshtein($originalCategoryTemp, $categoryName);

                if ($lev < 2 && $lev < $prevPoint) {
                    $prevPoint     = $lev;
                    $finalCategory = $category->id;
                }
            }
        }

        return $finalCategory;
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
        ini_set('memory_limit', '128M');
        ini_set('set_time_limit', '0');

        $servers = $this->get('setting_repository')->get('news_agency_config');
        $tpl     = $this->get('view')->getBackendTemplate();
        $path    = $this->getParameter('core.paths.cache') . DS
            . $this->get('core.instance')->internal_name;
        $logger  = $this->get('error.log');

        $synchronizer = new Synchronizer($path, $tpl, $logger);

        try {
            $synchronizer->syncMultiple($servers);
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', $e->getMessage());
        }

        return $this->redirect($this->generateUrl('backend_news_agency'));
    }

    /**
     * Basic logic to import an element
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    private function importElements($id = '', $sourceId = '', $category = null)
    {
        if (empty($id) || empty($sourceId)) {
            $this->get('session')->getFlashBag()->add('error', _('Please specify the article to import.'));

            return 'redirect_list';
        }

        $categoryInstance = new \ContentCategory($category);
        if (!is_object($categoryInstance)) {
            $this->get('session')->getFlashBag()->add('error', _('The category you have chosen doesn\'t exists.'));

            return 'redirect_category';
        }

        // Get EFE new from a filename
        try {
            $repository = new \Onm\Import\Repository\LocalRepository();
            $element    = $repository->findByFileName($sourceId, $id);
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()->add('error', _('Please specify the article to import.'));

            return 'redirect_list';
        }

        if ($category == 'GUESS') {
            // If the element has a original category that matches an existing category
            // in the newspaper redirect it to the import action with that category
            $category = $this->getSimilarCategoryIdForElement($element);
            if (empty($category)) {
                $category = '20';
            }
        } elseif (empty($category)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Please assign the category where import this article')
            );

            return 'redirect_category';
        }

        // Get server config
        $servers = s::get('news_agency_config');
        $server  = $servers[$sourceId];

        $fm = $this->get('data.manager.filter');

        // If the new has photos import them
        if (count($element->getPhotos()) > 0) {
            $i              = 0;
            $importedPhotos = [];

            foreach ($element->getPhotos() as $photo) {
                // Get image from FTP
                $filePath = realpath($repository->syncPath . DS . $sourceId . DS . $photo->getFilePath());
                $fileName = $photo->getFilePath();

                // If no image from FTP check HTTP
                if (!$filePath) {
                    $filePath = $repository->syncPath . DS . $sourceId . DS . $photo->getName();
                    $fileName = $photo->getName();
                }

                // Check if the file cache exists(keys)
                if (file_exists($filePath)) {
                    // If the image is already imported use its id
                    if (!array_key_exists($photo->getId(), $importedPhotos)) {
                        $data = [
                            'title'         => $fileName,
                            'description'   => $photo->getTitle(),
                            'local_file'    => $filePath,
                            'fk_category'   => $category,
                            'category_name' => $categoryInstance->name,
                            'category'      => $categoryInstance->name,
                            'tag_ids'       => $this->get('api.service.tag')
                                ->getTagIdsFromStr($photo->getTitle()),
                            'author_name'   => '&copy; EFE ' . date('Y'),
                            'original_filename' => $fileName,
                        ];

                        $newphoto = new \Photo();
                        $photoId  = $newphoto->createFromLocalFile($data);

                        $importedPhotos[$photo->getId()] = $photoId;
                    } else {
                        $photoId = $importedPhotos[$photo->getId()];
                    }

                    // Check if sync is from Opennemas instances
                    if ($element->getServicePartyName() == 'Opennemas') {
                        // If this article has more than one photo take the first one to front
                        if ($photo->getMediaType() == 'PhotoFront' && !isset($frontPhoto)) {
                            $frontPhoto = new \Photo($photoId);
                        } elseif ($photo->getMediaType() == 'PhotoInner' && !isset($innerPhoto)) {
                            $innerPhoto = new \Photo($photoId);
                        }
                    } elseif (!isset($innerPhoto)) {
                        $innerPhoto = new \Photo($photoId);
                    }
                }

                $i++;
            }
        }

        // Check if sync is from Opennemas instances for importing author
        if ($element->getServicePartyName() == 'Opennemas') {
            // Check if allow to import authors
            if (isset($server['author']) && $server['author'] == '1') {
                // Get author object,decode it and create new author
                $authorObj = $element->getRightsOwner();

                if (!is_null($authorObj)) {
                    // Fetch author data
                    $authorArray = get_object_vars($authorObj);

                    // Set user as deactivated author without privileges.
                    $authorArray['activated']        = 0;
                    $authorArray['id_user_group']    = ['3'];
                    $authorArray['accesscategories'] = [];

                    // Create author
                    $user = new \User();

                    if (!is_null($authorArray['id']) &&
                        !$user->checkIfUserExists($authorArray) &&
                        $user->checkIfExistsUserEmail($authorArray['email']) &&
                        $user->checkIfExistsUserName($authorArray['username'])
                    ) {
                        // Create new user
                        if ($user->create($authorArray)) {
                            // Write in log
                            $logger = $this->get('application.log');
                            $logger->info(
                                'User ' . $authorArray['username'] .
                                ' was created from importer by user ' .
                                $this->getUser()->name . ' (' . $this->getUser()->id . ')'
                            );
                        }

                        // Set user meta if exists
                        if ($authorObj->meta) {
                            $userMeta = get_object_vars($authorObj->meta);
                            $user->setMeta($userMeta);
                        }

                        // Fetch and save author image if exists
                        $authorImgUrl   = $element->getRightsOwnerPhoto();
                        $cm             = new \ContentManager();
                        $authorPhotoRaw = $cm->getUrlContent($authorImgUrl);
                        if ($authorPhotoRaw) {
                            $localImageDir  = MEDIA_IMG_PATH . $authorObj->photo->path_file;
                            $localImagePath = MEDIA_IMG_PATH . $authorObj->photo->path_img;
                            if (!is_dir($localImageDir)) {
                                \Onm\FilesManager::createDirectory($localImageDir);
                            }

                            if (file_exists($localImagePath)) {
                                unlink($localImagePath);
                            }

                            file_put_contents($localImagePath, $authorPhotoRaw);

                            // Get all necessary data for the photo
                            $infor = new \MediaItem($localImagePath);
                            $data  = [
                                'title'       => $authorObj->photo->name,
                                'name'        => $authorObj->photo->name,
                                'user_name'   => $authorObj->photo->name,
                                'path_file'   => $authorObj->photo->path_file,
                                'nameCat'     => $authorObj->username,
                                'category'    => '',
                                'created'     => $infor->atime,
                                'changed'     => $infor->mtime,
                                'date'        => $infor->mtime,
                                'size'        => round($infor->size / 1024, 2),
                                'width'       => $infor->width,
                                'height'      => $infor->height,
                                'type'        => $infor->type,
                                'type_img'    => substr($authorObj->photo->name, -3),
                                'media_type'  => 'image',
                                'author_name' => $authorObj->username,
                            ];

                            $photo   = new \Photo();
                            $photoId = $photo->create($data);

                            // Get new author id and update avatar_img_id
                            $newAuthor                  = get_object_vars($user->findByEmail($authorObj->email));
                            $authorId                   = $newAuthor['id'];
                            $newAuthor['avatar_img_id'] = $photoId;
                            unset($newAuthor['password']);
                            $user->update($newAuthor);
                        }
                    } else {
                        // Fetch the user if exists and is not null
                        if (!is_null($authorObj->email)) {
                            $author   = $user->findByEmail($authorObj->email);
                            $authorId = $author->id;
                        }
                    }
                }
            }
        }

        // If the new has videos import them
        if ($element->hasVideos()) {
            foreach ($element->getVideos() as $video) {
                $filePath = realpath(
                    $repository->syncPath . DS . $sourceId . DS . $video->getFilePath()
                );

                // If no video from FTP check HTTP
                if (!$filePath) {
                    $filePath = $repository->syncPath . DS . $sourceId . DS . $video->getName();
                    $fileName = $video['name'];
                }

                // Check if the file exists
                if ($filePath) {
                    $videoFileData = [
                        'file_type'      => $video->getFileType(),
                        'file_path'      => $filePath,
                        'category'       => $category,
                        'content_status' => 1,
                        'title'          => $video->getTitle(),
                        'tag_ids'       => $this->get('api.service.tag')
                            ->getTagIdsFromStr($video->getTitle()),
                        'description'    => '',
                        'author_name'    => 'internal',
                    ];

                    $video   = new \Video();
                    $videoID = $video->createFromLocalFile($videoFileData);

                    // If this article has more than one video take the first one
                    if (!isset($innerVideo)) {
                        $innerVideo = new \Video($videoID);
                    }
                }

                $i++;
            }
        }

        $commentsConfig = s::get('comments_config') ? s::get('comments_config') : [];

        $values = [
            'title'          => $element->getTitle(),
            'category'       => $category,
            'with_comment'   =>
                (array_key_exists('with_comments', $commentsConfig) ? $commentsConfig['with_comments'] : 1),
            'content_status' => 0,
            'frontpage'      => 0,
            'in_home'        => 0,
            'title_int'      => $element->getTitle(),
            'tag_ids'        => $this->get('api.service.tag')
                ->getTagIdsFromStr($element->getTitle()),
            'subtitle'       => $element->getPretitle(),
            'agency'         => $server['agency_string'],
            'fk_author'      => (isset($authorId) ? $authorId : 0),
            'summary'        => $element->getSummary(),
            'body'           => $element->getBody(),
            'posic'          => 0,
            'id'             => 0,
            'fk_publisher'   => $this->getUser()->id,
            'img1'           => (isset($frontPhoto) ? $frontPhoto->id : ''),
            'img1_footer'    => (isset($frontPhoto) ? $frontPhoto->description : ''),
            'img2'           => (isset($innerPhoto) ? $innerPhoto->id : ''),
            'img2_footer'    => (isset($innerPhoto) ? $innerPhoto->description : ''),
            'fk_video'       => '',
            'fk_video2'      => (isset($innerVideo) ? $innerVideo->id : ''),
            'footer_video2'  => (isset($innerVideo) ? $innerVideo->title : ''),
            'ordenArti'      => '',
            'ordenArtiInt'   => '',
            'urn_source'     => $element->getUrn(),
        ];

        $article      = new \Article();
        $newArticleID = $article->create($values);

        return $newArticleID;
    }
}
