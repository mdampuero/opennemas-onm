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

class EventController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_event_show';

    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.content';

    /**
     * Returns a list of extra data
     *
     * @return array
     **/
    protected function getExtraData($items = null)
    {
        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1 and fk_content_category=0');

        $categories = array_filter($categories, function ($category) use ($security) {
            return $security->hasCategory($category->pk_content_category);
        });

        $extra = [
            'tags'       => [],
            'categories' => $converter->responsify($categories),
        ];

        return array_merge($extra, $this->getLocaleData('frontend'));
    }
}
