<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Service\V1;

use Api\Exception\CreateItemException;
use Api\Exception\FileAlreadyExistsException;
use Api\Exception\UpdateItemException;

class WidgetService extends ContentOldService
{
    /**
     * Initializes the BaseService.
     *
     * @param ServiceContainer $container The service container.
     * @param string           $entity    The entity fully qualified class name.
     * @param string           $entity    The validator service name.
     */
    public function __construct($container, $entity, $validator = null)
    {
        $this->class      = $entity;
        $this->container  = $container;
        $this->dispatcher = $container->get('core.dispatcher');
        $this->em         = $container->get('widget_repository');
        $this->entity     = substr($entity, strrpos($entity, '\\') + 1);

        if (!empty($validator)) {
            $this->validator = $validator;
        }
    }
}
