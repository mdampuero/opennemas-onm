<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService\Handlers;

/**
 * Handles REST actions for instances.
 *
 * @package WebService
 **/
class Instances
{
    public $restler;
    private $mailer;

    /**
     * @url GET /instances/name/
     **/
    public function name()
    {
        return INSTANCE_UNIQUE_NAME;
    }

    /**
     * @url GET /instances/mediaurl/
     **/
    public function mediaUrl()
    {
        return MEDIA_IMG_PATH_WEB;
    }

    /**
     * @url GET /instances/siteurl/
     **/
    public function siteUrl()
    {
        return SITE_URL;
    }

    /**
     * @url GET /instances/mediaimgpath/
     **/
    public function mediaImgPath()
    {
        return MEDIA_IMG_PATH;
    }

    /**
     * @url GET /instances/instancemedia/
     **/
    public function instanceMedia()
    {
        return INSTANCE_MEDIA;
    }
}
