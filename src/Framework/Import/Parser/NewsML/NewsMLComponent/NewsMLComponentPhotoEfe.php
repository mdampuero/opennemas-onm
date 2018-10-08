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
class NewsMLComponentPhotoEfe extends NewsMLComponentPhoto
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

        if (empty($node)) {
            return false;
        }

        if (preg_match('/EFE/', $this->getAgencyName($data))) {
            return true;
        }

        return false;
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
        $filename = $data->xpath('/ContentItem/Characteristics/Property[@FormalName="EFE_Filename"]');

        if (!empty($filename)) {
            return (string) $filename[0]->attributes()->Value;
        }

        return $this->getFromBag('filename');
    }

    /**
     * {@inheritdoc}
     */
    public function getSummary($data)
    {
        $title = $data->xpath('/NewsComponent/NewsLines/HeadLine');
        if (empty($title)) {
            return $this->getFromBag('summary');
        }
        $title = (string) $title[0];
        return iconv(mb_detect_encoding($title), "UTF-8", $title);
    }
}
