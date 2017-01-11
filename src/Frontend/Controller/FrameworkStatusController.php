<?php

namespace Frontend\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class FrameworkStatusController extends Controller
{

    /**
     * Checks if the framework components are working properly.
     *
     * @return boolean True, if the cache is working properly. Otherwise,
     *                 returns false.
     */
    public function checkFrameworkAction()
    {
        $status = [];

        $status['cache']    = $this->checkCacheAction();
        $status['database'] = $this->checkDatabaseAction();
        $status['nfs']      = $this->checkNFSAction();

        $code     = 200;
        $response = [];

        foreach ($status as $key => $value) {
            $response[$key] = str_replace('"', '', $value->getContent());

            if ($value->getStatusCode() !== 200) {
                $code = 500;
            }
        }

        return new JsonResponse($response, $code);
    }
    /**
     * Checks if the current cache service is working properly.
     *
     * @return boolean True, if the cache is working properly. Otherwise,
     *                 returns false.
     */
    public function checkCacheAction()
    {
        $result = $this->get('core.status.checker')->checkCacheConnection();

        if ($result) {
            $response = new JsonResponse('OK', 200);
        } else {
            $response = new JsonResponse('FAILURE', 500);
        }

        return $response;
    }

    /**
     * Checks if the file system (NFS) is working properly.
     *
     * @return boolean True, if the cache is working properly. Otherwise,
     *                 returns false.
     */
    public function checkDatabaseAction()
    {
        $result = $this->get('core.status.checker')->checkDatabaseConnection();

        if ($result) {
            $response = new JsonResponse('OK', 200);
        } else {
            $response = new JsonResponse('FAILURE', 500);
        }

        return $response;
    }

    /**
     * Checks if the file system (NFS) is working properly.
     *
     * @return boolean True, if the cache is working properly. Otherwise,
     *                 returns false.
     */
    public function checkNFSAction()
    {
        $result = $this->get('core.status.checker')->checkNfs();

        if ($result) {
            $response = new JsonResponse('OK', 200);
        } else {
            $response = new JsonResponse('FAILURE', 500);
        }

        return $response;
    }
}
