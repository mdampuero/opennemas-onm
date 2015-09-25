<?php
/**
 * This file is part of the Onm package.
 *
 * (c) OpenHost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Onm\Import\DataSource\Parser;

/**
 * Parses XML files in Europapress custom format.
 */
class EuropaPress extends Parser
{
    /**
     * {@inheritdoc}
     */
    public function __construct($factory)
    {
        parent::__construct($factory);

        $this->categories = [
            'ACE' => _('Society'),
            'AGR' => _('Agriculture'),
            'AYU' => _('Council'),
            'CUL' => _('Society (CUL)'),
            'CYA' => _('Event CYA'),
            'CYT' => _('Society CYT'),
            'DCG' => _('Disturbs'),
            'DEP' => _('Sports'),
            'ECO' => _('Economy'),
            'EDU' => _('Society Education EDU'),
            'GAL' => _('GALICIA'),
            'JEI' => _('JUSTICIA'),
            'MEA' => _('Environment'),
            'OPI' => _('Opinion'),
            'OPL' => _('Politics OPL'),
            'POL' => _('Politics POL'),
            'PRT' => _('TV Guide'),
            'REL' => _('Society REL'),
            'SAN' => _('Society SAN'),
            'SOC' => _('Society SOC'),
            'SUC' => _('Event SUC'),
            'SYS' => _('Society SYS'),
            'TOR' => _('Society Toros'),
            'TRI' => _('Justice')
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (is_object($data) && $data->CODIGO->count() > 0) {
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
        $body = (string) $data->CONTENIDO;
        $body = nl2br($body);

        return iconv(mb_detect_encoding($body), "UTF-8", $body);
    }

    /**
     * Returns the category from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The category name.
     */
    public function getCategory($data)
    {
        $category = (string) $data->SECCION;

        if (!empty($category)) {
            if (array_key_exists($category, $this->categories)) {
                return $this->categories[$category];
            }
        }

        return '';
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
        $dateFormat = 'd/m/Y H:i:s';
        $originalDate = $data->FECHA.' '.$data->HORA;

        $date = \DateTime::createFromFormat(
            $dateFormat,
            $originalDate,
            new \DateTimeZone('Europe/Madrid')
        );

        $date = \DateTime::createFromFormat(
            'd/m/Y H:i:s P',
            $date->format('d/m/Y H:i:s P')
        );

        return $date;
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
        return (string) $data->CODIGO;
    }

    /**
     * Returns the pretitle from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The pretitle.
     */
    public function getPretitle($data)
    {
        $pretitle = (string) $data->ANTETITULO;

        return iconv(mb_detect_encoding($pretitle), "UTF-8", $pretitle);
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
        $priority = (string) $data->PRIORIDAD;

        if (!empty($priority)) {
            if (array_key_exists($priority, $this->priorities)) {
                return $this->priorities[$priority];
            }
        }

        return 1;
    }

    /**
     * Returns the summary from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The summary.
     */
    public function getSummary($data)
    {
        $summary = (string) $data->ENTRADILLA;

        return iconv(mb_detect_encoding($summary), "UTF-8", $summary);
    }

    /**
     * Returns the title from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The resource title.
     */
    public function getTitle($data)
    {
        $title = (string) $data->TITULAR;

        return iconv(mb_detect_encoding($title), "UTF-8", $title);
    }

    /**
     * Returns the unique urn from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The resource URN.
     */
    public function getUrn($data)
    {
        $classname = get_class($this);
        $classname = substr($classname, strrpos($classname, '\\') + 1);

        $tokens = [
            'europapress',
            $this->getCreatedTime($data)->format('YmdHis'),
            $this->getId($data)
        ];

        return 'urn:' . implode(':', $tokens);
    }

    /**
     * Returns the photo from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return array The photo data.
     */
    public function getPhoto($data)
    {
        if (!empty($data->FOTO)) {
            return [
                'id'           => $this->getId(),
                'name'         => (string) $this->getData()->FOTO->NOMBRE,
                'title'        => (string) $this->getData()->FOTO->PIE,
                'created_time' => $this->getCreatedTime(),
                'file_type'    => 'image/' . substr($this->getData()->FOTO->EXTENSION, 1),
                'file_path'    => (string) $this->getData()->FOTO->NOMBRE,
                'media_type'   => substr($this->getData()->FOTO->EXTENSION, 1)
            ];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $contents = [
            [
                'agency_name'  => 'Europapress',
                'body'         => $this->getBody($data),
                'category'     => $this->getCategory($data),
                'created_time' => $this->getCreatedTime($data),
                'id'           => $this->getId($data),
                'pretitle'     => $this->getPretitle($data),
                'priority'     => $this->getPriority($data),
                'related'      => [],
                'summary'      => $this->getSummary($data),
                'tags'         => $this->getTags($data),
                'title'        => $this->getTitle($data),
                'urn'          => $this->getUrn($data)
            ]
        ];

        $photo = $this->getPhoto($data);

        if (!empty($photo)) {
            $contents[0]['related'] = $photo['id'];
            $contents[] = $photo;
        }

        return $contents;
    }
}
