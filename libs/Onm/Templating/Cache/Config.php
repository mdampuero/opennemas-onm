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
     * Default cache values.
     *
     * @var array
     */
    protected $default = [
        'frontpages' => [
            'caching'        => 1,
            'cache_lifetime' => 600
        ],
        'frontpage-mobile'   => [
            'caching'        => 1,
            'cache_lifetime' => 600
        ],
        'articles' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'articles-mobile' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'opinion' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'video' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'video-inner' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'gallery-frontpage' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'gallery-inner' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'poll-frontpage' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'poll-inner' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'letter-frontpage' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'letter-inner' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'kiosko' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'newslibrary' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'specials' => [
            'caching'        => 1,
            'cache_lifetime' => 86400
        ],
        'sitemap' => [
            'caching'        => 1,
            'cache_lifetime' => 300
        ],
        'rss' => [
            'caching'        => 1,
            'cache_lifetime' => 600
        ]
    ];

    /**
     * Initializes the Config class
     *
     * @param string $configDir the config dir where the config.ini file is located in
     *
     * @return void
     */
    public function __construct($configDir = '')
    {
        $this->setConfigDir($configDir);
    }

    /**
     * Sets a the directory where the config.ini file is located in
     *
     * @param string $configDir the directory
     *
     * @return Config the class
     */
    public function setConfigDir($configDir = '')
    {
        $this->configDir = $configDir;

        return $this;
    }

    /**
     * Returns the parsed cache configuration file
     *
     * @return array the smarty cache configuration
     */
    public function load()
    {
        $filename = $this->configDir . 'cache.conf';

        $config = parse_ini_file($filename, true);

        $groups = [
            'frontpages' => [
                'name'  => ('Frontpage'),
                'icon' => 'frontpage.png',
            ],
            'frontpage-mobile' => [
                'name'  => _('Frontpage mobile version'),
                'icon' => 'mobile.png',
            ],
            'articles' => [
                'name'  => _('Inner Article'),
                'icon' => 'article.png',
            ],
            'articles-mobile' => [
                'name'  => _('Inner Article mobile version'),
                'icon' => 'mobile.png',
            ],
            'opinion' => [
                'name'  => _('Inner Opinion'),
                'icon' => 'opinion.png',
            ],
            'rss' => [
                'name'  => _('RSS'),
                'icon' => 'rss.png',
            ],
            'sitemap' => [
                'name'  => ('Sitemap'),
                'icon' => 'sitemap.png',
            ],
            'video' => [
                'name'  => ('Frontpage videos'),
                'icon' => 'video.png',
            ],
            'video-inner' => [
                'name'  => ('Inner video'),
                'icon' => 'video.png',
            ],
            'gallery-frontpage' => [
                'name'  => ('Gallery frontpage'),
                'icon' => 'album.png',
            ],
            'gallery-inner' => [
                'name'  => ('Gallery Inner'),
                'icon' => 'album.png',
            ],
            'kiosko' => [
                'name'  => ('Kiosko'),
                'icon' => 'kiosko.png',
            ],
            'letter-frontpage' => [
                'name'  => ('Letter frontpage'),
                'icon' => 'letter.png',
            ],
            'letter-inner' => [
                'name'  => ('Letter inner'),
                'icon' => 'letter.png',
            ],
            'newslibrary' => [
                'name'  => ('Newslibrary'),
                'icon' => 'newslibrary.png',
            ],
            'poll-frontpage' => [
                'name'  => ('Polls frontpage'),
                'icon' => 'poll.png',
            ],
            'poll-inner' => [
                'name'  => ('Poll inner'),
                'icon' => 'poll.png',
            ],
        ];

        $completeGroups = array_merge_recursive($config, $groups);

        foreach ($completeGroups as &$value) {
            $value['caching'] = (int) $value['caching'];
            $value['cache_lifetime'] = (int) $value['cache_lifetime'];
        }

        return $completeGroups;
    }

    /**
     * Saves the smarty configuration to the configuration file
     *
     * @param array $config the configuration to save
     *
     * @return boolean
     */
    public function save($config)
    {
        $filename = $this->configDir . '/cache.conf';

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

    /**
     * Saves the default cache configuration.
     *
     * @return void
     */
    public function saveDefault()
    {
        $this->save($this->default);
    }
}
