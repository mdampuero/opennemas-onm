<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            // Base symfony deps
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            // new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),

            // Opennemas third-party deps
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new CometCult\BraintreeBundle\CometCultBraintreeBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle(),
            new Http\HttplugBundle\HttplugBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),

            // Opennemas internal bundles
            new Api\ApiBundle(),
            new Backend\BackendBundle(),
            new BackendWebService\BackendWebServiceBundle(),
            new Framework\OnmFrameworkBundle(),
            new Frontend\FrontendBundle(),
            new FrontendMobile\FrontendMobileBundle(),
            new Manager\ManagerBundle(),
            new ManagerWebService\ManagerWebServiceBundle(),
            new WebService\WebServiceBundle(),
            new Common\Cache\CacheBundle(),
            new Common\Core\CoreBundle(),
            new Common\Data\DataBundle(),
            new Common\Migration\MigrationBundle(),
            new Common\ORM\OrmBundle(),
            new Common\External\ActOn\ActOnBundle(),
        ];

        if (in_array($this->getEnvironment(), [ 'dev', 'test' ], true)) {
            $bundles[] = new Symfony\Bundle\DebugBundle\DebugBundle();
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'Opennemas';
    }

    public function getRootDir()
    {
        return __DIR__;
    }

    public function getCacheDir()
    {
        return dirname(__DIR__) . '/tmp/cache/' . $this->getEnvironment();
    }

    public function getLogDir()
    {
        return dirname(__DIR__) . '/tmp/logs';
    }

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir() . '/config/config_' . $this->getEnvironment() . '.yml');
    }
}
