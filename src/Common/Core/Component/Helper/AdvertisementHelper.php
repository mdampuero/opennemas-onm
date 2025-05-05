<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class AdvertisementHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Array with all ads positions.
     *
     * @var array
     */
    protected $positions = [];

    /**
     * The advertisement group name for a page.
     *
     * @var string
     */
    protected $group;


    /**
     * Initializes the AdvertisementHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->conn      = $container->get('orm.connection.instance');
    }

    /**
     * Add new advertisement position.
     *
     * @param array $positions Positions to add.
     * @param string $themeName The theme name.
     *
     * @return AdvertisementHelper
     */
    public function addPositions($positions, $themeName)
    {
        if (!is_array($positions)) {
            return $this;
        }

        foreach ($positions as $data) {
            $data['custom'] = true;
            $data['theme']  = $themeName;

            $this->positions[$data['position']] = $data;
        }

        return $this;
    }

    /**
     * Returns the name for an advertisement given its position.
     *
     * @param integer $id The advertisement position.
     *
     * @return string The advertisement name.
     */
    public function getAdvertisementName($id)
    {
        return $this->positions[$id]['name'];
    }

    /**
     * Returns the list of positions.
     *
     * @return array The list of positions.
     */
    public function getPositions()
    {
        return $this->positions;
    }

    /**
     * Returns the list of positions for the current theme.
     *
     * @return array The list of positions for the current theme.
     */
    public function getPositionsForTheme()
    {
        $ads = [];
        foreach ($this->positions as $key => $value) {
            if (array_key_exists('custom', $value)) {
                $ads[$key] = $value;
            }
        }

        return $ads;
    }

    /**
     * Returns the list of positions for a group.
     *
     * @param string $groupName The name of a group.
     * @param array $positions The list of positions.
     *
     * @return array The list of positions for a group.
     */
    public function getPositionsForGroup($groupName = null, $positions = [])
    {
        $groupPositions = [];
        if (!is_null($groupName)) {
            $this->group = $groupName; // Set ads group name
            // Get group positions
            foreach ($this->positions as $key => $value) {
                if ($value['group'] == $groupName) {
                    $groupPositions[] = $key;
                }
            }
        }

        // Add more positions if exists
        if (!empty($positions)) {
            foreach ($positions as $key => $value) {
                $groupPositions[] = $value;
            }
        }

        return array_unique($groupPositions);
    }

    /**
     * Returns the list of names for advertisement positions.
     *
     * @return array The list of names.
     */
    public function getPositionNames()
    {
        $adsNames = [];
        foreach ($this->positions as $key => $value) {
            $adsNames[$key] = $value['name'];
        }

        return $adsNames;
    }

    /**
     * Returns the advertisement group.
     *
     * @return string The advertisement group name.
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Checks if SafeFrame is enabled.
     *
     * @return boolean True if SafeFrame is enabled. False otherwise.
     */
    public function isSafeFrameEnabled()
    {
        $settings = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('ads_settings');

        if (is_array($settings)
            && array_key_exists('safe_frame', $settings)
            && $settings['safe_frame']
        ) {
            return true;
        }

        return false;
    }

    public function getAdsTxtFromManager($skipMaster = false)
    {
        if (!$this->container->get('core.security')->hasPermission('MASTER') && !$skipMaster) {
            return [];
        }

        $oql = sprintf(
            'instances ~ "%s" or instances ~ "Todos"',
            $this->container->get('core.instance')->internal_name
        );
        $ads = $this->container->get('orm.manager')->getRepository('Ads')
            ->findBy($oql);

        return $ads;
    }

    public function getAdsTxtContent()
    {
        $adsTxt = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('ads_txt');

        $adsContainers = $this->getAdsTxtFromManager(true);
        $adsLines      = [];

        if (!empty($adsContainers) && is_array($adsContainers)) {
            foreach ($adsContainers as $container) {
                $containerLines = explode("\n", $container->ads_lines);
                foreach ($containerLines as $line) {
                    $adsLines[] = str_replace(' ', '', $line);
                }
            }
        }

        $containerLines = explode("\n", $adsTxt);
        foreach ($containerLines as $line) {
            $adsLines[] = str_replace(' ', '', $line);
        }

        return implode("\n", array_unique($adsLines));
    }


    /**
     * Returns the list of ads on the manager with their position.
     *
     * @return array The list of ads with their position.
     */
    public function getAdsManagerWithPosition()
    {
        $positions = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('adsposition');

        $ads = $this->getAdsTxtFromManager();

        if (!$ads || !is_array($ads)) {
            return [];
        }

        foreach ($ads as $ad) {
            $positions[$ad->id] = $positions[$ad->id] ?? 0;
            $ad->position       = $positions[$ad->id];
        }

        usort($ads, function ($a, $b) {
            return $a->position <=> $b->position;
        });

        return $ads;
    }

    /**
     * Checks if request url is ads restricted.
     *
     * @return boolean True if url is restricted. False otherwise.
     */
    public function isRestricted($url)
    {
        $restrictedUrls = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('restricted_urls');

        if (empty($restrictedUrls)) {
            return false;
        }

        $escapedChars = ['.', '&', '?'];
        $replaceChars = array_map(function ($e) {
            return "\\" . $e;
        }, $escapedChars);

        $search = str_replace($escapedChars, $replaceChars, $url);

        return preg_match('@' . $search . '/?($|\r?\n)@', $restrictedUrls);
    }
}
