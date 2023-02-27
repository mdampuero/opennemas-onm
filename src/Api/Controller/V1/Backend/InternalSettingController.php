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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Displays and saves system settings.
 */
class InternalSettingController extends SettingController
{
    /**
     * The list of settings that must be base64 encoded/decoded.
     *
     * @var array
     */
    protected $base64Encoded = [];

    /**
     * The list of settings that can be saved.
     *
     * @var array
     */
    protected $keys = [
        'onm_digest_user',
        'onm_digest_pass',
        'contact_email'
    ];

    /**
     * The list of settings that can be saved only by MASTER users.
     *
     * @var array
     */
    protected $onlyMasters = [];

    public function listAction(Request $request)
    {
        return new JsonResponse(
            parent::listAction($request)
        );
    }
}
