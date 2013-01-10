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
    * @url POST /instances/create/
    */
    protected function postCreate(
        $site_name,
        $internal_name,
        $domains,
        $user_name,
        $password,
        $contact_mail,
        $contact_IP,
        $timezone,
        $token
    ) {
        //Force internal_name lowercase
        $internalName = strtolower($internal_name);

        //If is creating a new instance, get DB params on the fly
        $internalNameShort = trim(substr($internalName, 0, 11));

        $settings = array(
            'TEMPLATE_USER' => "default",
            'MEDIA_URL'     => "http://media.opennemas.com",
            'BD_TYPE'       => "mysqli",
            'BD_HOST'       => "localhost",
            'BD_USER'       => $internalNameShort,
            'BD_PASS'       => \Onm\StringUtils::generatePassword(16),
            'BD_DATABASE'   => $internalNameShort,
            'TOKEN'         => $token
        );

        $date = new DateTime();
        $date->setTimezone(new DateTimeZone("UTC"));

        //Get all the Post data
        $data = array(
            'contact_IP'    => $contact_IP,
            'name'          => $site_name,
            'user_name'     => $user_name,
            'user_mail'     => $contact_mail,
            'user_pass'     => $password,
            'internal_name' => $internalName,
            'domains'       => $domains,
            'activated'     => 1,
            'settings'      => $settings,
            'site_created'  => $date->format('Y-m-d H:i:s'),
        );

        // Also get timezone if comes from openhost form
        if (!empty ($timezone)) {
            $allTimezones = \DateTimeZone::listIdentifiers();
            foreach ($allTimezones as $key => $value) {
                if ($timezone == $value) {
                    $data['timezone'] = $key;
                }
            }
        }

        $errors = array();
        $im = Onm\Instance\InstanceManager::getInstance();
        // Check for reapeted internalnameshort and if so, add a number at the end
        $data = $im->checkInternalShortName($data);
        $errors = $im->create($data);
        return $im->create($data);
    }

    /*
    * @url POST /instances/checkinstancename/
    */
    protected function postCheckInstanceName($url)
    {
        $im = Onm\Instance\InstanceManager::getInstance();
        return $im->checkInstanceExists($url);
    }

    /*
    * @url POST /instances/checkmailinuse/
    */
    protected function postCheckMailInUse($email)
    {
        $im = Onm\Instance\InstanceManager::getInstance();
        return $im->checkMailExists($email);
    }
}
