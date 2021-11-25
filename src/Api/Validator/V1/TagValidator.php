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
            throw new InvalidArgumentException(_('Invalid tag'), 400);
        }

        if ($item->exists()) {
            return;
        }

        $locales = $this->container->get('core.locale')
            ->getAvailableLocales('frontend');

        // Locale invalid
        if (!empty($item->locale)
            && !in_array($item->locale, array_keys($locales))
        ) {
            throw new InvalidArgumentException(_('Invalid tag'), 400);
        }

        $oql = ( $item->id && is_numeric($item->id) ) ?
            sprintf(
                '(( name = "%s" and id != "%d" ) or ( slug = "%s" and id != "%d" ))',
                $item->name,
                $item->id,
                $item->slug,
                $item->id,
            ) : sprintf(
                '(name = "%s" or slug = "%s")',
                $item->name,
                $item->slug
            );
        $oql .= (!empty($item->locale)) ?
            sprintf(' and locale = "%s"', $item->locale) :
            ' and locale is null';

        try {
            $tag = $this->container->get('api.service.tag')->getItemBy($oql);

            // Tags with different names are considered different
            if ($tag->name !== $item->name && $tag->slug !== $item->slug) {
                return;
            }
        } catch (\Exception $e) {
            return;
        }

        throw new InvalidArgumentException(_('Invalid tag'), 400);
    }
}
