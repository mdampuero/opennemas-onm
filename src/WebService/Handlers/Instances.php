<?php

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
