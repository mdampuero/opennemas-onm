<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Onm\Import\DataSource;

use Onm\Settings as s;
use Onm\Import\DataSource\FormatInterface;
use Onm\Import\DataSource\Format\NewsMLG1Component\Video;
use Onm\Import\DataSource\Format\NewsMLG1Component\Photo;

abstract class FormatAbstract
{

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function toArray()
    {
        $photos = array();
        $photoObjects = $this->getPhotos();
        foreach ($photoObjects as $photo) {
            $photos []= $photo->toArray();
        }

        $videos = array();
        $videoObjects = $this->getVideos();
        foreach ($videoObjects as $video) {
            $videos []= $video->toArray();
        }

        return [
            'id'           => $this->getId(),
            'xml_file'     => $this->xml_file,
            'source_id'    => $this->source_id,
            'urn'          => $this->getUrn(),
            'pretitle'     => $this->getPretitle(),
            'title'        => $this->getTitle(),
            'summary'      => $this->getSummary(),
            'priority'     => $this->getPriority(),
            'tags'         => $this->getTags(),
            'created_time' => $this->getCreatedTime()->format(\DateTime::ISO8601),
            'body'         => $this->getBody(),
            'category'     => $this->getCategory(),
            'agency_name'  => $this->getServiceName(),
            'service_name' => $this->getServicePartyName(),
            'author'       => json_encode($this->getRightsOwner()),
            'author_img'   => $this->getRightsOwnerPhoto(),
            'photos'       => $photos,
            'videos'       => $videos,
            'opennemas'    => $this->getMetadata(),
        ];
    }

    /**
     * Checks if this news component has photos
     *
     * @return boolean
     **/
    public function hasPhotos()
    {
        return count($this->getPhotos()) > 0;
    }

    /**
     * Returns the available photos in this multimedia package
     *
     * @return void
     **/
    public function getPhotos()
    {
        return [];
    }

    /**
     * Checks if this news component has photos
     *
     * @return boolean
     **/
    public function hasVideos()
    {
        return count($this->getVideos()) > 0;
    }

    /**
     * Returns the available images in this multimedia package
     *
     * @return void
     **/
    public function getVideos()
    {
        return [];
    }

    /**
     * Returns the element internal metadata
     *
     * @return array the element internal metadata
     **/
    public function getMetaData()
    {
        return array();
    }

    /**
     * Returns the author internal metadata
     *
     * @return array the author info
     **/
    public function getRightsOwner()
    {
        return array();
    }

    /**
     * Returns the internal author photo
     *
     * @return array the author photo url
     **/
    public function getRightsOwnerPhoto()
    {
        return array();
    }

    /**
     * Finds a regexp inside the title and content
     *
     * @return boolean
     **/
    public function hasContent($needle)
    {
        $needle = strtolower(\Onm\StringUtils::normalize($needle));
        $title = strtolower(\Onm\StringUtils::normalize($this->title));

        if (preg_match("@".$needle."@", $title)) {
            return true;
        }
        $body = strtolower(\Onm\StringUtils::normalize($this->body));
        if (preg_match("@".$needle."@", $body)) {
            return true;
        }

        return false;
    }
}
