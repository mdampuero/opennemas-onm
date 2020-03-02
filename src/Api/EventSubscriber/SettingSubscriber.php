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
     * @param Instance $instance The current instance.
     * @param TemplateCacheManager $container The service container.
     * @param CacheManager $th The CacheManager services for template.
     * @param VarnishHelper $vh The VarnishHelper service.
     */
    public function __construct(
        ?Instance $instance,
        CacheManager $th,
        VarnishHelper $vh,
        $container
    ) {
        $this->instance  = $instance;
        $this->template  = $th;
        $this->varnish   = $vh;
        $this->container = $container;
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
        $this->container->get('core.service.assetic.dynamic_css')->deleteTimestamp('%global%');

        if (!$this->container->hasParameter('varnish')) {
            return;
        }

        $this->varnish->deleteInstance($this->instance);
    }
}
