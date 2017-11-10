<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\ORM\Entity;

use Common\ORM\Core\Entity;

/**
 * The Instance class represents an Opennemas newspaper.
 */
class Instance extends Entity
{
    /**
     * Returns the instance client id.
     *
     * @return integer The client id.
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the database name.
     *
     * @return string The database name.
     */
    public function getDatabaseName()
    {
        if (!empty($this->settings)
            && array_key_exists('BD_DATABASE', $this->settings)
        ) {
            return $this->settings['BD_DATABASE'];
        }

        return null;
    }

    /**
     * Returns the instance main domain.
     *
     * @return string The instance main domain.
     */
    public function getMainDomain()
    {
        if (!is_array($this->domains) || empty($this->domains)) {
            return null;
        }

        $index = $this->main_domain;

        if ($index > 0 && $index <= count($this->domains)) {
            return $this->domains[$index - 1];
        }

        return $this->domains[0];
    }

    /**
     * Returns the relative path for the instance media files.
     *
     * @return string The relative path for the instance media files.
     */
    public function getMediaShortPath()
    {
        return "/media/{$this->internal_name}";
    }

    /**
     * Checks if the current instance has multilanguage enabled.
     *
     * @return boolean True if the instance has multilanguage enabled. False
     *                 otherwise.
     */
    public function hasMultilanguage()
    {
        return in_array(
            'es.openhost.module.multilanguage',
            $this->activated_modules
        );
    }
}
