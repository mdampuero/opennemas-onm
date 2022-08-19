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

class CategoryValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        // Check logo size
        if (!empty($item->logo_id)) {
            $logo = $this->container->get('api.service.photo')
                ->getItem($item->logo_id);

            // if ($logo->height > 120) {
            //     throw new InvalidArgumentException(
            //         sprintf(
            //             _('The maximum height for the %s is 120px. Please adjust your image size.'),
            //             'logo ' . _('of') . ' ' . $item->title
            //         ),
            //         400
            //     );
            // }
        }

        try {
            $category = $this->container->get('api.service.category')
                ->getItemBySlug($item->name);

            // Update action
            if ($category->id === $item->id) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        throw new InvalidArgumentException(_('Invalid category'), 400);
    }
}
