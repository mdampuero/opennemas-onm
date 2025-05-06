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
        // Associate objects with their place and entity
        foreach ($data['objects'] as $value) {
            $value['place']  = $data['places'][$value['place_id']];
            $value['entity'] = $data['entities'][reset($value['entity_ids'])];
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
     * Parses the array and returns an object with the event information.
     *
     * @param array      $data The data as an array.
     *
     * @return \StdClass $content The content data.
     */
    protected function parseArray(array $data) : \StdClass
    {
        /**
         * go_event
         * go_event_id
         * go_event_type
         * go_event_tag
         * go_event_extra_info
         * go_event_contact_web
         *
         * go_event_dates_start
         * go_event_dates_end
         * go_event_dates_duration
         * go_event_dates_all_day
         *
         * go_event_price_timetable
         *
         * go_event_tickets_free
         * go_event_tickets_price
         * go_event_tickets_link
         * go_event_tickets_sold_out
         *
         * go_event_address_address
         * go_event_address_name
         * go_event_address_locality
         * go_event_address_province
         * go_event_address_postal_code
         *
         * go_event_location_location
         * go_event_location_organizer
         *
         * go_event_map_latitude
         * go_event_map_longitude
         * go_event_map_link
         * go_event_map_place_id
         *
         * go_event_price_timetable
         */

        $content = new \StdClass();
        $now     = new \DateTime();
        $time    = array_pop($data['time']);
        $details = array_pop($data['session_details']);

        $content->id                   = $data['event_id'];
        $content->title                = $data['event_name'];
        $content->body                 = $data['entity']['info'];
        $content->created              = $now;
        $content->starttime            = $now;
        $content->changed              = $now;
        $content->agency               = 'Taquilla.com';
        $content->content_type_name    = 'event';
        $content->event_organizer_name = 'La Guía GO! | Taquilla.com';
        $content->event_organizer_url  = $data['entity']['name_url'];
        $content->event_start_date     = $data['date'];
        $content->event_start_hour     = $time != 'unknown' ? $time : '00:00';
        $content->event_place          = $data['place']['name'];
        $content->event_city           = $data['place']['city'];
        $content->event_address        = $data['place']['address'];
        $content->event_map_latitude   = $data['place']['latitude'];
        $content->event_map_longitude  = $data['place']['longitude'];
        $content->event_tickets_price  = $details['sminprice'];
        $content->event_tickets_link   = $details['sminprice_url'];
        $content->event_type_id        = $data['entity']['type_id'];
        $content->event_subtype_id     = $data['entity']['subtype_id'];
        $content->event_website        = $data['entity']['name_url'];
        $content->extCategory          = $data['entity']['type'];
        $content->tags                 = [];

        if (array_key_exists('end_date', $data) && $data['end_date'] != $data['date']) {
            $content->event_end_date = $data['end_date'];
            $content->event_end_hour = $time != 'unknown' ? $time : '00:00';
        }

        if (!empty($data['entity']['img_urls'])) {
            $featureContent['inner'] = new \StdClass();

            $img = $data['entity']['img_urls']['full_size'];

            $featureContent['inner']->title             = $data['entity']['name'];
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
