<?php
/**
 * Handles the actions for the framework status
 *
 * @package Manager_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Manager\Controller;

use Symfony\Component\HttpFoundation\Request;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the framework status
 *
 * @package Manager_Controllers
 **/
class FrameworkStatusController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \TemplateManager(TEMPLATE_MANAGER);
    }

    /**
     * Checks and shows the fullfilment framework dependencies
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function checkDependenciesAction(Request $request)
    {
        chdir(APPLICATION_PATH);
        $command = APPLICATION_PATH.'/bin/console framework:check-dependencies';
        $status = shell_exec($command);

        return $this->render(
            'framework/status.tpl',
            array('status' => $status)
        );
    }

    /**
     * Shows the APC information iframe
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function opcacheStatusAction(Request $request)
    {
        $config = $status = $mem = $stats =  $freeKeys =  $notSupportedMessage = null;
        $statusKeyValues = $directivesKeyValues = $newDirs = null;

        if (!extension_loaded('Zend OPcache')) {
            $notSupportedMessage = 'You do not have the Zend OPcache extension loaded.';
        }

        $action = $request->query->filter('action', null, FILTER_SANITIZE_STRING);
        if ($action == 'reset') {
            opcache_reset();
            return $this->redirect($this->generateUrl('manager_framework_opcache_status'));
        }

        // Fetch configuration and status information from OpCache
        $config = opcache_get_configuration();
        $status = opcache_get_status();
        if (!$config['directives']['opcache.enable']) {
            $notSupportedMessage = 'Zend OPcache extension loaded but not activated [opcache.enable != true].';
        }

        $mem      = $status['memory_usage'];
        $stats    = $status['opcache_statistics'];
        $freeKeys = $stats['max_cached_keys'] - $stats['num_cached_keys'];
        // var_dump($status);die();

        $statusKeyValues = array();
        foreach ($status as $key => $value) {
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
                        || $k  ===  'wasted_memory'
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

        $directivesKeyValues = array();
        foreach ($config['directives'] as $key => $value) {
            if ($value === false) {
                $value = 'false';
            }
            if ($value === true) {
                $value = 'true';
            }
            if ($key == 'opcache.memory_consumption') {
                $value = $this->sizeForHumans($value);
            }

            $directivesKeyValues[$key] = $value;
        }

        if (!array_key_exists('scripts', $status)) {
            $status['scripts'] = array();
        }
        $filesKeyValues = array();
        $dirs = array();
        foreach ($status['scripts'] as $key => $data) {
            $dirs[dirname($key)][basename($key)] = $data;
        }
        asort($dirs);

        $id = 1;
        $newDirs = array();

        foreach ($dirs as $dir => $files) {
            $memoryConsumption = 0;
            $newFiles = array();
            foreach ($files as $file => $data) {
                $memoryConsumption += $data["memory_consumption"];

                $newFile = $data;

                $newFile['memory_consumption_human_readable'] =
                    $this->sizeForHumans($data["memory_consumption"]);

                $newFiles []= $newFile;
            }

            $newDir = array(
                'name'                     => $dir,
                'total_memory_consumption' => $this->sizeForHumans($memoryConsumption),
                'count'                    => count($newFiles),
                'files'                    => $newFiles
            );

            $newDirs []= $newDir;
        }

        return $this->render(
            'framework/opcache_status.tpl',
            array(
                'not_supported_message' => $notSupportedMessage,
                'config'                => $config,
                'status'                => $status,
                'mem'                   => $mem,
                'stats'                 => $stats,
                'free_keys'             => $freeKeys,
                'status_key_values'     => $statusKeyValues,
                'directive_key_values'  => $directivesKeyValues,
                'files_key_values'      => $newDirs,
            )
        );
    }

    /**
     * Turn bytes into a human readable format
     * @param $bytes
     */
    public function sizeForHumans($bytes)
    {
        if ($bytes > 1048576) {
            return sprintf('%.2f&nbsp;MB', $bytes / 1048576);
        } else if ($bytes > 1024) {
            return sprintf('%.2f&nbsp;kB', $bytes / 1024);
        } else {
            return sprintf('%d&nbsp;bytes', $bytes);
        }
    }
}
