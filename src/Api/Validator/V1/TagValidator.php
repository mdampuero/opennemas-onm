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
            throw new InvalidArgumentException(array_pop($errors['errors']), 400);
        }

        $locales = $this->container->get('core.locale')
            ->getAvailableLocales('frontend');

        // Locale invalid
        if (!empty($item->locale)
            && !in_array($item->locale, array_keys($locales))
        ) {
            throw new InvalidArgumentException(_('The selected locale is invalid'), 400);
        }

        $oql = sprintf('(name = "%s" or slug = "%s")', $item->name, $item->slug);

        if ($item->id && is_numeric($item->id)) {
            $oql .= sprintf(' and id != %d', $item->id);
        }

        $oql .= (!empty($item->locale))
            ? sprintf(' and locale = "%s"', $item->locale)
            : ' and locale is null';

        try {
            $tags = $this->container->get('api.service.tag')->getList($oql);

            if ($tags['total'] > 0) {
                throw new \Exception();
            }
        } catch (\Exception $e) {
            throw new InvalidArgumentException(_('There is another tag with the same name or slug'), 409);
        }
    }
}
