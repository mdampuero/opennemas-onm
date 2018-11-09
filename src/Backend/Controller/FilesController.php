<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 */
class FilesController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $this->contentType = \ContentManager::getContentTypeIdFromName('attachment');
        $this->category    = $this->get('request_stack')->getCurrentRequest()
            ->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $this->ccm         = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->datos_cat) =
            $this->ccm->getArraysMenu($this->category, $this->contentType);

        $this->view->assign([
            'subcat'       => $this->subcat,
            'allcategorys' => $this->parentCategories,
            'datos_cat'    => $this->datos_cat,
            'category'     => $this->category,
        ]);

        // Optimize  this crap from this ---------------------------------------
        $this->fileSavePath = INSTANCE_MEDIA_PATH . FILE_DIR;

        // Create folder if it doesn't exist
        if (!file_exists($this->fileSavePath)) {
            \Onm\FilesManager::createDirectory($this->fileSavePath);
        }
    }

    /**
     * Lists the files for a given category.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_ADMIN')")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => null ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->pk_content_category
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->pk_content_category
                ];
            }
        }

        return $this->render(
            'files/list.tpl',
            [ 'categories' => $categories ]
        );
    }

    /**
     * Shows the files in the widget.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_ADMIN')")
     */
    public function widgetAction()
    {
        return $this->render('files/list.tpl', [
            'category' => 'widget'
        ]);
    }

    /**
     * Shows the file usage statistics.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_ADMIN')")
     */
    public function statisticsAction()
    {
        $cm               = new \ContentManager();
        $total_num_photos = 0;
        $files            = $size = $sub_size = $num_photos = [];
        $fullcat          = $this->ccm->orderByPosmenu($this->ccm->categories);

        $num_sub_photos = [];
        $sub_files      = [];
        $aux_categories = [];

        foreach ($this->parentCategories as $k => $v) {
            $num_photos[$k]    =
                $this->ccm->countContentByType($v->pk_content_category, $this->contentType);
            $total_num_photos += $num_photos[$k];

            $files[$v->pk_content_category] = $cm->findAll(
                'Attachment',
                'fk_content_type = 3 AND category = ' . $v->pk_content_category,
                'ORDER BY created DESC'
            );

            if (!empty($fullcat)) {
                foreach ($fullcat as $child) {
                    if ($v->pk_content_category == $child->fk_content_category) {
                        $num_sub_photos[$k][$child->pk_content_category] =
                            $this->ccm->countContentByType($child->pk_content_category, 3);
                        $total_num_photos                               +=
                            $num_sub_photos[$k][$child->pk_content_category];
                        $sub_files[$child->pk_content_category][]        =
                            $cm->findAll(
                                'Attachment',
                                'fk_content_type = 3 AND category = ' . $child->pk_content_category,
                                'ORDER BY created DESC'
                            );
                        $aux_categories[]                                = $child->pk_content_category;
                        $sub_size[$k][$child->pk_content_category]       = 0;
                        $this->view->assign('num_sub_photos', $num_sub_photos);
                    }
                }
            }
        }

        //Calculo del tamaño de los ficheros por categoria/subcategoria
        $i          = 0;
        $total_size = 0;
        foreach ($files as $categories => $contenido) {
            $size[$i] = 0;
            if (!empty($contenido)) {
                foreach ($contenido as $value) {
                    if ($categories == $value->category) {
                        if (file_exists($this->fileSavePath . '/' . $value->path)) {
                            $size[$i] += filesize($this->fileSavePath . '/' . $value->path);
                        }
                    }
                }
            }

            $total_size += $size[$i];
            $i++;
        }

        if (!empty($parentCategories) && !empty($aux_categories)) {
            foreach ($parentCategories as $k => $v) {
                foreach ($aux_categories as $ind) {
                    if (empty($sub_files[$ind][0])) {
                        continue;
                    }
                    foreach ($sub_files[$ind][0] as $value) {
                        if ($v->pk_content_category != $this->ccm->get_id($this->ccm->getFather($value->catName))) {
                            continue;
                        }
                        if ($this->ccm->get_id($this->ccm->getFather($value->catName))) {
                            $sub_size[$k][$ind] += filesize(MEDIA_PATH . '/' . FILE_DIR . '/' . $value->path);
                        }
                    }

                    if (isset($sub_size[$k][$ind])) {
                        $total_size += $sub_size[$k][$ind];
                    }
                }
            }
        }

        return $this->render('files/statistics.tpl', [
            'total_img'    => $total_num_photos,
            'total_size'   => $total_size,
            'size'         => $size,
            'sub_size'     => $sub_size,
            'num_photos'   => $num_photos,
            'categorys'    => $this->parentCategories,
            'subcategorys' => $this->subcat,
        ]);
    }

    /**
     * Creates a file.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' != $request->getMethod()) {
            $ls = $this->get('core.locale');
            return $this->render(
                'files/new.tpl',
                [
                    'category' => $this->category,
                    'locale'   => $ls->getLocale('frontend'),
                    'tags'     => []
                ]
            );
        }

        set_time_limit(0);
        $files = $request->files->all();

        if (!is_array($files)
            || (is_array($files) && !array_key_exists('path', $files))
            || (is_array($files) && array_key_exists('path', $files) && count($files['path']) <= 0)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must pick a file before submitting the form')
            );

            return $this->redirect(
                $this->generateUrl('admin_files_create', ['category' => $this->category])
            );
        }

        $uploadedFile = $files['path'];

        if (!$uploadedFile->isValid()) {
            $this->get('error.log')->error(sprintf(
                'There was a problem uploading %s .Error Code: %s',
                $uploadedFile->getClientOriginalName(),
                $uploadedFile->getError()
            ));
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('You must pick a file smaller than %d Mb'), MAX_UPLOAD_FILE / 1024 / 1024)
            );

            return $this->redirect(
                $this->generateUrl('admin_files_create', ['category' => $this->category])
            );
        }

        $rtbMediaManager = '';
        if ($this->get('core.security')->hasExtension('es.openhost.module.rtb_media_advertisement')) {
            $rtbMediaManager = '|js|html';
        }

        // White list of file types that we allow
        $regexp = sprintf(
            '@(7z|avi|bmp|bz2|css|csv|doc|docx|eot|flac|flv|gif|gz'
            . '|ico|jpeg|jpg|js|mka|mkv|mov|mp3|mp4|mpeg|mpg|odt|odp|ods|odw'
            . '|otf|ogg|ogm|opus|pdf|png|ppt|pptx|rar|rtf|svg|svgz|swf|tar|tbz'
            . '|tgz|ttf|txt|txz|wav|webm|webp|woff|woff2|xls|xlsx|xml|xz|zip%s)$@',
            $rtbMediaManager
        );

        if (!preg_match($regexp, $uploadedFile->getClientOriginalExtension())) {
            $this->get('error.log')->error(sprintf(
                'User %s tried to upload a not allowed file type %s (%s).',
                $this->getUser()->id,
                $uploadedFile->getClientOriginalExtension(),
                $uploadedFile->getClientOriginalName()
            ));
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('We are sorry, file extension %s is not allowed for upload as it could '
                    . 'contain malicious code. Please contact with our support  or retry using a '
                    . 'different file extension.'), $uploadedFile->getClientOriginalExtension())
            );

            return $this->redirect(
                $this->generateUrl('admin_files_create', ['category' => $this->category])
            );
        }

        $date          = new \DateTime();
        $directoryDate = $date->format("/Y/m/d/");
        $basePath      = $this->fileSavePath . $directoryDate;
        $fileName      = \Onm\StringUtils::cleanFileName($uploadedFile->getClientOriginalName());
        // Create folder if it doesn't exist
        if (!file_exists($basePath)) {
            $directoryCreated = \Onm\FilesManager::createDirectory($basePath);
            if (!$directoryCreated) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    sprintf(_('Unable to create the directory to save the file'))
                );

                return $this->redirect(
                    $this->generateUrl('admin_files_create', ['category' => $this->category])
                );
            }
        }

        //  If the file extension is html or js this files will be use for sever them directly from a expecific
        $data = [
            'title'          => $request->request
                ->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'path'           => $directoryDate . $fileName,
            'category'       => $request->request->filter('category', null, FILTER_SANITIZE_STRING),
            'content_status' => !preg_match('@(js|html)$@', $uploadedFile->getClientOriginalExtension()),
            'description'    => $request->request->get('description', ''),
            'fk_publisher'   => $this->getUser()->id,
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        // Move uploaded file
        try {
            $uploadedFile->move($basePath, $fileName);
        } catch (\Exception $e) {
            $this->get('error.log')->error($e->getMessage());
            $this->get('session')->getFlashBag()->add(
                'error',
                _('There was an error while uploading the file.')
            );

            return $this->redirect(
                $this->generateUrl('admin_files_create', ['category' => $this->category])
            );
        }

        $attachment = new \Attachment();
        if ($attachment->create($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _("File created successfuly.")
            );

            return $this->redirect(
                $this->generateUrl('admin_file_show', ['id' => $attachment->id])
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to upload the file: A file with the same name already exists.')
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_files_create', ['category' => $this->category])
        );
    }

    /**
     * Shows file data given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id   = $request->query->getDigits('id');
        $page = $request->query->getDigits('page');

        $file = new \Attachment($id);


        // If the file doesn't exists redirect to the listing
        // and show error message
        if (is_null($file->pk_attachment)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the file with the id "%s"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_files'));
        }
        $auxTagIds     = $file->getContentTags($file->id);
        $file->tag_ids = array_key_exists($file->id, $auxTagIds) ?
            $auxTagIds[$file->id] :
            [];

        $ls = $this->get('core.locale');
        return $this->render('files/new.tpl', [
            'attaches' => $file,
            'page'     => $page,
            'locale'         => $ls->getRequestLocale('frontend'),
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($file->tag_ids)['items']
        ]);
    }

    /**
     * Updates a file given the data sent by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->request->getDigits('id');

        $file = new \Attachment($id);
        $data = [
            'title'          => $request->request
                ->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'category'       => $request->request->filter('category', null, FILTER_SANITIZE_STRING),
            'content_status' => 1,
            'id'             => (int) $id,
            'description'    => $request->request->filter('description', null),
            'fk_publisher'   => $this->getUser()->id,
            'tag_ids'        => json_decode($request->request->get('tag_ids', ''), true)
        ];

        if ($file->update($data)) {
            dispatchEventWithParams('content.update', [ 'content' => $file ]);
            $this->get('session')->getFlashBag()->add('success', sprintf(_('File successfully updated.')));
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('There was a problem while saving the file information.'))
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_file_show', [ 'id' => $id, ])
        );
    }

    /**
     * Save positions for widget.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_ADMIN')")
     */
    public function savePositionsAction(Request $request)
    {
        $positions = $request->request->get('positions');

        $result = true;
        if (isset($positions)
            && is_array($positions)
            && count($positions) > 0
        ) {
            $pos = 1;
            foreach ($positions as $id) {
                $file   = new \Attachment($id);
                $result = $result && $file->setPosition($pos);
                $pos   += 1;
            }
        }

        if ($result) {
            $msg = "<div class='alert alert-success'>"
                . _("Positions saved successfully.")
                . '<button data-dismiss="alert" class="close">×</button></div>';
        } else {
            $msg = "<div class='alert alert-error'>"
                . _("Unable to save the new positions. Please contact with your system administrator.")
                . '<button data-dismiss="alert" class="close">×</button></div>';
        }

        return new Response($msg);
    }


    /**
     * Lists all the files within a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('items_per_page', 20);

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = [
            'content_type_name' => [ [ 'value' => 'attachment' ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ]
        ];

        if ($categoryId != 0) {
            $filters['category_name'] = [ [ 'value' => $category->name ] ];
        }

        $countFiles = true;
        $files      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countFiles);

        $pagination = $this->get('paginator')->get([
            'epp'   => $itemsPerPage,
            'page'  => $page,
            'total' => $countFiles,
            'route' => [
                'names'  => 'admin_files_content_provider_related',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            [
                'contentType'           => 'Attachment',
                'contents'              => $files,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $this->category,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_files_content_provider_related'),
            ]
        );
    }
}
