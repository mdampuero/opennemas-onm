<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Common\Core\Controller\Controller;

class FrameworkStatusController extends Controller
{
    /**
     * Shows the APC information iframe.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse|RedirectResponse The response object.
     *
     * @Security("hasPermission('OPCACHE_LIST')")
     */
    public function opcacheStatusAction(Request $request)
    {
        if (!extension_loaded('Zend OPcache')) {
            $notSupportedMessage = 'You do not have the Zend OPcache extension loaded.';
        }

        $action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);
        if ($action == 'reset') {
            \opcache_reset();

            return $this->redirect($this->generateUrl('manager_framework_opcache_status'));
        }

        // Fetch configuration and status information from OpCache
        $config = \opcache_get_configuration();
        $status = \opcache_get_status();

        $mem          = $status['memory_usage'];
        $opcacheStats = $status['opcache_statistics'];
        $freeKeys     = $opcacheStats['max_cached_keys'] - $opcacheStats['num_cached_keys'];

        if (!array_key_exists('scripts', $status)) {
            $status['scripts'] = [];
        }

        if (!$config['directives']['opcache.enable']) {
            $notSupportedMessage = 'Zend OPcache extension loaded but not activated [opcache.enable != true].';
        }

        $statusKeyValues     = $this->getStatus($status);
        $directivesKeyValues = $this->getDirectives($config);
        $newDirs             = $this->getNewDirs($status['scripts'], $config);

        return new JsonResponse([
            'not_supported_message' => $notSupportedMessage,
            'config'                => $config,
            'status'                => $status,
            'mem'                   => $mem,
            'stats'                 => $opcacheStats,
            'free_keys'             => $freeKeys,
            'status_key_values'     => $statusKeyValues,
            'directive_key_values'  => $directivesKeyValues,
            'files_key_values'      => $newDirs,
        ]);
    }

    /**
     * Returns an array representing the current status of the opcache
     *
     * @param  array $status the opcache status values
     *
     * @return array
     */
    public function getStatus($status)
    {
        $statusKeyValues = [];
        if (!is_array($status)) {
            $status = [];
        }

        foreach ($status as $key => &$value) {
            if ($key === 'scripts') {
                continue;
            }

            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ($v === false) {
                        $value = 'false';
                    }
                    if ($v === true) {
                        $value = 'true';
                    }

                    if ($k === 'used_memory'
                        || $k === 'free_memory'
                        || $k === 'wasted_memory'
                    ) {
                        $v = $this->sizeForHumans($v);
                    }

                    if ($k === 'current_wasted_percentage'
                        || $k === 'opcache_hit_rate'
                    ) {
                        $v = number_format($v, 2) . '%';
                    }

                    if ($k === 'blacklist_miss_ratio') {
                        $v = number_format($v, 2) . '%';
                    }

                    if ($k === 'start_time'
                        || $k === 'last_restart_time'
                    ) {
                        $v = ($v ? date(DATE_RFC822, $v) : 'never');
                    }

                    $statusKeyValues[$k] = $v;
                }
                continue;
            }
            if ($value === false) {
                $value = 'false';
            }

            if ($value === true) {
                $value = 'true';
            }

            $statusKeyValues[$key] = $value;
        }

        return $statusKeyValues;
    }

    /**
     * Returns the list of OpCache directives
     *
     * @param  array $config the opcache configuration values
     *
     * @return array
     */
    public function getDirectives($config)
    {
        $directivesKeyValues = [];
        foreach ($config['directives'] as $key => $value) {
            $value = ($value === false) ? 'false' : 'true';

            if ($key == 'opcache.memory_consumption') {
                $value = $this->sizeForHumans($value);
            }

            $directivesKeyValues[$key] = $value;
        }

        return $directivesKeyValues;
    }

    /**
     * Returns the list of cached binaries in opcache with its
     * memory consumption grouped by folder
     *
     * @param array $scripts the list of scripts
     * @param array $data scripts related opcache data
     *
     * @return array the list of cached binaries
     */
    public function getNewDirs($scripts, $data)
    {
        $dirs = [];
        foreach ($scripts as $key => $data) {
            $dirs[dirname($key)][basename($key)] = $data;
        }
        asort($dirs);

        $newDirs = [];
        foreach ($dirs as $dir => $files) {
            $memoryConsumption = 0;
            $newFiles          = [];
            foreach ($files as $data) {
                $memoryConsumption += $data["memory_consumption"];

                $newFile = $data;

                $newFile['memory_consumption_human_readable'] =
                    $this->sizeForHumans($data["memory_consumption"]);

                $newFiles[] = $newFile;
            }

            $newDir = [
                'name'                     => $dir,
                'total_memory_consumption' => $this->sizeForHumans($memoryConsumption),
                'count'                    => count($newFiles),
                'files'                    => $newFiles
            ];

            $newDirs[] = $newDir;
        }

        return $newDirs;
    }

    /**
     * Turns a number of bytes into a human readable format
     *
     * @param int $bytes the number of bytes
     *
     * @return string the formated number
     */
    public function sizeForHumans($bytes)
    {
        if ($bytes > 1048576) {
            return sprintf('%.2f MB', $bytes / 1048576);
        } elseif ($bytes > 1024) {
            return sprintf('%.2f kB', $bytes / 1024);
        } else {
            return sprintf('%d bytes', $bytes);
        }
    }
}
