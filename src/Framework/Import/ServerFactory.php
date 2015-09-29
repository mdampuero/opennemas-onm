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
     * Returns an instance of the server where to sync from
     *
     * @param array $params The server paramameters.
     w
     * @return Server The Server.
     */
    public function get($serverParams)
    {
        $servers = $this->getServers(__DIR__ . DS . 'Servers');

        foreach ($servers as $name) {
            $class = __NAMESPACE__ . "\\Servers\\" . $name;

            try {
                return new $class($serverParams);
            } catch (\Exception $e) {
            }
        }

        throw new \Exception(_('Unable to create a server'));
    }

    /**
     * Gets a list of available servers with its relative path.
     *
     * @param string $directory The directory where search parsers.
     *
     * @return array The list of available parsers.
     */
    private function getServers($directory)
    {
        if (empty($directory)) {
            return [];
        }

        $path      = __DIR__ . DS . 'Servers';
        $namespace = str_replace([ $path , DS ], [ '', '\\' ], $directory);

        $files = scandir($directory);

        if (!empty($namespace)) {
            $namespace .= '\\';
        }

        $parsers = [];
        foreach ($files as $file) {
            if ($file !== '..' && $file !== '.' && $file !== 'Server.php') {
                if (!is_file($directory . DS . $file)) {
                    $parsers = array_merge(
                        $parsers,
                        $this->getServers($directory . DS . $file)
                    );
                } else {
                    $parsers[] = ltrim($namespace, '\\')
                        . basename($file, '.php');
                }
            }
        }

        uasort($parsers, function ($a, $b) {
            return strlen($a) >= strlen($b) ? -1 : 1;
        });

        return $parsers;
    }
}
