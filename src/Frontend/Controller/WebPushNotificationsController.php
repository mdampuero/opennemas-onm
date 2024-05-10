<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Frontend\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Defines the frontend controller for the articles.
 */
class WebPushNotificationsController extends FrontendController
{
    /**
     * {@inheritdoc}
     */
    public function scriptAction(Request $request)
    {
        $settingsDs = $this->get('orm.manager')->getDataSet('Settings', 'instance');
        $service    = $request->attributes->get('service') ?? $settingsDs->get('webpush_service');

        if (empty($service) || !empty($settingsDs->get('webpush_stop_collection'))) {
            throw new ResourceNotFoundException();
        }

        try {
            $webpushHelper = $this->get(sprintf('core.helper.%s', $service));
            return $webpushHelper->getWebpushCollectionFile();
        } catch (\Exception $e) {
            throw new ResourceNotFoundException();
        }
        throw new ResourceNotFoundException();
    }
}
