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

class TagService extends OrmService
{

    /**
     * Method to simplificate the tag word for enable a search system
     */
    public function createSearchableWord($word)
    {
        return \Onm\StringUtils::generateSlug($word, false);
    }
}
