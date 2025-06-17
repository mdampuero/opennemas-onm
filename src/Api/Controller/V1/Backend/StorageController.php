<?php

namespace Api\Controller\V1\Backend;

use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class StorageController extends Controller
{
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
        $uploadDir   = $this->getParameter('kernel.project_dir') . '/var/uploads/temp/' . $fileId;

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Save the chunk
        $chunk->move($uploadDir, $chunkNumber);
        //sleep(1);
        // Check if this is the last chunk
        if ((int) $chunkNumber + 1 == (int) $totalChunks) {
            // Reconstruct the file from chunks
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

            // Create a SplFileInfo object for the reconstructed file
            $file = new \SplFileInfo($tempFilePath);

            // Get path and filename
            $finalPath = $helper->generatePath($file, new \DateTime());

            // Create the final directory if it doesn't exist
            $finalDir = dirname($finalPath);
            if (!is_dir($finalDir)) {
                mkdir($finalDir, 0777, true);
            }

            // Move the reconstructed file to its final destination
            rename($tempFilePath, $finalPath);

            // Remove the temporary upload directory
            rmdir($uploadDir);

            // Respond with the file information
            return new JsonResponse([
                'status'   => 'done',
                'fileName' => $file->getFilename(),
                'finalPath' => $finalPath,
                'relativePath' => $instance->getVideosShortPath() . '/' . $helper->getRelativePath($finalPath),
            ]);
        }

        // Uploading chunk
        return new JsonResponse(['status' => 'uploading']);
    }
}
