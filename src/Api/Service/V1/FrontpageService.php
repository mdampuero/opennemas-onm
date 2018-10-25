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
     * used to render a frontpage given a category id and frontpage version id
     *
     * @param int $categoryId the category id to get contents from
     * @param int $frontpageVersionId the category id to get contents from
     *
     * @return array
     **/
    public function getDataForCategoryAndVersion($categoryId, $versionId)
    {
        return $this->container->get('api.service.frontpage_version')
            ->getFrontpageData($categoryId, $versionId);
    }
}
