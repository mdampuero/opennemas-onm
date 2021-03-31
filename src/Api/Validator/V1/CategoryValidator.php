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
        $names = $item->name;
        if (!is_array($names)) {
            $names = [ $names ];
        }

        foreach ($names as $name) {
            try {
                $category = $this->container->get('api.service.category')
                    ->getItemBySlug($name);

                // Update action
                if ($category->id == $item->id) {
                    continue;
                }
            } catch (\Exception $e) {
                continue;
            }

            throw new InvalidArgumentException(_('Invalid category'), 400);
        }
    }
}
