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
        $bundles = array(
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
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'), true)) {
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

    /**
     * {@inheritdoc}
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->getRootDir().'/config/config_'.$this->getEnvironment().'.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheDir()
    {
        return realpath($this->getRootDir().'/../tmp/cache').'/'.$this->getEnvironment();
    }

    /**
     * {@inheritdoc}
     */
    public function getLogDir()
    {
        return realpath($this->getRootDir().'/../tmp/logs');
    }
}
