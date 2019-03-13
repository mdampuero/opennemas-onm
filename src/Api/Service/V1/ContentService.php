<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

class ContentService extends OrmService
{
    /**
     * Returns a category basing on a slug.
     *
     * @param string $slug The category slug.
     *
     * @return Category The category.
     */
    public function getItemBySlug($slug)
    {
        $oql = sprintf('slug regexp "(.+\"|^)%s(\".+|$)"', $slug);

        return $this->getItemBy($oql);
    }
}
