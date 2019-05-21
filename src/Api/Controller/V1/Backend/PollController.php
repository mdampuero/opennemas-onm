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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PollController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'POLL_MANAGER';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_poll_show';

    /**
     * {@inheritDoc}
     */
    public function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'tags'       => $this->getTags($items),
            'categories' => $this->getCategories($items)
        ]);
    }

    /**
     * {@inheritDoc}`
     */
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('album');
    }
}
