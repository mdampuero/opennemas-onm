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
 * Parses NewsComponent of text type from NewsML custom format for EFE.
 */
class NewsMLComponentTextEuropaPress extends NewsMLComponentText
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath('/NewsComponent');

        if (!is_array($node) || count($node) == 0) {
            return false;
        }

        $node = $data->xpath('/NewsComponent/ContentItem/MediaType[@FormalName="Text"]');

        if (!empty($node)
            && preg_match('/europa\s*press/i', $this->getAgencyName($data))
        ) {
            return true;
        }

        return false;
    }

    /**
     * Returns the agency name from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The agency name.
     */
    public function getAgencyName($data)
    {
        $agency = $data->xpath('//NewsLines/CreditLine');

        if (is_array($agency) && count($agency) > 0) {
            return (string) $agency[0];
        }

        return '';
    }
}
