<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

class CacheManagerController extends Controller
{
    /**
     * Displays the list of actions to execute.
     *
     * @return Response The response object.
     */
    public function indexAction()
    {
        $hasRedis = method_exists($this->get('cache'), 'getRedis');

        return $this->render('cache_manager/index.tpl', [ 'redis_enabled' => $hasRedis ]);
    }

    /**
     * Show the configuration form and stores its information.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CACHE_MANAGER')
     *     and hasPermission('CACHE_TPL_ADMIN')")
     */
    public function configAction(Request $request)
    {
        // Init template cache config manager with frontend user template
        $frontpageTemplate = $this->get('view')->getTemplate();
        $configDir         = $frontpageTemplate ->config_dir[0];
        $configContainer   = $this->get('template_cache_config_manager');
        $configManager     = $configContainer->setConfigDir($configDir);

        // If the request is post then save the configuration with the data provided
        if ($this->request->getMethod() == 'POST') {
            $config              = [];
            $cacheGroups         = $request->request->get('groups');
            $cacheGroupsEnabled  = $request->request->get('enabled');
            $cacheGroupsLifeTime = $request->request->get('lifetime');

            foreach ($cacheGroups as $section) {
                $caching  = isset($cacheGroupsEnabled[$section]) ? 1 : 0;
                $lifetime = intval($cacheGroupsLifeTime[$section]);

                $config[$section] = [
                    'caching'        => $caching,
                    'cache_lifetime' => $lifetime,
                ];
            }

            // Save changes on file
            $saved = $configManager->save($config);

            $flashBag = $this->get('session')->getFlashBag();
            if ($saved) {
                $flashBag->add('success', 'Cache configuration saved successfully.');
            } else {
                $flashBag->add('error', 'Unable to save the cache configuration.');
            }

            return $this->redirect($this->generateUrl('admin_cache_manager_config'));
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
     * Clears all caches.
     *
     * @return Response The response object.
     */
    public function clearAllCacheAction()
    {
        $this->clearSmartyCache();
        $this->clearSmartyCompiles();
        $this->clearRedis();
        $this->clearVarnishCache();

        $this->get('session')->getFlashBag()
            ->add('success', 'Cleared all cache for the instance (smarty compiles, smarty cache, redis and varnish).');

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }

    /**
     * Deletes all the frontend cache files.
     *
     * DANGER: this action is really CPU expensive.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CACHE_MANAGER')
     *     and hasPermission('CACHE_TPL_ADMIN')")
     */
    public function clearCacheAction()
    {
        $this->clearSmartyCache();

        $this->get('session')->getFlashBag()
            ->add('success', 'Smarty cache removed for the instance.');

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }

    /**
     * Deletes all the frontend cache files
     * DANGER: this action is really CPU expensive
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CACHE_MANAGER')
     *     and hasPermission('CACHE_TPL_ADMIN')")
     */
    public function clearCompiledTemplatesAction()
    {
        $this->clearSmartyCompiles();

        $this->get('session')->getFlashBag()
            ->add('success', 'Smarty compiled templates removed for the instance.');

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }

    /**
     * Deletes all varnish cache
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('CACHE_MANAGER')
     *     and hasPermission('CACHE_TPL_ADMIN')")
     */
    public function clearVarnishCacheAction()
    {
        // Initialization of the frontend template object
        if (!$this->container->hasParameter('varnish')) {
            return false;
        }

        $this->clearVarnishCache();

        $this->get('session')->getFlashBag()
            ->add('success', 'Varnish BAN queued for current instance.');

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }

    /**
     * Deletes the redis cache for the current instance.
     *
     * @return Response The response object.
     */
    public function clearRedisCacheForInstanceAction()
    {
        $this->clearRedis();
        $this->get('session')->getFlashBag()
            ->add('success', 'Redis cache cleared for current instance.');

        return $this->redirect($this->generateUrl('admin_cache_manager'));
    }

    /**
     * Removes the redis cache for the current instance.
     */
    private function clearRedis()
    {
        return $this->get('cache.manager')->getConnection('instance')->removeByPattern(
            '*' . $this->get('core.instance')->internal_name . '*'
        );
    }

    /**
     * Sends a BAN to varnish to purge all the cache for the current instance.
     */
    private function clearVarnishCache()
    {
        $instanceName = $this->get('core.instance')->internal_name;

        $this->container->get('varnish_ban_message_exchanger')
            ->addBanMessage(sprintf('obj.http.x-tags ~ instance-%s', $instanceName));
    }

    /**
     * Removes all the smarty cache for the current instance.
     */
    public function clearSmartyCache()
    {
        $frontpageTemplate = $this->get('view')->getTemplate();
        $frontpageTemplate->clearAllCache();
    }

    /**
     * Removes all the smarty compiles for the current instance.
     */
    public function clearSmartyCompiles()
    {
        $frontpageTemplate = $this->get('view')->getTemplate();
        $frontpageTemplate->clearCompiledTemplate();
    }
}
