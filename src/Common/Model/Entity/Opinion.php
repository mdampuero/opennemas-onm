<?php

namespace Common\Model\Entity;

use Opennemas\Orm\Core\Entity;

/**
 * The Opinion class represents an Opennemas opinion.
 */
class Opinion extends Entity
{
    /**
     * The name of the setting to save extra field configuration.
     *
     * @var string
     */
    const EXTRA_INFO_TYPE = 'extraInfoContents.OPINION_MANAGER';
}
