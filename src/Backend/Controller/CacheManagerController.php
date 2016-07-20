<?php
/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the system information
 *
 * @package Backend_Controllers
 **/
class CacheManagerController extends Controller
{

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function indexAction(Request $request)
    {
        return $this->render('cache_manager/index.tpl');

    }
    /**
     * Show the configuration form and stores its information
     *
     * @param Request $request the request object
     *
     * @return string the string response
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function configAction(Request $request)
    {
        // Init template cache config manager with frontend user template
        $frontpageTemplate = $this->get('core.template');
        $configDir         = $frontpageTemplate ->config_dir[0];
        $configContainer   = $this->container->get('template_cache_config_manager');
        $configManager     = $configContainer->setConfigDir($configDir);

        // If the request is post then save the configuration with the data provided
        if ($this->request->getMethod() == 'POST') {
            $config = array();
            $cacheGroups         = $request->request->get('groups');
            $cacheGroupsEnabled  = $request->request->get('enabled');
            $cacheGroupsLifeTime = $request->request->get('lifetime');

            foreach ($cacheGroups as $section) {
                $caching  = (isset($cacheGroupsEnabled[$section]))? 1: 0;
                $lifetime = intval($cacheGroupsLifeTime[$section]);

                $config[$section] = array(
                    'caching'        => $caching,
                    'cache_lifetime' => $lifetime,
                );
            }

            // Save changes on file
            $saved = $configManager->save($config);

            $flashBag = $this->get('session')->getFlashBag();
            if ($saved) {
                $flashBag->add('success', _('Cache configuration saved successfully.'));
            } else {
                $flashBag->add('error', _('Unable to save the cache configuration.'));
            }

            return $this->redirect($this->generateUrl('admin_cache_manager'));
        } else {
            // Load cache manager config and show the form with that info
            $config = $configManager->load();

            return $this->render(
                'cache_manager/config.tpl',
                ['config' => $config]
            );
        }
    }

    /**
     * Deletes all the frontend cache files
     * DANGER: this action is really CPU expensive
     *
     * @return string the result string
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function clearCacheAction()
    {
        // Initialization of the frontend template object
        $frontpageTemplate = $this->get('core.template');
        $frontpageTemplate->clearAllCache();

        $this->get('session')->getFlashBag()
            ->add('success', _('Smarty cache removed for the instance.'));

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }

    /**
     * Deletes all the frontend cache files
     * DANGER: this action is really CPU expensive
     *
     * @return string the result string
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function clearCompiledTemplatesAction()
    {
        // Initialization of the frontend template object
        $frontpageTemplate = $this->get('core.template');
        $frontpageTemplate->clearCompiledTemplate();

        $this->get('session')->getFlashBag()
            ->add('success', _('Smarty compiled templates removed for the instance.'));

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }

    /**
     * Deletes all varnish cache
     *
     * @return string the result string
     *
     * @Security("has_role('CACHE_TPL_ADMIN')")
     *
     * @CheckModuleAccess(module="CACHE_MANAGER")
     **/
    public function clearVarnishCacheAction()
    {
        // Initialization of the frontend template object
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $instanceName = $this->get('core.instance')->internal_name;

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s', $instanceName));

        $this->get('session')->getFlashBag()
            ->add('success', _('Varnish BAN queued for current instance.'));

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }
}
