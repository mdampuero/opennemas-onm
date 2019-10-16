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
namespace Common\NewsAgency\Component\Factory;

use Symfony\Component\Finder\Finder;

/**
 * Initializes a Server to download files from.
 */
class ServerFactory
{
   /**
     * The Finder component.
     *
     * @var Finder
     */
    protected $finder;

    /**
     * The list of available servers.
     *
     * @var array
     */
    protected $servers;

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
        $this->finder  = new Finder();
        $this->servers = $this->getServers();
        $this->tpl     = $tpl;
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
        foreach ($this->servers as $class) {
            $server = new $class($params, $this->tpl);

            if ($server->checkParameters()) {
                return $server;
            }
        }

        throw new \Exception(_('Unable to create a server'));
    }

    /**
     * Gets a list of available servers with its relative path.
     *
     * @return array The list of available servers.
     */
    protected function getServers() : array
    {
        $directory = realpath(__DIR__ . '/../Server');
        $parsers   = [];

        $files = $this->finder->in($directory)
            ->name('*.php')
            ->notName('Server.php')
            ->notName('Http.php')
            ->files();

        foreach ($files as $file) {
            $parsers[] = sprintf(
                '\Common\NewsAgency\Component\Server\%s',
                str_replace(
                    [ $directory . '/', '.php', '/' ],
                    [ '', '', '\\'],
                    $file->getRealPath()
                )
            );
        }

        uasort($parsers, function ($a, $b) {
            return strlen($a) >= strlen($b) ? -1 : 1;
        });

        return $parsers;
    }
}
