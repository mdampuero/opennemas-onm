<?php

class Instances extends RestBase
{
    public $restler;

    /*
    * @url GET /instances/name/
    */
    public function name()
    {
        return INSTANCE_UNIQUE_NAME;
    }

    /*
    * @url GET /instances/mediaurl/
    */
    public function mediaUrl()
    {
        return MEDIA_IMG_PATH_WEB;
    }

    /*
    * @url GET /instances/sitepath/
    */
    public function siteUrl()
    {
        return SITE_URL;
    }

    /*
    * @url GET /instances/mediaimgpath/
    */
    public function mediaImgPath()
    {
        return MEDIA_IMG_PATH;
    }

    /*
    * @url GET /instances/instancemedia/
    */
    public function instanceMedia()
    {
        return INSTANCE_MEDIA;
    }

    /*
    * @url POST /instances/checkinstancename/
    */
    protected function postCheckInstanceName($key,$url) {
        $im = Onm\Instance\InstanceManager::getInstance();
        return $im->checkInstanceExists($url);
    }

    /*
    * @url POST /instances/checkmailinuse/
    */
    protected function postCheckMailInUse($key,$email) {
        $im = Onm\Instance\InstanceManager::getInstance();
        return $im->checkMailExists($email);
    }
}

