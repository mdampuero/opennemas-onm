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

use Framework\Import\Parser\NewsML\NewsML;

/**
 * Parses NewsComponent of text type from NewsML custom format for EFE.
 */
class NewsMLComponentMultimediaEFE extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!parent::checkFormat($data)) {
            return false;
        }

        if (empty($data->xpath('/NewsComponent/ContentItem/Format[@FormalName="NITF"]'))) {
            return false;
        }

        if ($this->getAgencyName($data) == 'Agencia EFE') {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getAgencyName($data)
    {
        $agency = $data->xpath('//SentFrom/Party/Property');

        if (is_array($agency) && count($agency) > 0) {
            return (string) $agency[0]->attributes()->Value;
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getTags($data)
    {
        $tags = $data->xpath("//Property[@FormalName=\"Tesauro\"]");

        if (is_array($tags) && count($tags) > 0) {
            $tags = (string) $tags[0]->attributes()->Value;

            $groups = explode(";", $tags);
            $tags = array();
            foreach ($groups as $group) {
                preg_match('@(.*):(.*)@', $group, $matches);

                if (!empty($matches)) {
                    $tags[] = $matches[2];
                }
            }

            return implode(',', $tags);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $content = $data->xpath('//nitf');

        if (is_array($content) && count($content) > 0) {
            $content = $content[0];

            // Discard extra elements
            $content = simplexml_load_string(
                $content->asXML(),
                null,
                LIBXML_NOERROR | LIBXML_NOWARNING
            );

            try {
                $content = $this->factory->get($content)->parse($content);

                return $content[0];
            } catch (\Exception $e) {
                return [];
            }
        }
    }






























    /**
     * Returns the available text in this multimedia package
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return void
     **/
    public function getTexts2()
    {
        $contents = $data->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.texts\"]"
        );

        $texts = null;
        if (isset($contents[0]) && $contents[0]->NewsComponent) {
            foreach ($contents[0]->NewsComponent as $component) {
                $nitf = new \Onm\Import\DataSource\Format\NewsMLG1Component\NITF($component);
                $texts []= $nitf;
            }
        }

        return $texts;
    }

    /**
     * Returns the available photos in this multimedia package
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return void
     **/
    public function getPhotos()
    {
        if (!isset($this->photos)) {
            $contents = $data->xpath(
                "//NewsItem/NewsComponent/NewsComponent"
                ."[@Duid=\"multimedia_".$this->id.".multimedia.photos\"]"
            );

            if (count($contents) > 0) {
                $this->photos = array();
                foreach ($contents[0] as $componentName => $component) {
                    if ($componentName == 'NewsComponent') {
                        $photoComponent = new MultimediaResource($component);
                        $this->photos[] = $photoComponent;
                    }
                }
            } else {
                $this->photos = array();
            }
        }

        return $this->photos;
    }

    /**
     * Returns the available images in this multimedia package
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return void
     **/
    public function getVideos()
    {
        if (!isset($this->videos)) {
            $contents = $data->xpath(
                "//NewsItem/NewsComponent/NewsComponent"
                ."[@Duid=\"multimedia_".$this->id.".multimedia.videos\"]"
            );

            if (count($contents) > 0) {
                $this->videos = array();
                foreach ($contents[0] as $componentName => $component) {
                    if ($componentName == 'NewsComponent') {
                        $videoComponent = new MultimediaResource($component);
                        $this->videos[$videoComponent->id] = $videoComponent;
                    }
                }
            } else {
                $this->videos = array();
            }
        }

        return $this->videos;
    }

    /**
     * Returns the available audios in this multimedia package
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return void
     **/
    public function getAudios()
    {
        $contents = $data->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.audios\"]"
        );

        return $contents;
    }

    /**
     * Returns the available Documentary modules in this multimedia package
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return void
     **/
    public function getModdocs()
    {
        $contents = $data->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.moddocs\"]"
        );

        return $contents;
    }

    /**
     * Returns the available files in this multimedia package
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return void
     **/
    public function getFiles($data)
    {
        $contents = $data->xpath(
            "//NewsItem/NewsComponent/NewsComponent"
            ."[@Duid=\"multimedia_".$this->id.".multimedia.files\"]"
        );

        return $contents;
    }

    /**
     * Returns the available text in this multimedia package
     *
     * @return void
     **/
    public function getTexts()
    {
        // Multimedia contents
        $xpathExpresion = "//NewsItem/NewsComponent/NewsComponent[@Duid=\"multimedia_".$this->id.".multimedia.texts\"]";
        $multimediaContents = $this->getData()->xpath($xpathExpresion);

        // Only text contents
        $xpathExpresion = "//NewsItem/NewsComponent[@Duid=\"text_".$this->id.".text\"]";
        $textContents = $this->getData()->xpath($xpathExpresion);

        $texts = [];

        if (isset($multimediaContents[0])
            && $multimediaContents[0]->NewsComponent->count() > 0
        ) {
            foreach ($multimediaContents[0]->NewsComponent as $value) {
                $component = $value->ContentItem->DataContent;
                $texts[]   = new \Onm\Import\DataSource\Format\NewsMLG1Component\NITF($component);
            }
        }

        if (isset($textContents[0]) && $textContents[0]->ContentItem) {
            $component = $textContents[0]->ContentItem->DataContent;
            $texts[]   = new \Onm\Import\DataSource\Format\NewsMLG1Component\NITF($component);
        }

        return $texts;
    }
}
