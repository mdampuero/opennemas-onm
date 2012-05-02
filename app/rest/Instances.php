<?php

class Instances
{
    public $restler;

    /*
    * @url GET /instances/name/
    */
    function name ()
    {
        return INSTANCE_UNIQUE_NAME;
    }

    /*
    * @url GET /instances/mediaurl/
    */
    function mediaUrl ()
    {
        return MEDIA_IMG_PATH_WEB;
    }

    /*
    * @url GET /instances/sitepath/
    */
    function siteUrl ()
    {
        return SITE_URL;
    }

    /*
    * @url GET /instances/mediaimgpath/
    */
    function mediaImgPath ()
    {
        return MEDIA_IMG_PATH;
    }

}
