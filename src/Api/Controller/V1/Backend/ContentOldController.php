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

class ContentOldController extends ContentController
{
    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.content_old';

    /**
     * Returns a list of extra data.
     *
     * @return array The extra data.
     */
    protected function getExtraData($items = null)
    {
        return [
            'related_contents' => $this->getRelatedContents($items),
            'keys'             => $this->getL10nKeys(),
            'authors'          => $this->getAuthors(),
            'locale'           => $this->get('core.helper.locale')->getConfiguration(),
            'comments_enabled' => $this->get('core.helper.comment')->enableCommentsByDefault(),
            'template_vars'    => [
                'media_dir' => $this->get('core.instance')->getMediaShortPath() . '/images',
            ],
        ];
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
     * Returns the list of contents related with items.
     *
     * @param Content $content The content.
     *
     * @return array The list of photos linked to the content.
     */
    protected function getRelatedContents($items)
    {
        return [];
    }
}
