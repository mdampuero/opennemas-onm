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

class FrontpageService extends OrmService
{
    /**
     * Returns the data (frontpages, vesrions, content positions, contents and vesrion id)
     * used in the frontpage manager given a category id and frontpage version id
     *
     * @param int $categoryId the category id to get contents from
     * @param int $versionId the category id to get contents from
     *
     * @return array
     **/
    public function getDataForCategoryAndVersion($categoryId, $versionId)
    {
        return $this->container->get('api.service.frontpage_version')
            ->getFrontpageData($categoryId, $versionId);
    }

    /**
     * Returns the data (frontpages, vesrions, content positions, contents and version id)
     * used to render a frontpage given category id
     *
     * @param int $categoryId the category id to get contents from
     *
     * @return array
     **/
    public function getCurrentVersionForCategory($categoryId)
    {
        return $this->container->get('api.service.frontpage_version')
            ->getPublicFrontpageData($categoryId);
    }
}
