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
use Common\Core\Component\Validator\Validator as CoreValidator;

class TagValidator extends Validator
{
    /**
     * {@inheritdoc}
     */
    public function validate($item)
    {
        $errors = $this->container->get('core.validator')
            ->validate([
                'name' => $item->name
            ], CoreValidator::BLACKLIST_RULESET_TAGS);

        if (!empty($errors)) {
            throw new InvalidArgumentException('Invalid tag', 400);
        }

        if (!$item->exists()) {
            $oql = sprintf(
                'name = "%s" and locale = "%s"',
                $item->name,
                $item->locale
            );

            try {
                $tag = $this->container->get('api.service.tag')->getItemBy($oql);

                // Tags with different names are considered different
                if ($tag->name !== $item->name) {
                    return;
                }
            } catch (\Exception $e) {
                return;
            }

            throw new InvalidArgumentException('Invalid tag', 400);
        }
    }
}
