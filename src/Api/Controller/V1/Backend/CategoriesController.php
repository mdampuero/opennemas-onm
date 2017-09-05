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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Common\Core\Controller\Controller;

/**
 * Crud and management for categories entity.
 */
class CategoriesController extends Controller
{
    /**
     * Save settings for USER_MANAGER extension.
     *
     * @param Request $request The request object.
     *
     * @return JsonResposne The response object.
     */
    public function saveCategoriesAction(Request $request)
    {
        $msg      = $this->get('core.messenger');
        $categories = $request->request->all();

        try {
            $this->get('orm.manager')->getDataSet('Category', 'instance')
                ->set('user_settings', $settings);

            $msg->add(_('Settings saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns the list of settings for USER_MANAGER extension.
     *
     * @return JsonResponse The response object.
     */
    public function listSettingsAction()
    {
        $settings = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('user_settings', []);

        return new JsonResponse([ 'settings' => $settings ]);
    }
}
