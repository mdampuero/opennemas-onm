<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Common\NewsAgency\Component\Parser\EuropaPress;

use Common\NewsAgency\Component\Parser\Parser;
use Common\NewsAgency\Component\Resource\ExternalResource;

/**
 * Parses XML files in custom Europapress format.
 */
class EuropaPress extends Parser
{
    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (is_object($data) &&
            $data->CODIGO->count() > 0 &&
            empty($data->FIRMA2) &&
            empty($data->FOTOP)
        ) {
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

        if (empty($body)) {
            return '';
        }

        $body = preg_split('/(\r\n|\n|\r)/', $body);
        $body = '<p>' . implode('</p><p>', array_filter($body)) . '</p>';

        return iconv(mb_detect_encoding($body), 'UTF-8', $body);
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
        if (empty($data->SECCION)) {
            return '';
        }

        $category = (string) $data->SECCION;

        return $category;
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
        if (empty($data->FECHA) || empty($data->HORA)) {
            return new \DateTime();
        }

        $date = \DateTime::createFromFormat(
            'd/m/Y H:i:s',
            $data->FECHA . ' ' . $data->HORA,
            new \DateTimeZone('UTC')
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
        if (empty($data->PRIORIDAD)) {
            return 5;
        }

        $priority = (string) $data->PRIORIDAD;

        if (array_key_exists($priority, $this->priorities)) {
            return $this->priorities[$priority];
        }

        if ($priority > 5) {
            return 5;
        }

        return $priority;
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
     * Returns the href from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The resource href.
     */
    public function getHref($data)
    {
        return '';
    }

    /**
     * Returns the unique urn from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     * @param string          The resource type.
     *
     * @return string The resource URN.
     */
    public function getUrn($data, $type = 'text')
    {
        $classname = get_class($this);
        $classname = substr($classname, strrpos($classname, '\\') + 1);

        $resource = strtolower($classname);
        $agency   = 'europapress';

        $date = $this->getCreatedTime($data);
        $id   = $this->getId($data);

        if (!empty($date)) {
            $date = $date->format('YmdHis');
        }

        return "urn:$resource:$agency:$date:$type:$id";
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
        if (empty($data->FOTO)) {
            return null;
        }

        $resource = new ExternalResource();

        $resource->agency_name  = 'EuropaPress';
        $resource->created_time = $this->getCreatedTime($data)
            ->format('Y-m-d H:i:s');
        $resource->extension    = substr($data->FOTO->EXTENSION, 1);
        $resource->file_name    = (string) $data->FOTO->NOMBRE;
        $resource->file_path    = (string) $data->FOTO->NOMBRE;
        $resource->id           = $this->getId($data) . '.photo';
        $resource->image_type   = 'image/' . $resource->extension;
        $resource->title        = (string) $data->FOTO->PIE;
        $resource->summary      = (string) $data->FOTO->PIE;
        $resource->description  = (string) $data->FOTO->PIE;
        $resource->type         = 'photo';
        $resource->urn          = $this->getUrn($data, 'photo');

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $contents = [];

        $resource = new ExternalResource();

        $resource->agency_name  = 'EuropaPress';
        $resource->body         = $this->getBody($data);
        $resource->category     = $this->getCategory($data);
        $resource->created_time = $this->getCreatedTime($data)
            ->format('Y-m-d H:i:s');
        $resource->id           = $this->getId($data);
        $resource->pretitle     = $this->getPretitle($data);
        $resource->priority     = $this->getPriority($data);
        $resource->related      = [];
        $resource->summary      = $this->getSummary($data);
        $resource->tags         = $this->getTags($data);
        $resource->title        = $this->getTitle($data);
        $resource->type         = 'text';
        $resource->urn          = $this->getUrn($data);
        $resource->href         = $this->getHref($data);

        $contents[] = $resource;

        $photo = $this->getPhoto($data);

        if (!empty($photo)) {
            $resource->related[] = $photo->id;

            $contents[] = $photo;
        }

        return $contents;
    }
}
