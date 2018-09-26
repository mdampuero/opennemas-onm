<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\Core\Component\Helper;

class InstanceSyncHelper
{

    /**
     * The application service container
     *
     * @var string
     */
    private $container;

    /**
     * Initializes the PurchaseManager.
     *
     * @param ServiceContainer $contaienr The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns the sync URL from a categoryName
     *
     * @param string $categoryName the name of the category
     *
     * @return string the URL to sync from
     */
    public function getSyncURL($categoryName)
    {
        $syncParams = $this->container->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get('sync_params');

        if ($syncParams) {
            foreach ($syncParams as $siteUrl => $values) {
                if (is_array($values['categories'])
                    && in_array($categoryName, $values['categories'])
                ) {
                    return $siteUrl;
                }
            }
        }

        return '';
    }
}
