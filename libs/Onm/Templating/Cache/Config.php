<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Onm\Templating\Cache;

/**
 * Handles all the events after content updates
 */
class Config
{
    /**
     * Initializes the Config class
     *
     * @param string $configDir the config dir where the config.ini file is located in
     *
     * @return void
     **/
    public function __construct($configDir = '')
    {
        $this->setConfigDir($configDir);
    }

    /**
     * Sets a the directory where the config.ini file is located in
     *
     * @param string $configDir the directory
     *
     * @return Config the clas
     **/
    public function setConfigDir($configDir = '')
    {
        $this->configDir = $configDir;

        return $this;
    }

    /**
     * Returns the parsed cache configuration file
     *
     * @return array the smarty cache configuration
     **/
    public function load()
    {
        $filename = $this->configDir . 'cache.conf';

        return parse_ini_file($filename, true);
    }

    /**
     * Saves the smarty configuration to the configuration file
     *
     * @param array $config the configuration to save
     *
     * @return void
     **/
    public function save($config)
    {
        $filename = $this->configDir . 'cache.conf';
        $fp = @fopen($filename, 'w');

        $iniContents = '';

        foreach ($config as $section => $entry) {
            $iniContents .= '[' . $section . ']' . "\n"
                         .'caching = ' . $entry['caching'] . "\n"
                         .'cache_lifetime = '.$entry['cache_lifetime']."\n\n";
        }

        $saved = file_put_contents($filename, $iniContents);

        if ($saved) {
            return true;
        } else {
            return false;
        }
    }
}
