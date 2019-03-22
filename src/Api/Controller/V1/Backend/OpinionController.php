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

class OpinionController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_opinion_show';

    /**
     * Returns a list of extra data
     *
     * @return array
     **/
    protected function getExtraData($items = null)
    {
        $extra = parent::getExtraData($items);

        $extraFields = null;

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get(\Opinion::EXTRA_INFO_TYPE);
        }

        return array_merge([
            'extra_fields' => $extraFields,
        ], $extra);
    }
}
