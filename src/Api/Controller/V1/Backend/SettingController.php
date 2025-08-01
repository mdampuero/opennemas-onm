<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;

/**
 * Displays and saves system settings.
 */
class SettingController extends Controller
{
    /**
     * The list of settings that must be base64 encoded/decoded.
     *
     * @var array
     */
    protected $base64Encoded = [];

    /**
     * The list of settings that must be parsed to int.
     *
     * @var array
     */
    protected $toint = [];

    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [];

    /**
     * Returns a list of available locales by name.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listLocaleAction(Request $request)
    {
        $query   = $request->get('q');
        $locales = $this->get('core.locale')->setContext('frontend')
            ->getSupportedLocales();

        if (!empty($query)) {
            $locales = array_filter($locales, function ($a) use ($query) {
                return strpos(strtolower($a), strtolower($query)) !== false;
            });
        }

        $keys    = array_keys($locales);
        $values  = array_values($locales);
        $locales = [];

        for ($i = 0; $i < count($keys); $i++) {
            $locales[] = [
                'code' => $keys[$i],
                'name' => "$values[$i] ($keys[$i])"
            ];
        }

        return new JsonResponse($locales);
    }

    /**
     * Returns the list of settings.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('SETTINGS_MANAGER')
     *     and hasPermission('ONM_SETTINGS')")
     */
    public function listAction(Request $request)
    {
        $params = $request->get('params');
        $keys   = is_array($params) && !empty($params) ? array_keys($params) : $this->keys;

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get($keys);

        // Decode scripts
        foreach ($this->base64Encoded as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = base64_decode($settings[$key]);
            }
        }

        // Parse to int keys
        if ($this->toint && !empty($this->toint)) {
            foreach ($this->toint as $key) {
                if (array_key_exists($key, $settings) && !empty($settings[$key]) &&
                    is_array($settings[$key])) {
                    foreach ($settings[$key] as $element => $value) {
                        $settings[$key][$element] = (int) $value;
                    }
                } elseif (array_key_exists($key, $settings)) {
                    $settings[$key] = (int) $settings[$key];
                }
            }
        }

        $settings = array_filter($settings, function ($a) {
            return !empty($a);
        });

        return [
            'settings' => $settings,
        ];
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     *
     * @Security("hasExtension('SETTINGS_MANAGER')
     *     and hasPermission('ONM_SETTINGS')")
     */
    public function saveAction(Request $request)
    {
        return $this->saveSettings($request->get('settings'));
    }

    /**
     * Performs the action of saving the configuration settings
     *
     * @param Mixed $settings the settings object
     *
     * @return Response the response object
     *
     */
    protected function saveSettings($settings)
    {
        $msg = $this->get('core.messenger');

        // Remove settings for only masters
        if (!$this->get('core.security')->hasPermission('MASTER')) {
            foreach ($this->onlyMasters as $key) {
                unset($settings[$key]);
            }
        }

        // Encode scripts
        foreach ($this->base64Encoded as $key) {
            if (array_key_exists($key, $settings)) {
                $settings[$key] = base64_encode($settings[$key]);
            }
        }

        if (!empty($settings)) {
            // Save settings
            $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->set($settings);

            // Delete caches for custom_css and frontpages
            $this->get('core.dispatcher')->dispatch('setting.update');

            // TODO: Remove when using new ORM features
            $cache = $this->get('cache');
            foreach ($this->keys as $key) {
                $cache->delete($key);
            }
        }


        $msg->add(_('Settings saved.'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getcode());
    }
}
