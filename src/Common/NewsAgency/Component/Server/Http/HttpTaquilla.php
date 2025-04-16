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

        return !empty($content)
            && json_validate($content);
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
        $content = $this->getContentFromUrl($this->getUrl());

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

        // Associate objects with their place and entity
        foreach ($data['objects'] as &$value) {
            $value['place']  = $data['places'][$value['place_id']];
            $value['entity'] = $data['entities'][array_pop($value['entity_ids'])];
             // Generate files
            $this->remoteFiles[] = [
                'content'  => $value,
                'filename' => (string) $value['date'] . $value['event_id'] . '.xml',
                'url'      => preg_replace([ '/\n/', '/\s+/' ], '', $value['event_id'])
            ];
        }

        return $this;
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
        $content->event_latitude       = $data['place']['latitude'];
        $content->event_longitude      = $data['place']['longitude'];
        $content->event_tickets_price  = $details['sminprice'];
        $content->event_tickets_link   = $details['sminprice_url'];
        $content->event_website        = $data['entity']['name_url'];
        $content->extCategory          = $data['entity']['type'];
        $content->tags                 = [];

        if (!empty($data['entity']['img_urls'])) {
            $featureContent['inner'] = new \StdClass();

            $img     = $data['entity']['img_urls']['full_size'];
            $imgData = @getimagesize($img);

            $featureContent['inner']->title             = $data['entity']['name'];
            $featureContent['inner']->content_type_name = 'photo';
            $featureContent['inner']->pk_content        = 'photo_' . $data['event_id'];
            $featureContent['inner']->created           = $now;
            $featureContent['inner']->external_uri      = $data['entity']['img_urls']['full_size'];
            $featureContent['inner']->image_data        = [
                'filename' => basename($img),
                'mimetype' => $imgData['mime'],
                'width'    => $imgData[0],
                'height'   => $imgData[1]
            ];
        }

        $content->featureContent = $featureContent ?? null;

        return $content;
    }

    /**
     * Listado de tipos y subtipos
     */
    public function getTypes()
    {
        $tipos = [
            // Tipos principales
            4   => 'Espectáculos y teatro',
            2   => 'Conciertos',
            3   => 'Deportes',
            5   => 'Museos y visitas guiadas',
            7   => 'Parques de ocio',
            15  => 'Cine',

            // Subtipos de "Espectáculos y teatro"
            30  => 'Musicales',
            31  => 'Teatro',
            33  => 'Humor/Monólogos',
            32  => 'Magia',
            34  => 'Espectáculos para niños',
            36  => 'Circo',
            48  => 'Flamenco',
            49  => 'Ópera/Clásica',
            29  => 'Danza y Ballet',
            37  => 'Otros espectáculos',

            // Subtipos de "Conciertos"
            104 => 'Festivales',
            2   => 'Pop/Rock',
            8   => 'Hip Hop',
            46  => 'Hard-Rock/Heavy',
            45  => 'Autor/Folk',
            47  => 'Jazz/Blues',
            22  => 'Dance/Electrónica',
            10  => 'Otros estilos',
            102 => 'Discotecas',

            // Subtipos de "Deportes"
            14  => 'Fútbol',
            15  => 'Baloncesto',
            16  => 'Tenis',
            17  => 'F1/automovilismo',
            18  => 'Motociclismo',
            20  => 'Otros deportes',

            // Subtipos de "Museos y visitas guiadas"
            24  => 'Museos/Exposiciones',
            26  => 'Visitas guiadas y Tours',

            // Subtipos de "Parques de ocio"
            43  => 'Parques de atracciones',
            41  => 'Parques acuáticos',
            42  => 'Zoo/Acuarios',
            44  => 'Otros parques',
        ];

        return $tipos;
    }
}
