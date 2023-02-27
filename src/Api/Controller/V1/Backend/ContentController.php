<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Controller\V1\ApiController;

class ContentController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.content';

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $instance = $this->get('core.instance');

        if (is_array($items) && !empty($items)) {
            $itemsId = array_map(function ($item) {
                return $item->id;
            }, $items);

            $views = $this->get('content_views_repository')->getViews($itemsId);
        }

        return [
            'authors'          => $this->getAuthors(),
            'comments_enabled' => $this->get('core.helper.comment')->enableCommentsByDefault(),
            'keys'             => $this->getL10nKeys(),
            'locale'           => $this->get('core.helper.locale')->getConfiguration(),
            'paths'            => [
                'photo'      => $instance->getImagesShortPath(),
                'attachment' => $instance->getFilesShortPath(),
                'newsstand'  => $instance->getNewsstandShortPath(),
            ],
            'related_contents' => $this->getRelatedContents($items),
            'base_url'         => $instance->getBaseUrl(true),
            'views'            => $views ?? []
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getTags($items = null)
    {
        $config = $this->get('core.helper.locale')->getConfiguration();
        $tags   = parent::getTags($items);

        if (!$config['multilanguage']) {
            return $tags;
        }

        $locales = array_keys(
            $this->get('core.helper.locale')->getConfiguration()['available']
        );

        $tagsByLocale = [];

        foreach ($locales as $locale) {
            $tagsByLocale[$locale] = [];
            foreach ($tags as $tag) {
                if (empty($tag['locale']) || $tag['locale'] === $locale) {
                    array_push($tagsByLocale[$locale], $tag);
                }
            }
        }

        return $tagsByLocale;
    }

    /**
     * {@inheritdoc}
     */
    protected function getItemId($item)
    {
        return $item->pk_content;
    }

    /**
     * Returns the list of l10n keys.
     *
     * @return array The list of l10n keys.
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys();
    }

    /**
     * Returns the list of related contents of the item.
     *
     * @param Content $content The content.
     *
     * @return array The related contents of the item.
     */
    protected function getRelatedContents($content)
    {
        $extra = [];

        if (empty($content)) {
            return $extra;
        }

        if (is_object($content)) {
            $content = [ $content ];
        }

        foreach ($content as $element) {
            if (!is_array($element->related_contents)) {
                continue;
            }

            foreach ($element->related_contents as $relation) {
                try {
                    $er      = $this->container->get('entity_repository');
                    $content = $er->find($relation['content_type_name'], $relation['target_id']);

                    $extra[$relation['target_id']] = in_array($relation['content_type_name'], $er::ORM_CONTENT_TYPES) ?
                            $this->container->get('api.service.content')->responsify($content) :
                            $content;
                } catch (GetItemException $e) {
                }
            }
        }

        return $extra;
    }
}
