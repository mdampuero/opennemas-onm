<?php

namespace Frontend\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class FrameworkStatusController extends Controller
{
    /**
     * Checks if the current cache service is working properly.
     *
     * @return boolean True, if the cache is working properly. Otherwise,
     *                 returns false.
     */
    public function checkCacheAction()
    {
        $cacheId = 'framework.cache.check';
        $cache = $this->get('cache');

        $cache->save($cacheId, 'bar');

        if ($cache->fetch($cacheId) !== 'bar'
            || $cache->delete($cacheId) !== 1
        ) {
            return new JsonResponse('FAILURE', 500);
        }

        return new JsonResponse('OK', 200);
    }

    /**
     * Checks if the file system (NFS) is working properly.
     *
     * @return boolean True, if the cache is working properly. Otherwise,
     *                 returns false.
     */
    public function checkDatabaseAction()
    {
        $conn = $this->get('dbal_connection');

        $rs = $conn->executeQuery('SHOW VARIABLES LIKE "version"');

        if ($rs) {
            $rs = $rs->fetchAll();

            if (count($rs) === 1
                && array_key_exists('Variable_name', $rs[0])
                && $rs[0]['Variable_name'] == 'version'
            ) {
                return new JsonResponse('OK', 200);
            }
        }

        return new JsonResponse('FAILURE', 500);
    }

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
                $code = 207;
            }
        }

        return new JsonResponse($response, $code);
    }

    /**
     * Checks if the file system (NFS) is working properly.
     *
     * @return boolean True, if the cache is working properly. Otherwise,
     *                 returns false.
     */
    public function checkNFSAction()
    {
        $dir      = APPLICATION_PATH . '/tmp/cache/common';
        $filename = $dir . '/framework.nfs.check';

        if (!file_exists($dir)) {
            if (mkdir($dir, 0777, true) === false) {
                return new JsonResponse('FAILURE', 500);
            }
        }

        if (!file_put_contents($filename, 'bar', true)) {
            return new JsonResponse('FAILURE', 500);
        }

        if (file_exists($filename)) {
            if (!unlink($filename)) {
                return new JsonResponse('FAILURE', 500);
            }
        }

        return new JsonResponse('OK', 200);
    }
}
