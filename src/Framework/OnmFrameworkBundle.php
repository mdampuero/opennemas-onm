<?php
/**
 * Defines the framework bundle
 *
 * @package Framework
 */
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Framework\DependencyInjection\OpennemasExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Framework\DependencyInjection\Compiler\RemoveSymfonyRouterListenerServicePass;

/**
 * Initializes the OnmFrameworkBundle
 *
 * @package Framework
 */
class OnmFrameworkBundle extends Bundle
{
    /**
     * {@inheritDoc}
     */
    public function getContainerExtension()
    {
        return new OpennemasExtension();
    }

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RemoveSymfonyRouterListenerServicePass());
    }
}
