<?php
/**
 * Defines the frontend controller for the content archives
 *
 * @package Frontend_Controllers
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Common\Core\Annotation\BotDetector;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Onm\Settings as s;

/**
 * Handles actions for the RTB Module
 *
 * @package Frontend_Controllers
 */
class RTBController extends Controller
{
    /**
     * Returns the file contents
     *
     * @param Request $request the request object
     *
     * @return Response the requested file
     */
    public function showAction(Request $request)
    {
        // Check for the module existence and if it is enabled
        if (!$this->get('core.security')->hasExtension('es.openhost.module.advanced_advertisement')) {
            throw new ResourceNotFoundException();
        }

        // Search for the
        $fileName = $request->query->get('filename');
        $fileId   = $this->checkRTBFileInConfigSettings($fileName);
        $filePath = $this->getFilePath($fileId);

        if (!file_exists($filePath)) {
            throw new ResourceNotFoundException();
        }

        $fileContents = file_get_contents($filePath);

        // Return the resopnse object
        return new Response($fileContents, 200, [
            'x-instance'  => $this->get('core.instance')->internal_name,
            'x-tags'      => 'rtb,',
            'x-cache-for' => '+1 day',
            'x-cacheable' => true,
        ]);
    }

    /**
     * This method checks if the file was added to rtb files
     *
     * @param string $fileName the file to check
     *
     * @return boolean if the file was added
     */
    private function checkRTBFileInConfigSettings($fileName)
    {
        $configurations = $this->get('setting_repository')->get(['rtb_files']);

        if (!is_array($configurations)
            || !array_key_exists('rtb_files', $configurations)
            || !is_array($configurations['rtb_files'])
        ) {
            return null;
        }

        foreach ($configurations['rtb_files'] as $file) {
            if ($file['filename'] == $fileName) {
                return $file['id'];
            }
        }

        return null;
    }

    /**
     * This method gets from the file path from DB
     *
     * @param string $fileId the file Id to recover from Database
     *
     * @return string path for the file
     */
    private function getFilePath($fileId)
    {
        $file = $this->get('entity_repository')->find('Attachment', $fileId);

        if (!is_object($file)) {
            throw new ResourceNotFoundException();
        }

        $path = INSTANCE_MEDIA_PATH . FILE_DIR . $file->path;

        if (!file_exists($path) && !is_readable($path)) {
            throw new ResourceNotFoundException();
        }

        return $path;
    }
}
