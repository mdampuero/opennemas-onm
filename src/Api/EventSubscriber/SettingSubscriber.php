<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\EventSubscriber;

use Common\Core\Component\Template\Cache\CacheManager;
use Common\Core\Component\Helper\VarnishHelper;
use Common\Orm\Entity\Instance;

use Framework\Component\Assetic\DynamicCssService;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SettingSubscriber implements EventSubscriberInterface
{

    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * Initializes the SettingSubscriber
     *
     * @param Instance $instance          The current instance.
     * @param CacheManager $th            The CacheManager services for template.
     * @param VarnishHelper $vh           The VarnishHelper service.
     * @param DynamicCssService $dcs      The DynamicCssService.
     */
    public function __construct(
        ?Instance $instance,
        CacheManager $th,
        VarnishHelper $vh,
        DynamicCssService $dcs
    ) {
        $this->instance = $instance;
        $this->template = $th;
        $this->varnish  = $vh;
        $this->dcs      = $dcs;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            'setting.update' => [ [ 'onSettingUpdate' ] ]
        ];
    }

    /**
     * Removes smarty, varnish and dynamic css caches when settings are updated
     */
    public function onSettingUpdate()
    {
        $this->template->deleteAll();
        $this->dcs->deleteTimestamp('%global%');
        $this->varnish->deleteInstance($this->instance);
    }
}
