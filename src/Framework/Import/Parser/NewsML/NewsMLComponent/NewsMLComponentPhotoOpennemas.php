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
class NewsMLComponentPhotoOpennemas extends NewsMLComponentPhoto
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
            '/NewsComponent/NewsComponent/ContentItem/MediaType[@FormalName="PhotoFront"]'
        );

        if (!empty($node)) {
            return true;
        }

        $node = $data->xpath(
            '/NewsComponent/NewsComponent/ContentItem/MediaType[@FormalName="PhotoInner"]'
        );

        if (!empty($node)) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary($data)
    {
        $summaries = $data->xpath('/NewsComponent//p');

        $summary = '';
        if (is_array($summaries) && !empty($summaries)) {
            foreach ($summaries as $s) {
                $summary .= (string) $s;
            }

            $summary = iconv(mb_detect_encoding($summary), "UTF-8", $summary);
        }

        return $summary;
    }

    /**
     * Returns the filename from the parsed data.
     *
     * @param SimpleXMLObject $data The parsed data.
     *
     * @return string The file name.
     */
    public function getFilename($data)
    {
        $filename = $data->xpath('/ContentItem/Characteristics/Property[@FormalName="Onm_Filename"]');

        if (!empty($filename)) {
            return (string) $filename[0]->attributes()->Value;
        }

        return $this->getFromBag('filename');
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $resource =  parent::parse($data);

        $resource->agency_name = 'Opennemas';

        return $resource;
    }
}
