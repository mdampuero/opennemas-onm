<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

class EventController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'es.openhost.module.events';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => null,
        'list'   => null,
        'show'   => null
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'event';

    /**
     * Returns a list of extra data to use in  the create/edit item form
     *
     * @return array
     **/
    private function getExtraData()
    {
        $extra = [];

        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1'); // review this filter to search for commen and specific for kiosko

        $categories = array_filter($categories, function ($a) use ($security) {
            return $security->hasCategory($a->pk_content_category);
        });

        $extra['categories'] = $converter->responsify($categories);
        array_unshift($extra['categories'], [
            'pk_content_category' => null,
            'title'               => _('Select a category...')
        ]);

        return $extra;
    }
}
