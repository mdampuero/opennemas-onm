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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

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
        return [
            'authors'          => $this->getAuthors(),
            'comments_enabled' => $this->get('core.helper.comment')->enableCommentsByDefault(),
            'keys'             => $this->getL10nKeys(),
            'locale'           => $this->get('core.helper.locale')->getConfiguration(),
            'paths'            => [
                'photo'      => $this->get('core.instance')->getImagesShortPath(),
                'attachment' => $this->get('core.instance')->getFilesShortPath(),
                'newsstand'  => $this->get('core.instance')->getNewsstandShortPath(),
            ],
            'related_contents' => $this->getRelatedContents($items),
        ];
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
     * Returns the list of photos linked to the article.
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
