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

use Common\Core\Component\Helper\UrlGeneratorHelper;
use Common\ORM\Entity\Instance;
use Onm\Varnish\MessageExchanger;

class VarnishHelper
{
    /**
     * The UrlGeneratorHelper service.
     *
     * @var UrlGeneratorHelper
     */
    protected $uh;

    /**
     * The varnish service.
     *
     * @var MessageExchanger
     */
    protected $varnish;

    /**
     * Initializes the VarnishCacheHelper.
     *
     * @param MesasgeExchanger $varnish The varnish MessageExchanger service.
     */
    public function __construct(UrlGeneratorHelper $uh, MessageExchanger $varnish)
    {
        $this->uh      = $uh;
        $this->varnish = $varnish;
    }

    /**
     * Delete a list of files from varnish cache.
     *
     * @param array $files The list of files to delete from varnish cache.
     */
    public function deleteFiles(array $files)
    {
        foreach ($files as $file) {
            $this->varnish->addBanMessage(
                sprintf('req.url ~ %s', $this->uh->generate($file))
            );
        }
    }

    /**
     * Deletes the varnish cache for the current instance.
     *
     * @param Instance $instance The instance to delete varnish for.
     */
    public function deleteInstance(Instance $instance)
    {
        $this->varnish->addBanMessage(
            sprintf('obj.http.x-tags ~ instance-%s', $instance->internal_name)
        );
    }
}
