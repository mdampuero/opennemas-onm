<?php
/**
 * Implements the ServerFactory class
 *
 * This file is part of the Onm package.
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import;

/**
 * Initializes a Server to download files from.
 */
class ServerFactory
{
    /**
     * The template service.
     *
     * @var TemplateAdmin
     */
    protected $tpl;

    /**
     * Initializes the ServierFactory.
     *
     * @param TemplateAdmin $tpl The template service.
     */
    public function __construct($tpl)
    {
        $this->tpl = $tpl;
    }

    /**
     * Returns an instance of the server where to sync from
     *
     * @param array $params The server paramameters.
     *
     * @return Server The Server.
     */
    public function get($params)
    {
        $directory = __DIR__ . DS . 'Server';
        $servers   = $this->getServers($directory);

        foreach ($servers as $name) {
            $class = __NAMESPACE__ . '\\Server'
                . str_replace([$directory, DS ], [ '', '\\'], $name);

            try {
                return new $class($params, $this->tpl);
            } catch (\Exception $e) {
                continue;
            }
        }

        throw new \Exception(_('Unable to create a server'));
    }

    /**
     * Gets a list of available servers with its relative path.
     *
     * @param string $directory The directory where search servers.
     *
     * @return array The list of available servers.
     */
    private function getServers($directory)
    {
        if (empty($directory)) {
            return [];
        }

        $files = scandir($directory);

        $servers = [];
        foreach ($files as $file) {
            if ($file !== '..'
                && $file !== '.'
                && $file !== 'Server.php'
                && $file !== 'Http.php'
            ) {
                if (!is_file($directory . DS . $file)) {
                    $servers = array_merge(
                        $servers,
                        $this->getServers($directory . DS . $file)
                    );
                } else {
                    $servers[] = $directory . DS . basename($file, '.php');
                }
            }
        }

        uasort($servers, function ($a, $b) {
            return strlen($a) >= strlen($b) ? -1 : 1;
        });

        return $servers;
    }
}
