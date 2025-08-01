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

use Api\Exception\GetItemException;
use Api\Service\V1\CategoryService;
use Opennemas\Orm\Core\EntityManager;

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
     * Initializes the DynamicCssService.
     *
     * @param EntityManager   $em The entity manager service.
     * @param CategoryService $cs The category service.
     */
    public function __construct(EntityManager $em, CategoryService $cs)
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
        $datetime = new \DateTime();

        if ($section == '%global%' || $section == 'home') {
            $id = $section;
        } else {
            try {
                $id = $this->cs->getItemBySlug($section)->id;
            } catch (GetItemException $e) {
                return $datetime->getTimestamp();
            }
        }

        $timestamps = $settings->get('dynamic_css', []);

        if (empty($timestamps) || empty($timestamps[ $id ])) {
            $timestamps[ $id ] = $datetime->getTimestamp();
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
