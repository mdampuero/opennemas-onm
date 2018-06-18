<?php
/**
 * Defines the frontend controller for the advertisement content type
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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;

/**
 * Defines the frontend controller for the advertisement content type
 *
 * @package Frontend_Controllers
 */
class AdvertisementController extends Controller
{
    /**
     * Redirects the user to the target URL defined by an advertisement
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     */
    public function redirectAction(Request $request)
    {
        $dirtyID = $request->query->filter('id', null, FILTER_SANITIZE_STRING);

        // Resolve ad ID, search in repository or redirect to 404
        $advertisement = $this->get('content_url_matcher')
            ->matchContentUrl('advertisement', $dirtyID);

        if (empty($advertisement)) {
            throw new ResourceNotFoundException();
        }

        // Increase number of clicks
        $advertisement->setNumClics($advertisement->id);

        if ($advertisement->url) {
            return $this->redirect($advertisement->url);
        } else {
            return new Response('<script type="text/javascript">window.close();</script>');
        }
    }

    /**
     * Displays a public record of Authorized Digital Sellers - ads.txt file
     *
     * @return Response The response object.
     */
    public function showAdsTxtAction()
    {
        // Check ads module and show default ads.txt if deactivated
        if (!$this->get('core.security')->hasExtension('ADS_MANAGER')) {
            $instanceName = getService('core.instance')->internal_name;

            $content = "google.com, pub-7694073983816204, DIRECT, f08c47fec0942fa0\n"
                . "#SmartAdServer\n"
                . "smartadserver.com,3035,DIRECT\n"
                . "contextweb.com,560288,DIRECT,89ff185a4c4e857c\n"
                . "pubmatic.com,156439,DIRECT\n"
                . "pubmatic.com,154037,DIRECT\n"
                . "rubiconproject.com,16114,DIRECT,0bfd66d529a55807\n"
                . "openx.com,537149888,DIRECT,6a698e2ec38604c6\n"
                . "sovrn.com,257611,DIRECT,fafdf38b16bf6b2b\n"
                . "appnexus.com,3703,DIRECT,f5ab79cb980f11d1";

            return new Response($content, 200, [
                'Content-Type' => 'text/plain',
                'x-cacheable'  => true,
                'x-cache-for'  => '100d',
                'x-tags'       => 'instance-' . $instanceName . ',ads,txt',
                'x-instance'   => $instanceName,
            ]);
        }

        // Check for the module existence and if it is enabled
        if (!$this->get('core.security')->hasExtension('es.openhost.module.advancedAdvertisement')) {
            throw new ResourceNotFoundException();
        }

        $content      = $this->get('setting_repository')->get('ads_txt');
        $instanceName = getService('core.instance')->internal_name;

        return new Response(trim($content), 200, [
            'Content-Type' => 'text/plain',
            'x-cacheable'  => true,
            'x-cache-for'  => '100d',
            'x-tags'       => 'instance-' . $instanceName . ',ads,txt',
            'x-instance'   => $instanceName,
        ]);
    }

    /**
     * Returns the file contents
     *
     * @param Request $request the request object
     *
     * @return Response the requested file
     */
    public function showRTBFileAction(Request $request)
    {
        // Check for the module existence and if it is enabled
        if (!$this->get('core.security')->hasExtension('es.openhost.module.advancedAdvertisement')) {
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
