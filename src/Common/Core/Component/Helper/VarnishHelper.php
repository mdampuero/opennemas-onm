<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

use Onm\Varnish\MessageExchanger;
use Common\ORM\Entity\Instance;

class VarnishHelper
{
    /**
     * Initializes the VarnishCacheHelper.
     *
     * @param MesasgeExchanger $varnish The varnish MessageExchanger service.
     */
    public function __construct(MessageExchanger $varnish)
    {
        $this->varnish = $varnish;
    }

    /**
     * Deletes the varnish cache for the current instance.
     */
    public function deleteInstance(Instance $instance)
    {
        $this->varnish->addBanMessage(
            sprintf('obj.http.x-tags ~ instance-%s', $instance->internal_name)
        );
    }
}
