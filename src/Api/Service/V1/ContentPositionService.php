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

use Common\ORM\Entity\FrontpageVersion;
use Api\Exception\CreateItemException;

class ContentPositionService extends OrmService
{


    public function getContentPositions($categoryId, $frontpageId)
    {
        $repository = $this->container->get('orm.manager')->getRepository('ContentPosition');
        return $repository->getContentPositions($categoryId, $frontpageId);
    }

    public function getCategoriesWithManualFrontpage()
    {
        $repository = $this->container->get('orm.manager')
            ->getRepository('ContentPosition');
        return $repository->getCategoriesWithManualFrontpage();
    }
}
