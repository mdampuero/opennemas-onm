<?php
namespace Framework\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RemoveSymfonyRouterListenerServicePass implements CompilerPassInterface
{
    /**
     * Removes the router_listener from the container
     * This will be replaced by our own router listener.
     *
     * @param ContainerBuilder $container The container builder
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $container->removeDefinition('router_listener');
    }
}
