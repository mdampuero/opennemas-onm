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

use Common\Core\Component\Template\Cache\CacheManager;

class TemplateCacheHelper
{
    /**
     * The CacheManager service.
     *
     * @var CacheManager
     */
    protected $cache;

    /**
     * Initializes the TemplateCacheHelper.
     *
     * @param CacheManager $cache The CacheManager service.
     */
    public function __construct(CacheManager $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Deletes cache files for a list of categories.
     */
    public function deleteCategories($categories)
    {
        foreach ($categories as $category) {
            $this->cache->delete(
                'category',
                'list',
                $category->pk_content_category
            );
        }
    }

    /**
     * Deletes cache files for dynamic CSS.
     */
    public function deleteDynamicCss()
    {
        $this->cache->delete('css', 'global');
    }
}
