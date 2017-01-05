<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ManagerWebService\Controller;

use Common\Core\Annotation\Security;
use Common\ORM\Entity\Setting;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Displays, saves, modifies and removes settings.
 */
class SettingController extends Controller
{
    /**
     * @api {get} /settings List of settings
     * @apiName GetSettings
     * @apiGroup Setting
     *
     * @apiParam {String} oql The OQL query.
     *
     * @apiSuccess {Integer} total   The total number of elements.
     * @apiSuccess {Array}   results The list of settings.
     *
     * @Security("hasPermission('setting_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $converter = $this->get('orm.manager')->getConverter('Settings');
        $ds        = $this->get('orm.manager')->getDataSet('Settings', 'manager');
        $settings  = $ds->get([ 'max_height', 'max_width', 'site_language', 'time_zone' ]);

        return new JsonResponse([
            'extra'    => $this->getExtraData(),
            'settings' => $converter->objectify($settings)
        ]);
    }

    /**
     * Creates a new setting.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasPermission('setting_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $em  = $this->get('orm.manager');
        $msg = $this->get('core.messenger');
        $ds  = $em->getDataSet('Settings', 'manager');

        $settings = $request->request->all();

        $ds->set($settings);
        $msg->add(_('Settings saved successfully'), 'success');

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());

        return $response;
    }

    /**
     * Returns a list of parameters for the template.
     *
     * @return array Array of template parameters.
     */
    private function getExtraData()
    {
        $extra = [
            'languages'  => $this->get('core.locale')->getLocales(),
            'time_zones' => \DateTimeZone::listIdentifiers()
        ];

        return $extra;
    }
}
