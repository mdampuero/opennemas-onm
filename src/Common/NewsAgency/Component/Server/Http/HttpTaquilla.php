<?php
/*
 * This file is part of the onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Server\Http;

use Common\NewsAgency\Component\Server\Server;

/**
 * Synchronize local folders with an HTTP Taquilla server with Json data.
 */
class HttpTaquilla extends Http
{
    /**
     * {@inheritdoc}
     */
    public function checkConnection() : bool
    {
        $content = $this->getContentFromUrl($this->getUrl());

        return !empty($content);
    }

    /**
     * {@inheritdoc}
     */
    public function checkParameters() : bool
    {
        if (array_key_exists('url', $this->params)
            && preg_match('@api.taquilla.com@', $this->getUrl())
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFiles(string $path, ?array $files = null) : Server
    {
        if (empty($files)) {
            $files = $this->remoteFiles;
        }

        if (!is_writable($path)) {
            throw new \Exception(
                sprintf(_('Directory %s is not writable.'), $path)
            );
        }

        foreach ($files as $file) {
            $localFile = $path . DS . $file['filename'];
            // If no content on file, download it (is an image)
            if (!file_exists($localFile)) {
                $content = $file['content'] ?? null;
                !is_null($content)
                  ? $this->generateNewsML($localFile, $file['content'])
                  : file_put_contents($localFile, $this->getContentFromUrl($file['url']));

                $this->downloaded++;
            }

            $this->localFiles[] = $localFile;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemoteFiles() : Server
    {
        $content = $this->getContentFromUrl($this->getUrl() . '&t10num=1000&t10ic=1');

        if (!$content) {
            throw new \Exception(sprintf(
                _('Can\'t connect to server %s. Please check your connection details.'),
                $this->params['name']
            ));
        }

        $data = json_decode($content, true);

        if (empty($data)) {
            return $this;
        }

        $this->generateDataArray($data);

        // Iterate over the next event pages
        do {
            $content = $this->getContentFromUrl(
                'https://api.taquilla.com' . $data['meta']['next'] . '&t10id=96008'
            );

            $data = json_decode($content, true);

            if (!empty($data)) {
                $this->generateDataArray($data);
            }
        } while ($data['meta']['next']);

        // Group events by entity to avoid duplicated
        $this->groupEventsByEntity($data);

        return $this;
    }

    /**
     * Generates data array and add data to the remoteFiles array
     *
     * @param array  $data The array file content.
     */
    protected function generateDataArray(array $data) : void
    {
        // Associate objects with their place and entities
        foreach ($data['objects'] as $value) {
            foreach ($value['entity_ids'] as $id) {
                $value['entities'][] = $data['entities'][$id];
            }

            $value['place'] = $data['places'][$value['place_id']];
            // Generate files
            $this->remoteFiles[] = [
                'content'  => $value,
                'filename' => (string) $value['date'] . $value['event_id'] . '.xml',
                'url'      => preg_replace([ '/\n/', '/\s+/' ], '', $value['event_id'])
            ];
        }
    }

    /**
     * Groups remote files by event/entity combinations and consolidates date ranges
     *
     * Transforms multiple events with the same name/entity but different dates into
     * single events with start/end date ranges. This reduces duplication while
     * preserving the temporal information.
     */
    protected function groupEventsByEntity(): void
    {
        $grouped = [];

        // Group files by event_name + last entity_id hash
        foreach ($this->remoteFiles as $file) {
            $eventKey = md5(
                $file['content']['event_name'] . '-' .
                end($file['content']['entity_ids'])
            );

            $grouped[$eventKey][] = $file;
        }

        // Process each group to consolidate date ranges
        $consolidatedEvents = [];
        foreach ($grouped as $files) {
            if (count($files) === 1) {
                $consolidatedEvents[] = $files[0];
                continue;
            }

            $consolidatedEvents[] = $this->createDateRangeEvent($files);
        }

        $this->remoteFiles = $consolidatedEvents;
    }

    /**
     * Creates a consolidated event with date range from multiple single-date events
     */
    protected function createDateRangeEvent(array $events): array
    {
        $dates = array_column(array_column($events, 'content'), 'date');
        sort($dates);

        $baseEvent                        = $events[0];
        $baseEvent['content']['date']     = reset($dates); // earliest date
        $baseEvent['content']['end_date'] = end($dates); // latest date

        return $baseEvent;
    }

    /**
     * Saves a new NewsML files from a string.
     *
     * @param string $path    The path to the file.
     * @param array  $content The array file content.
     */
    protected function generateNewsML(string $path, array $data) : void
    {
        $content = $this->parseArray($data);

        $newsML = $this->tpl->fetch('news_agency/newsml_templates/base.tpl', [
            'content'       => $content,
            'featuredMedia' => $content->featureContent,
            'extSource'     => "Taquilla.com",
            'extCategory'   => $content->extCategory,
            'tags'          => []
        ]);

        file_put_contents($path, $newsML);

        $time = $content->created->getTimestamp();

        touch($path, $time);
    }

    /**
     * Check if server is from a special city.
     * Madrid (54) or Barcelona (36)
     *
     * @param bool $content True if is a special city.
     */
    protected function isSpecialCity() : bool
    {
        $urlParams = parse_url($this->getUrl());

        if (!key_exists('query', $urlParams)) {
            return false;
        }

        parse_str($urlParams['query'], $queryParams);

        if (!key_exists('t10region', $queryParams)) {
            return false;
        }

        return in_array($queryParams['t10region'], [ 54, 36 ]);
    }

    /**
     * Parses the array and returns an object with the Taquilla event.
     *
     * @param array      $data The data as an array.
     *
     * @return \StdClass $content The content data.
     */
    protected function parseArray(array $data) : \StdClass
    {
        $content = new \StdClass();
        $now     = new \DateTime();
        $time    = array_pop($data['time']);
        $details = array_pop($data['session_details']);

        // Process entities data related due to multiple entities
        $content->body = '';
        foreach ($data['entities'] as $entity) {
            $content->body            .= $entity['info'] . "\n";
            $content->event_type_id    = $entity['type_id'];
            $content->event_subtype_id = $entity['subtype_id'];
            $content->extCategory      = $entity['type'];
        }

        // Import as article if is from those types
        $content->content_type_name = 'event';
        $types                      = [
            5  => 'Museos y visitas guiadas',
            7  => 'Parques de ocio',
            26 => 'Visitas guiadas y tours',
            43 => 'Parques de atracciones',
            41 => 'Parques acuáticos',
            42 => 'Zoo/Acuarios',
            44 => 'Otros parques',
        ];

        if (in_array($content->event_subtype_id, array_keys($types))) {
            $content->content_type_name = 'article';
        }

        // Import as article if is Musical and special city
        if ($this->isSpecialCity() && $content->event_subtype_id == 30) {
            $content->content_type_name = 'article';
        }

        $content->id                   = $data['event_id'];
        $content->title                = $data['event_name'];
        $content->created              = $now;
        $content->starttime            = $now;
        $content->changed              = $now;
        $content->agency               = 'Taquilla.com';
        $content->event_organizer_name = 'La Guía GO! | Taquilla.com';
        $content->event_start_date     = $data['date'];
        $content->event_start_hour     = $time != 'unknown' ? $time : '00:00';
        $content->event_place          = $data['place']['name'];
        $content->event_city           = $data['place']['city'];
        $content->event_address        = $data['place']['address'];
        $content->event_map_latitude   = $data['place']['latitude'];
        $content->event_map_longitude  = $data['place']['longitude'];
        $content->event_tickets_price  = $details['sminprice'];
        $content->event_tickets_link   = $details['sminprice_url'];
        $content->event_organizer_url  = strtok($details['sminprice_url'], '?');
        $content->event_website        = $details['sminprice_url'];
        $content->tags                 = [];

        if (array_key_exists('end_date', $data) && $data['end_date'] != $data['date']) {
            $content->event_end_date = $data['end_date'];
            $content->event_end_hour = $time != 'unknown' ? $time : '00:00';
        }

        $entity = reset($data['entities']);
        if (!empty($entity['img_urls'])) {
            $featureContent['inner'] = new \StdClass();

            $img = $entity['img_urls']['full_size'];

            $featureContent['inner']->title             = $entity['name'];
            $featureContent['inner']->content_type_name = 'photo';
            $featureContent['inner']->pk_content        = 'photo_' . $data['event_id'];
            $featureContent['inner']->created           = $now;
            $featureContent['inner']->external_uri      = $img;
            $featureContent['inner']->image_data        = ['filename' => basename($img) ];
        }

        $content->featureContent = $featureContent ?? null;

        return $content;
    }
}
