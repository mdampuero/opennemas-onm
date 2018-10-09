<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\NewsML\NewsMLComponent;

/**
 * Parses NewsComponent that represent a photo resource from NewsML files.
 */
class NewsMLComponentPhotoEuropaPress extends NewsMLComponentPhoto
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath(
            '/NewsComponent/NewsComponent/ContentItem/MediaType[@FormalName="Photo"]'
        );

        if (!empty($node) &&
            (preg_match('/europa\s*press/i', $this->getAgencyName($data)) ||
            preg_match('/europa\s*press/i', $this->getFromBag('agency_name')))
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getAgencyName($data)
    {
        $agency = $data->xpath('/NewsComponent/AdministrativeMetadata/Creator/Party');

        if (is_array($agency) && count($agency) > 0) {
            return (string) $agency[0]->attributes()->FormalName;
        }

        return $this->getFromBag('agency_name');
    }
}
