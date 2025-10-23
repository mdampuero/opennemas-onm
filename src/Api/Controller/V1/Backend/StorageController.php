<?php

namespace Api\Controller\V1\Backend;

use Common\Core\Controller\Controller;
use Common\Model\Entity\Task;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\Process;

class StorageController extends Controller
{
    /**
     * Handles uploading a file chunk by chunk.
     *
     * @param Request $request HTTP request containing the file chunk and metadata
     * @return JsonResponse JSON response indicating upload status and file info when complete
     */
    public function uploadChunkAction(Request $request)
    {
        $logger = $this->get('application.log');
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));

        $chunk       = $request->files->get('chunk');
        $chunkNumber = $request->request->get('chunkNumber');
        $totalChunks = $request->request->get('totalChunks');
        $fileName    = $request->request->get('fileName');
        $fileId      = $request->request->get('fileId');
        $fileSize    = $request->request->get('fileSize');
        $fileType    = $request->request->get('fileType');
        $helper      = $this->container->get('core.helper.' . $fileType);
        $instance    = $this->container->get('core.instance');
        $uploadDir   = '/home/opennemas/current/tmp/spool/uploads/' . $fileId;

        $allowedExtensions = ['mp4', 'webm', 'ogg', 'ogv', 'mov', 'avi', 'wmv', 'flv', 'mpeg', 'mpg'];
        $extension         = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            return new JsonResponse([
                'status'  => 'error',
                'message' => 'Only video files are allowed'
            ], 400);
        }

        if ((int) $chunkNumber === 0) {
            $mime = $chunk->getMimeType();
            if (strpos($mime, 'video/') !== 0) {
                return new JsonResponse([
                    'status'  => 'error',
                    'message' => 'Only video files are allowed'
                ], 400);
            }
        }

        /**
         * Log for debugging purposes.
         */
        $logger->info(sprintf('STORAGE - Uploading chunk %d/%d ', $chunkNumber, $totalChunks), [
            'chunkNumber' => $chunkNumber,
            'totalChunks' => $totalChunks,
            'fileName'    => $fileName,
            'fileId'      => $fileId,
            'fileType'    => $fileType,
            'fileSize'    => $fileSize,
        ]);

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $chunk->move($uploadDir, $chunkNumber);

        if ((int) $chunkNumber + 1 == (int) $totalChunks) {
            $logger->info('STORAGE - Uploading finish ', [
                'folderTmp'    => $uploadDir
            ]);
            return new JsonResponse([
                'status'       => 'done',
                'step'         => [
                    'label'      => 'Compressing',
                    'styleClass' => 'warning',
                    'progress'   => '0%'
                ],
                'fileName'     => $fileName,
                'folderTmp'    => $uploadDir,
                'totalChunks'  => $totalChunks,
                'fileSize'     => $fileSize,
                'fileSizeMB'   => round($fileSize / 1048576, 2),
            ]);
        }

        return new JsonResponse(['status' => 'uploading']);
    }

    /**
     * Processes a content item based on its current step and status.
     *
     * @param Request $request HTTP request containing the content primary key
     * @return JsonResponse JSON response with updated item data and step information
     */
    public function processAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));
        $pk_content   = $request->request->get('pk_content');
        $service      = $this->get('api.service.content');
        $item         = $service->getItem($pk_content);
        $step         = $item->information['step'] ?? [];
        $stepProgress = intval(rtrim($step['progress'], '%'));
        $instance     = $this->get('core.instance');
        $information  = $item->information ?? [];

        if ($step['label'] === 'Compressing' && $stepProgress == 0 && $information['status'] === 'done') {
            $information['status'] = 'compressing';
            $service->updateItem($pk_content, [
                'information' => $information
            ]);

            /**
             * Create a new task for the FFMPEG converter.
             */
            $em = $this->get('orm.manager');
            $em->persist(new Task($em->getConverter('Task')->objectify([
                'name' => 'FFMPEG converter',
                'command' => '/home/opennemas/current/bin/console %s --item=%s --instance=%s',
                'params' => [
                    'app:ffmpeg:compress',
                    $item->pk_content,
                    $instance->id
                ],
                'status' => 'pending',
                'instance_id' => $instance->id,
                'created' => new \DateTime()
            ])));
        }

        $uploadingLabels = ['Uploading to S3', 'Uploading to Bunny Stream'];

        if (in_array($step['label'], $uploadingLabels, true)
            && $stepProgress == 0
            && $information['status'] === 'done'
        ) {
            $information['status'] = 'uploading';

            $created = new \Datetime();
            $photo   = $this->container->get('api.service.photo')->createItem([
                'created'     => $created->format('Y-m-d H:i:s'),
                'path_file'   => $created->format('/Y/m/d/'),
                'title'       => $item->title
            ], new \SplFileInfo($information['thumbnails']), true);

            $information['photo'] = $photo->id;
            $service->updateItem($pk_content, [
                'information' => $information
            ]);

            unlink($information['thumbnails'] ?? '');

            $process = new Process(
                sprintf(
                    '/home/opennemas/current/bin/console %s --operation=%s --item=%s --instance=%s',
                    'app:core:storage',
                    'uploadByItem',
                    $item->pk_content,
                    $instance->id
                )
            );
            $process->start();
        }

        return new JsonResponse([
            'item' => $service->responsify($item),
            'step' => $step
        ]);
    }
}
