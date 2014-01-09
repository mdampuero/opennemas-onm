<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Framework\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents as SymfonyKernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Onm\Settings as s;
use Symfony\Component\Debug\Debug;

/**
 * ResponseListener fixes the Response headers based on the Request.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ApplicationBootupListener implements EventSubscriberInterface
{
    /**
     * Filters the Response.
     *
     * @param FilterResponseEvent $event A FilterResponseEvent instance
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        if (!empty($_SERVER['HTTPS'])
            && $_SERVER['HTTPS'] !== 'off'
            || $_SERVER['SERVER_PORT'] == 443
        ) {
            $protocol = "https://";
        } else {
            $protocol = "http://";
        }

        define('SS', "/");
        define('DS', DIRECTORY_SEPARATOR);
        define('SYS_LOG_PATH', realpath(SITE_PATH.DS.'..'.DS."tmp/logs"));

        define('SITE', $_SERVER['SERVER_NAME']);
        define('BASE_URL', '/');
        define('ADMIN_DIR', "admin");
        define('SITE_URL', $protocol.SITE.BASE_URL);
        define('SITE_URL_ADMIN', SITE_URL.ADMIN_DIR);

        define('SYS_NAME_GROUP_ADMIN', 'Administrador');

        define('IMG_DIR', "images");
        define('FILE_DIR', "files");
        define('ADS_DIR', "advertisements");
        define('OPINION_DIR', "opinions");

        define('TEMPLATE_MANAGER', "manager");

        define('ITEMS_PAGE', "20"); // TODO: delete from application

        define('TEMPLATE_ADMIN', "admin");
        define('TEMPLATE_ADMIN_PATH', SITE_PATH.DS.DS."themes".DS.TEMPLATE_ADMIN.SS);
        define('TEMPLATE_ADMIN_PATH_WEB', SS."themes".SS.TEMPLATE_ADMIN.SS);
        define('TEMPLATE_ADMIN_URL', SS."themes".SS.TEMPLATE_ADMIN.SS);

        define('STATIC_PAGE_PATH', 'estaticas');

        // Backup paths
        define('BACKUP_PATH', SITE_PATH.DS.'..'.DS."tmp/backups");

        $maxUpload          = (int) (ini_get('upload_max_filesize'));
        $maxPost            = (int) (ini_get('post_max_size'));
        $memoryLimit        = (int) (ini_get('memory_limit'));
        $maxAllowedFileSize = min($maxUpload, $maxPost, $memoryLimit) * pow(1024, 2);
        define('MAX_UPLOAD_FILE', $maxAllowedFileSize);

        $commonCachepath = APPLICATION_PATH.DS.'tmp'.DS.'instances'.DS.'common';
        if (!file_exists($commonCachepath)) {
            mkdir($commonCachepath, 0755, true);
        }
        define('COMMON_CACHE_PATH', realpath($commonCachepath));

        if (!defined('DEPLOYED_AT')) {
            define('DEPLOYED_AT', '0000000000');
        }

        mb_internal_encoding("UTF-8");

        $env = getContainerParameter('environment');
        initEnvironment($env);

        // Register the Debugger into the application, transforms fatal errors into
        // exceptions only in development environments
        Debug::enable(null, ($env !== 'production'));
    }

    public static function getSubscribedEvents()
    {
        return array(
            SymfonyKernelEvents::REQUEST => array('onKernelRequest', 101),
        );
    }
}
