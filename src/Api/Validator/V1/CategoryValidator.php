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

                // Check logo size
                if (!empty($category->logo_id)) {
                    $logo = $this->container->get('api.service.photo')
                        ->getItem($category->logo_id);

                    if ($logo->height > 120) {
                        throw new InvalidArgumentException(
                            sprintf(
                                _('The maximum height for the %s is 120px. Please adjust your image size.'),
                                'logo ' . _('of') . ' ' . $category->title
                            ),
                            400
                        );
                    }
                }

                // Update action
                if ($category->id == $item->id) {
                    continue;
                }
            } catch (\Exception $e) {
                throw new InvalidArgumentException($e->getMessage(), $e->getCode());
            }
        }
    }
}
