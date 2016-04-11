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
 * The Content class represents an Opennemas content.
 */
class Instance extends Entity
{
    /**
     * Returns the instance Client object.
     *
     * @return Client The client.
     */
    public function getClient()
    {
        if (!array_key_exists('client', $this->metas)) {
            return null;
        }

        if (is_array($this->metas['client'])) {
            return $this->metas['client'];
        }

        return $this->metas['client'];
    }

    /**
     * Returns the database name.
     *
     * @return string The database name.
     */
    public function getDatabaseName()
    {
        if (array_key_exists('BD_DATABASE', $this->settings)) {
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
        if ($this->main_domain && $this->main_domain > 0) {
            $domain = $this->domains[$this->main_domain - 1];
        } elseif (is_array($this->domains) && !empty($this->domains)) {
            $domain = $this->domains[0];
        } else {
            $domain = null;
        }

        return $domain;
    }
}
