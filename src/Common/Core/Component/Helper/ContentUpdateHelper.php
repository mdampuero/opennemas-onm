<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Common\Core\Component\Helper;

use Symfony\Component\DependencyInjection\Container;

/**
* Perform searches in Database related with one content
*/
class ContentUpdateHelper
{
    /**
     * The services container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The content service.
     *
     * @var ContentService
     */
    protected $service;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $template;

    /**
     * The entity repository.
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Initializes the ContentHelper.
     *
     * @param Container $container The service container.
     */
    public function __construct($container)
    {
        $this->container          = $container;
        $this->service            = $this->container->get('api.service.content');
        $this->template           = $this->container->get('core.template.frontend');
        $this->entityManager      = $this->container->get('entity_repository');
        $this->cache              = $this->container->get('cache.connection.instance');
        $this->locale             = $this->container->get('core.locale');
    }

    /**
     * Returns the image id for the provided item.
     *
     * @param mixed $item The item to get property from.
     *
     * @return int The image id.
     */
    public function getImage($item = null) : ?int
    {
        if (!is_array($item)) {
            return null;
        }

        return array_key_exists('image_id', $item)
            ? $item['image_id']
            : null;
    }

    /**
     * Returns the body for the provided item.
     *
     * @param mixed $item The item to get property from.
     *
     * @return string The body.
     */
    public function getBody($item = null) : ?string
    {
        if (!is_array($item)) {
            return null;
        }

        return array_key_exists('body', $item)
            ? $item['body']
            : null;
    }

    /**
     * Returns the title for the provided item.
     *
     * @param mixed $item The item to get property from.
     *
     * @return string The title.
     */
    public function getTitle($item = null) : ?string
    {
        if (!is_array($item)) {
            return null;
        }

        return array_key_exists('title', $item)
            ? $item['title']
            : null;
    }

    /**
     * Returns the caption for the provided item.
     *
     * @param mixed $item The item to get property from.
     *
     * @return string The caption.
     */
    public function getCaption($item = null) : ?string
    {
        if (!is_array($item)) {
            return null;
        }

        return array_key_exists('caption', $item)
            ? $item['caption']
            : null;
    }

    /**
     * Returns the modified date for the provided item.
     *
     * @param mixed $item The item to get property from.
     *
     * @return string The modified date.
     */
    public function getModifiedDate($item = null) : ?string
    {
        if (!is_array($item)) {
            return null;
        }

        return array_key_exists('modified', $item)
            ? $item['modified']
            : null;
    }

    /**
     * Returns the modified date for the provided item.
     *
     * @param mixed $item The item to get property from.
     *
     * @return string The modified date.
     */
    public function getModifiedDateTimestamp($item = null) : ?int
    {
        if (!is_array($item)) {
            return null;
        }

        $modified = array_key_exists('modified', $item)
            ? $item['modified']
            : null;

        if ($modified) {
            $modified = strtotime($modified);
        }
        return $modified;
    }

    /**
     * Check if the update has a modified date.
     *
     * @param mixed $item The item to check modified date for.
     *
     * @return bool True if the content has a modified date. False otherwise.
     */
    public function hasModifiedDate($item = null) : bool
    {
        return !empty($this->getModifiedDate($item));
    }

    /**
     * Check if the update has a caption.
     *
     * @param mixed $item The item to check caption for.
     *
     * @return bool True if the content has a caption. False otherwise.
     */
    public function hasCaption($item = null) : bool
    {
        return !empty($this->getCaption($item));
    }

    /**
     * Check if the update has a title.
     *
     * @param mixed $item The item to check body for.
     *
     * @return bool True if the content has a title. False otherwise.
     */
    public function hasTitle($item = null) : bool
    {
        return !empty($this->getTitle($item));
    }

    /**
     * Check if the update has a body.
     *
     * @param mixed $item The item to check body for.
     *
     * @return bool True if the content has a body. False otherwise.
     */
    public function hasBody($item = null) : bool
    {
        return !empty($this->getBody($item));
    }

    /**
     * Check if the update has a body.
     *
     * @param mixed $item The item to check body for.
     *
     * @return bool True if the content has a body. False otherwise.
     */
    public function hasImage($item = null) : bool
    {
        return !empty($this->getImage($item));
    }
}
