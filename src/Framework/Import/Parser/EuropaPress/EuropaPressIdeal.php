<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Framework\Import\Parser\EuropaPress;

use Framework\Import\Resource\Resource;

/**
 * Parses XML files in custom Europapress format.
 */
class EuropaPressIdeal extends EuropaPress
{

    /**
     * {@inheritdoc}
     */
    public function checkFormat($data)
    {
        if (is_object($data) &&
            $data->CODIGO->count() > 0 &&
            (
                !empty($data->FIRMA2) ||
                !empty($data->FOTOP)
            )
        ) {
            return true;
        }

        return false;
    }



    /**
     * Returns the title from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return string The resource title.
     */
    public function getSignature($data)
    {
        if (empty($data->FIRMA2)) {
            return '';
        }

        $signature = (string) $data->FIRMA2;

        return iconv(mb_detect_encoding($signature), "UTF-8", $signature);
    }

    /**
     * Returns the photo from the parsed data.
     *
     * @param SimpleXMLObject The parsed data.
     *
     * @return array The photo data.
     */
    public function getPhotoFront($data)
    {
        if (empty($data->FOTOP)) {
            return null;
        }

        $resource = new Resource();

        $resource->agency_name  = 'Grupo Idealgallego';
        $resource->created_time = $this->getCreatedTime($data)
            ->format('Y-m-d H:i:s');
        $resource->extension    = substr($data->FOTOP->EXTENSION, 1);
        $resource->file_name    = (string) $data->FOTOP->NOMBRE;
        $resource->file_path    = (string) $data->FOTOP->NOMBRE;
        $resource->id           = $this->getId($data) . 'front_ig.photo';
        $resource->image_type   = 'image/' . $resource->extension;
        $resource->title        = (string) $data->FOTOP->PIE;
        $resource->summary      = (string) $data->FOTOP->PIE;
        $resource->description  = (string) $data->FOTOP->PIE;
        $resource->type         = 'photo';
        $resource->urn          = $this->getUrn($data, 'photo') . 'front';

        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($data)
    {
        $contents = [];

        $resource = new Resource();

        $resource->signature    = $this->getSignature($data);
        $resource->agency_name  = 'Grupo Idealgallego';
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

        $contents[] = $resource;

        $photoFront = $this->getPhotoFront($data);

        if (!empty($photoFront)) {
            $resource->related[] = $photoFront->id;

            $contents[] = $photoFront;
        }

        $photoInner = $this->getPhoto($data);

        if (!empty($photoInner)) {
            $resource->related[] = $photoInner->id;

            $contents[] = $photoInner;
        }


        return $contents;
    }
}
