<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Validator\V1;

use Api\Exception\InvalidArgumentException;
use Api\Validator\Validator;

class SubscriberValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        if (!array_key_exists('type', $item->getChanges())) {
            return;
        }

        if ($item->type > 2
            || ($item->type === 2
            && !$this->container->get('core.security')->hasPermission('MASTER'))
        ) {
            throw new InvalidArgumentException('Invalid value for "type"', 400);
        }
    }
}
