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
 * All operations related to the translations.
 */
class TranslationController extends Controller
{

    /**
     * List all Translate services
     *
     * @param Request $request The request object.
     *
     * @return JsonResposne The response object.
     */
    public function servicesListAction(Request $request)
    {
        $translationServices = $this->get('core.translate')->getAvailableTranslators();
        return new JsonResponse($translationServices);
    }
}
