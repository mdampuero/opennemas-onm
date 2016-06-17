<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

use Onm\Cache\AbstractCache;
use Onm\Instance\InstanceManager;
use Onm\Exception\InstanceNotFoundException;
use Onm\Exception\InstanceNotRegisteredException;

/**
 * Loads and initializes an instance from the request object.
 */
class ThemeLoaderListener implements EventSubscriberInterface
{
    /**
     * The current theme.
     *
     * @var Theme.
     */
    protected $theme;

    /**
     * Initializes the instance loader.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Loads a theme basing on the request.
     *
     * @param GetResponseEvent $event A GetResponseEvent object.
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $theme = $this->getThemeByUuid('es.openhost.theme.admin');
        $this->container->get('core.template.admin')->addActiveTheme($theme);

        $theme = $this->getThemeByUuid('es.openhost.theme.manager');
        $this->container->get('core.template.manager')->addActiveTheme($theme);

        $this->theme = $this->getActiveTheme();

        if (empty($this->theme)) {
            return;
        }

        $template = $this->container->get('core.template');
        $parents  = $this->getParents($this->theme->uuid);

        $template->addActiveTheme($this->theme);

        foreach ($parents as $uuid) {
            $theme = $this->getThemeByUuid($uuid);

            if (!empty($theme)) {
                $template->addTheme($theme);
            }
        }

        if (empty($this->theme->parameters)) {
            return;
        }

        foreach ($this->theme->parameters as $key => $values) {
            if (method_exists($this, 'load' . $key)) {
                $this->{'load' . $key}($values);
            }
        }
    }

    /**
     * Returns the current theme.
     *
     * @return Instance The current theme.
     */
    public function getTheme()
    {
        return $this->theme;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * @return array The event names to listen to.
     */
    public static function getSubscribedEvents()
    {
        return [
            SymfonyKernelEvents::REQUEST => [ [ 'onKernelRequest', 100 ] ],
        ];
    }

    /**
     * Returns the active theme.
     *
     * @return mixed The active theme if it exists. False otherwise.
     */
    protected function getActiveTheme()
    {
        $instance = $this->container->get('instance');

        return $this->getThemeByUuid($instance->settings['TEMPLATE_USER']);
    }

    /**
     * TODO: Remove when new ORM is merged or search by uuid is allowed in the
     *       theme repository.
     *
     * Returns a theme given an UUID.
     *
     * @param string $uuid The theme UUID.
     *
     * @return mixed The theme if it exists. False otherwise.
     */
    protected function getThemeByUuid($uuid)
    {
        if (empty($this->themes)) {
            $this->themes = $this->container->get('orm.loader')->getPlugins();
        }

        $themes = array_filter($this->themes, function ($a) use ($uuid) {
            $uuid = 'es.openhost.theme.'
                . str_replace('es.openhost.theme.', '', $uuid);

            return $a->uuid === $uuid;
        });

        if (empty($themes)) {
            return false;
        }

        return array_shift($themes);
    }

    /**
     * Returns the list of parents of the current theme.
     *
     * @param Extension $theme The theme UUID.
     *
     * @return array The list of parents.
     */
    protected function getParents($uuid)
    {
        $uuids   = [];
        $parents = [];
        $theme   = $this->getThemeByUuid($uuid);

        if (empty($theme)
            || empty($theme->parameters)
            || !array_key_exists('parent', $theme->parameters)
        ) {
            return $parents;
        }

        foreach ($theme->parameters['parent'] as $parent) {
            $uuids[]   = $parent;
            $parents[] = $parent;
        }

        foreach ($parents as $parent) {
            $uuids = array_merge($uuids, $this->getParents($parent));
        }

        return array_unique($uuids);
    }

    /**
     * Adds advertisement positions defined by theme to the advertisement
     * manager.
     *
     * @param array $positions The list of positions.
     */
    protected function loadAdvertisements($positions)
    {
        $this->container->get('core.manager.advertisement')
            ->addPositions($positions);
    }

    /**
     * Adds layouts defined by theme to the layout manager.
     *
     * @param array $positions The list of positions.
     */
    protected function loadLayouts($layouts)
    {
        $this->container->get('core.manager.layout')->addLayouts($layouts);
    }

    /**
     * Adds menu positions defined by theme to the menu manager.
     *
     * @param array $menus The list of menu positions.
     */
    protected function loadMenus($menus)
    {
        $this->container->get('core.manager.menu')->addMenus($menus);
    }
}
