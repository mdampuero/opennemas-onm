<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\NewsML\EuropaPress;

use Framework\Import\Parser\NewsML\NewsML;

/**
 * Parses XML files in NewsML custom format for EuropaPress.
 */
class NewsMLEuropaPress extends NewsML
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (!parent::checkFormat($data)) {
            return false;
        }

        if ($this->getAgencyName($data) === 'Europa Press') {
            return true;
        }

        return false;
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
        $body = $data->xpath('//DataContent');

        if (empty($body)) {
            return $this->getFromBag('body');
        }

        $body = $body[0];

        preg_match('@<body[^>]*>(.*?)<\/body>@is', $body, $matches);

        $body = '';
        if (array_key_exists(1, $matches)) {
            $body = $matches[1];
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
        $date = (string) $data->NewsEnvelope->DateAndTime;

        // ISO 8601 doesn't match this date 20111211T103900+0000
        $date = preg_replace('@\+(\d){4}$@', '', $date);
        $date = \DateTime::createFromFormat(
            'Y-m-d\TH:i:s',
            $date,
            new \DateTimeZone('UTC')
        );

        $date->setTimezone(new \DateTimeZone('UTC'));

        return $date;
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
        $topics = $data->xpath('//NewsItem/NewsComponent/TopicSet/Topic');

        $tags = array();
        foreach ($topics as $topic) {
            $tag = ucwords(strtolower((string) $topic->Description));

            if (stripos($tag, 'Servicio') !== 0) {
                $tags[] = $tag;
            }
        }

        $tags = array_unique($tags);

        return implode(',', $tags);
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $this->bag['agency_name']  = $this->getAgencyName($data);
        $this->bag['id']           = $this->getId($data);
        $this->bag['body']         = $this->getBody($data);
        $this->bag['tags']         = $this->getTags($data);
        $this->bag['created_time'] = $this->getCreatedTime($data)
            ->format('Y-m-d H:i:s');

        $items = $data->xpath('/NewsML/NewsItem');

        $contents = [];
        foreach ($items as $item) {
            $items    = simplexml_load_string($item->asXML());
            $contents = array_merge($contents, $this->parseItem($item));
        }

        foreach ($contents as &$content) {
            $content->merge($this->bag);
        }

        return $contents;
    }
}
