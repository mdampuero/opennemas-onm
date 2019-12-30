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

class PhotoController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'IMAGE_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_photo_get_item';

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.photo';

    /**
     * {@inheritDoc}
     */
    protected function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('photo');
    }
}
