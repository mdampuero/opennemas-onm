<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\NewsML\NewsMLComponent;

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

    /**
     * {@inheritdoc}
     */
    public function getFile($data)
    {
        $files = $data->xpath('/NewsComponent/NewsComponent/ContentItem');

        if (empty($files)) {
            return '';
        }

        $i     = 0;
        $index = 0;

        foreach ($files as $file) {
            $file = simplexml_load_string($file->asXML());

            // Ignore videos
            $video = $file->xpath('/ContentItem/MediaType[@FormalName="Video"]');

            if (empty($video)) {
                $f = $file->xpath('/ContentItem/Characteristics/Property[@FormalName="EFE_Filename"]');

                if (!empty($f)
                    && strpos($f[0]->attributes()->Value, 'w.')
                    && !strpos($f[0]->attributes()->Value, 'miniw.')
                ) {
                    $index = $i;
                }
            }

            $i++;
        }

        return simplexml_load_string($files[$index]->asXML());
    }

        /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $resource = parent::parse($data);

        $resource->body = str_replace(['<p>', '</p>'], '', (string) $resource->body);

        return $resource;
    }
}
