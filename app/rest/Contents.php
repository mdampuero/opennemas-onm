<?php

class Contents
{
    public $restler;

    /*
    * @url GET /contents/resolve/:id
    */
    public function resolve($id)
    {
        $this->validateInt($id);

        $refactorID = Content::resolveID($id);

        return $refactorID;
    }

    /*
    * @url GET /contents/contenttype/:contentId
    */
    public function contentType($contentID)
    {

        $this->validateInt($contentID);

        $sql = "SELECT name FROM content_types WHERE `pk_content_type`=$contentID";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs->_numOfRows < 1) {
            $return_value = false;
        } else {
            $return_value = ucfirst($rs->fields['name']);
        }

        return $return_value;
    }

    public function setNumViews($id = null)
    {

        if (!array_key_exists('HTTP_USER_AGENT', $_SERVER)
            && empty($_SERVER['HTTP_USER_AGENT'])
        ) {
            return false;
        }

        $botStrings = array(
            "google",
            "bot",
            "msnbot",
            "facebookexternal",
            "yahoo",
            "spider",
            "archiver",
            "curl",
            "python",
            "nambu",
            "twitt",
            "perl",
            "sphere",
            "PEAR",
            "java",
            "wordpress",
            "radian",
            "crawl",
            "yandex",
            "eventbox",
            "monitor",
            "mechanize",
        );

        $httpUserAgent = preg_quote($_SERVER['HTTP_USER_AGENT']);

        foreach ($botStrings as $bot) {
            if (stristr($httpUserAgent, $bot) != false) {
                return false;
            }
            // if (preg_match("@".strtolower($httpUserAgent)."@", $bot) > 0) {
            //     return false;
            // }
        }

        if (is_null($id) || empty($id)) {
            return false;
        }

        // Multiple exec SQL
        if (is_array($id) ) {
            $ads = array();

            if (count($id)>0) {
                foreach ($id as $item) {
                    if (is_object($item)
                       && isset($item->pk_advertisement)
                       && !empty($item->pk_advertisement)
                    ) {
                        $ads[] = $item->pk_advertisement;
                    }
                }
            }
            if (empty($ads)) {
                return false;
            }

            $sql =  'UPDATE `contents` SET `views`=`views`+1'
                    .' WHERE  `pk_content` IN ('.implode(',', $ads).')';

        } else {
            $sql =  'UPDATE `contents` SET `views`=`views`+1 '
                    .'WHERE `available`=1 AND `pk_content`='.$id;
        }

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            Application::logDatabaseError();

            return false;
        }
    }

    private function validateInt($number)
    {
        if (!is_numeric($number)) {
            throw new RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new RestException(400, 'parameter is not finite');
        }
    }
}

