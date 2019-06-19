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
     * {@inheritdoc}
     */
    protected $service = 'api.service.content_old';

    /**
     * {@inheritdoc}
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
}
