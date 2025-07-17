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
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));

        $chunk       = $request->files->get('chunk');
        $chunkNumber = $request->request->get('chunkNumber');
        $totalChunks = $request->request->get('totalChunks');
        $fileName    = $request->request->get('fileName');
        $fileId      = $request->request->get('fileId');
        $fileType    = $request->request->get('fileType');
        $helper      = $this->container->get('core.helper.' . $fileType);
        $instance    = $this->container->get('core.instance');
        $uploadDir   = $this->getParameter('kernel.project_dir') . '/tmp/uploads/' . $fileId;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $chunk->move($uploadDir, $chunkNumber);

        if ((int) $chunkNumber + 1 == (int) $totalChunks) {
            $tempFilePath = $uploadDir . '/' . $fileName;
            $output       = fopen($tempFilePath, 'wb');

            for ($i = 0; $i < $totalChunks; $i++) {
                $chunkPath = $uploadDir . '/' . $i;
                $input     = fopen($chunkPath, 'rb');
                stream_copy_to_stream($input, $output);
                fclose($input);
                unlink($chunkPath);
            }
            fclose($output);

            $file         = new \SplFileInfo($tempFilePath);
            $fileSize     = $file->getSize();
            $finalPath    = $helper->generatePath($file, new \DateTime());
            $finalPathTmp = $helper->generateTmpPath($file, new \DateTime(), $instance->internal_name);
            $finalDir     = dirname($finalPath);
            if (!is_dir($finalDir)) {
                mkdir($finalDir, 0777, true);
            }

            rename($tempFilePath, $finalPath);
            rmdir($uploadDir);

            $enabledCompressed = true;
            $enabledProvider   = true;

            if (filter_var($enabledCompressed, FILTER_VALIDATE_BOOLEAN)) {
                $step = [
                    'label'      => 'Compressing',
                    'styleClass' => 'warning',
                    'progress'   => '0%'
                ];
            } elseif (filter_var($enabledProvider, FILTER_VALIDATE_BOOLEAN)) {
                $step = [
                    'label'      => 'Uploading to S3',
                    'styleClass' => 'primary',
                    'progress'   => '0%'
                ];
            } else {
                $step = [
                    'label'      => 'Completed',
                    'styleClass' => 'success',
                    'progress'   => ''
                ];
            }

            return new JsonResponse([
                'source'       => [pathinfo($finalPath, PATHINFO_EXTENSION) =>
                $instance->getVideosShortPath() . '/' . $helper->getRelativePath($finalPath)],
                'status'       => 'done',
                'fileName'     => $file->getFilename(),
                'step'         => $step,
                'fileSize'     => $fileSize,
                'fileSizeMB'   => round($fileSize / 1048576, 2),
                'finalPath'    => $finalPath,
                'finalPathTmp' => $finalPathTmp,
                'path'         => $instance->getVideosShortPath() . '/' . $helper->getRelativePath($finalPath),
                'relativePath' => $instance->getVideosShortPath() . '/' . $helper->getRelativePath($finalPath),
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

        if ($step['label'] === 'Uploading to S3' && $stepProgress == 0 && $information['status'] === 'done') {
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
