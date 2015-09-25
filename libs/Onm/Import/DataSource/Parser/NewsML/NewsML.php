<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Parser;

class NewsML extends Parser
{
    /**
     * Array of parsed parameters.
     *
     * @var array
     */
    protected $bag = [];

    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!is_object($data)) {
            return false;
        }

        $node = $data->xpath('/NewsML');

        if (!is_array($node) || count($node) == 0) {
            return false;
        }

        // Check if NewsML from EFE
        $node = $data->xpath('//*[@Value="Agencia EFE"]');

        if (count($node) > 0) {
            return false;
        }

        // Check if NewsML from Europapress
        $node = $data->xpath('//ProviderId');

        if (empty($node) || $node[0] == 'Europa press') {
            return false;
        }

        return true;
    }

    /**
     * Checks if the given data contains a photo.
     *
     * @param SimpleXMLObject $data The data to check.
     *
     * @return boolean True if the given data contains a photo. Otherwise,
     * return false.
     */
    public function checkPhoto($data)
    {
        $q = '/NewsComponent/NewsComponent';

        // Check if NewsMLComponentPhoto
        $count = 0;
        $count += count($data->xpath($q . '/Role[@FormalName="Caption"]'));
        $count += count($data->xpath($q . '/Role[@FormalName="Preview"]'));
        $count += count($data->xpath($q . '/Role[@FormalName="Quicklook"]'));
        $count += count($data->xpath($q . '/Role[@FormalName="Thumbnail"]'));

        return $count > 0;
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
        $agency = $data->xpath('/Provider/Party');

        if (is_array($agency) && count($agency) > 0) {
            return (string) $agency[0]->attributes()->FormalName;
        }

        return '';
    }

    /**
     * Returns the bag of the current parser.
     *
     * @return array The parser bag.
     */
    public function getBag()
    {
        return $this->bag;
    }

    /**
     * Returns the body from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The body.
     */
    public function getBody($data)
    {
        $bodies = $data->xpath('//p');

        $body = '';
        foreach ($bodies[0]->children() as $child) {
            $body .= "<p>$child</p>\n";
        }

        return iconv(mb_detect_encoding($body), "UTF-8", $body);
    }

    /**
     * Returns the created time from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return \DateTime The created time.
     */
    public function getCreatedTime($data)
    {
        $date = $data->xpath('//FirstCreated');

        if (is_array($date) && !empty($date)) {
            return \DateTime::createFromFormat('Ymd\THisP', $date[0]);
        }

        return null;
    }

    /**
     * Returns the id from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The id.
     */
    public function getId($data)
    {
        $id  = $data->xpath('//NewsItemId');

        if (is_array($id) && count($id) > 0) {
            return (string) $id[0];
        }

        return '';
    }

    /**
     * Returns the pretitle from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The pretitle.
     **/
    public function getPretitle($data)
    {
        $pretitle = $data->xpath('//SubHeadLine');

        if (is_array($pretitle) && count($pretitle) > 0) {
            $pretitle = $pretitle[0];

            return iconv(mb_detect_encoding($pretitle), "UTF-8", $pretitle);
        }

        return '';
    }

    /**
     * Returns the priority from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return integer The priority level.
     */
    public function getPriority($data)
    {
        $priority = $data->xpath("//NewsItem/NewsManagement/Urgency");

        if (is_array($priority) && count($priority) > 0) {
            return (integer) $priority[0]->attributes()->FormalName;
        }

        return 1;
    }

    /**
     * Returns the type from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The type.
     */
    public function getType($data)
    {
        $type = $data->xpath("//MediaType");

        if (is_array($type) && count($type) > 0) {
            return (string) $type[0]->attributes()->FormalName;
        }

        return '';
    }

    /**
     * Returns the title from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The title.
     */
    public function getTitle($data)
    {
        $title = $data->xpath('//HeadLine');

        if (is_array($title) && count($title) > 0) {
            $title = (string) $title[0];
            return iconv(mb_detect_encoding($title), "UTF-8", $title);
        }

        return '';
    }

    /**
     * Returns the URN from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The URN.
     */
    public function getUrn($data)
    {
        return '';
        return (string) $data->NewsItem->Identification->NewsIdentifier->PublicIdentifier;
    }

    /**
     * Returns the list of tags from teh parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The tag list.
     */
    public function getTags($data)
    {
        $tags = $data->xpath('//DescriptiveMetadata//OfInterestTo');

        if (is_array($tags) && count($tags) > 0) {
            $tags = $tags[0]->FormalName;

            return explode('--', $tags);
        }

        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['id']           = $this->getId($data);
        $this->bag['created_time'] = $this->getCreatedTime($data);
        $this->bag['agency_name']  = $this->getAgencyName($data);

        $items = $data->xpath('/NewsML/NewsItem');

        $contents = [];
        foreach ($items as $item) {
            $items = simplexml_load_string($item->asXML());
            $contents = array_merge($contents, $this->parseItem($item));
        }

        foreach ($contents as &$content) {
            $content = array_merge($content, $this->bag);
        }

        return $contents;
    }

    /**
     * Parses a NewsComponent.
     *
     * @param SimpleXMLObject $data The component to parse.
     */
    public function parseComponent($data)
    {
        $parser = $this->factory->get($data);
        $parsed = $parser->parse($data);

        $this->bag = array_merge($this->bag, $parser->getBag());

        return $parsed;
    }

    /**
     * Parses a NewsItem.
     *
     * @param SimpleXMLObject $item The item to parse.
     */
    public function parseItem($item)
    {
        $components = $item->xpath('NewsComponent');

        $contents = [];
        foreach ($components as $component) {
            $component = simplexml_load_string($component->asXML());
            $contents = array_merge($contents, $this->parseComponent($component));
        }

        return $contents;
    }
}
