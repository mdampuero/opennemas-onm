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
     * Lists the files for a given category.
     *
     * @return Response          The response object.
     *
     * @Security("hasExtension('FILE_MANAGER')
     *     and hasPermission('ATTACHMENT_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('files/list.tpl');
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
            return $this->render('files/new.tpl', [
                'locale' => $this->get('core.locale')->getLocale('frontend'),
            ]);
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

            return $this->redirect($this->generateUrl('admin_files_create'));
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

            return $this->redirect($this->generateUrl('admin_files_create'));
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

        if (!$this->get('core.security')->hasPermission('MASTER')
            && !preg_match($regexp, $uploadedFile->getClientOriginalExtension())
        ) {
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

            return $this->redirect($this->generateUrl('admin_files_create'));
        }

        $date          = new \DateTime();
        $directoryDate = $date->format("/Y/m/d/");
        $fileName      = \Onm\StringUtils::cleanFileName($uploadedFile->getClientOriginalName());
        $basePath      = $this->container->getParameter('core.paths.public')
            . $this->get('core.instance')->getFilesShortPath()
            . $directoryDate;
        // Create folder if it doesn't exist
        if (!file_exists($basePath)) {
            $directoryCreated = \Onm\FilesManager::createDirectory($basePath);
            if (!$directoryCreated) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    sprintf(_('Unable to create the directory to save the file'))
                );

                return $this->redirect($this->generateUrl('admin_files_create'));
            }
        }

        //  If the file extension is html or js this files will be use for sever them directly from a expecific
        $data = [
            'title'          => $request->request
                ->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'path'           => $directoryDate . $fileName,
            'category'       => $request->request->filter('category', null, FILTER_SANITIZE_STRING),
            'content_status' => (int) !preg_match('@(js|html)$@', $uploadedFile->getClientOriginalExtension()),
            'description'    => $request->request->get('description', ''),
            'fk_publisher'   => $this->getUser()->id,
            'tags'           => json_decode($request->request->get('tags', ''), true)
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

            return $this->redirect($this->generateUrl('admin_files_create'));
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

        return $this->redirect($this->generateUrl('admin_files_create'));
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
        $file = $this->get('entity_repository')->find('Attachment', $id);

        if (!is_object($file) || is_null($file->pk_attachment)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the file with the id "%s"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_files'));
        }

        return $this->render('files/new.tpl', [
            'attaches' => $file,
            'locale'   => $this->get('core.locale')->getRequestLocale('frontend'),
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

        $file = $this->get('entity_repository')->find('Attachment', $id);

        $data = [
            'title'          => $request->request
                ->filter('title', null, FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'category'       => $request->request->filter('category', null, FILTER_SANITIZE_STRING),
            'content_status' => 1,
            'id'             => (int) $id,
            'description'    => $request->request->filter('description', null),
            'fk_publisher'   => $this->getUser()->id,
            'tags'           => json_decode($request->request->get('tags', ''), true)
        ];

        if ($file->update($data)) {
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
}
