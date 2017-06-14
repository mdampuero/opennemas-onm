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
     */
    public function addPositions($positions, $themeName)
    {
        if (!is_array($positions)) {
            return $this;
        }

        foreach ($positions as $data) {
            $data['custom'] = true;
            $data['theme'] = $themeName;
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
     *
     * @return array The list of positions for a group.
     */
    public function getPositionsForGroup($groupName = null, $positions = [])
    {
        $groupPositions = [];
        if (!is_null($groupName)) {
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

        return $groupPositions;
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
     * Checks if SafeFrame is enabled.
     *
     * @return boolean True if SafeFrame is enabled. False otherwise.
     */
    public function isSafeFrameEnabled()
    {
        $settings = $this->container->get('setting_repository')
            ->get('ads_settings');

        if (is_array($settings)
            && array_key_exists('safe_frame', $settings)
            && $settings['safe_frame']
        ) {
            return true;
        }

        return false;
    }
}
