<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Component\Assetic;

use Common\ORM\Core\EntityManager;

class DynamicCssService
{
    /**
     * The entity manager.
     *
     * @var EntityManager
     */
    protected $em;

    /**
     * The category api service
     *
     * @var CategoryService
     */
    protected $cs;

    /**
     * Initializes the instance
     */
    public function __construct($em, $cs)
    {
        $this->em = $em;
        $this->cs = $cs;
    }

    /**
     * Retrieves timestamp for an specific section
     * or create timestamp if doesn't exists
     *
     * @param String The section name
     *
     * @return Datetime The timestamp for the dynamic css
     */
    public function getTimestamp($section)
    {
        $settings = $this->em->getDataSet('Settings', 'instance');

        if ( $section == '%global%' || $section == 'home' ) {
            $id = $section;
        } else {
            $id = $this->cs->getItemBySlug($section)->pk_content_category;
        }

        $timestamps = $settings->get('dynamic_css', []);

        if (empty($timestamps) || empty($timestamps[ $id ])) {
            $datetime               = new \DateTime();
            $timestamps[ $id ]      = $datetime->getTimestamp();
            $settings->set('dynamic_css', $timestamps);
            return $datetime->getTimestamp();
        }

        return $timestamps[ $id ];
    }

    /**
     * Invalidate timestamp for an specific section
     *
     * @param String The section name
     */
    public function deleteTimestamp($section)
    {
        $settings = $this->em->getDataSet('Settings', 'instance');

        $timestamps = $settings->get('dynamic_css', []);
        unset($timestamps[ $section ]);
        $settings->set('dynamic_css', $timestamps);
    }
}
