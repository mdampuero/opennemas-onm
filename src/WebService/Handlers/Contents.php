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
 * Handles REST actions for contents.
 *
 * @package WebService
 **/
class Contents
{
    public $restler;

    /*
    * @url GET /contents/resolve/:id
    */
    public function resolve($id)
    {
        $this->validateInt($id);

        $refactorID = \Content::resolveID($id);

        return $refactorID;
    }

    /*
    * @url GET /contents/read/:contentID
    */
    public function read($contentID)
    {
        $this->validateInt($contentID);

        $content = new \Content($contentID);
        $content = $content->get($contentID);

        return serialize($content);
    }

    /*
    * @url GET /contents/contenttype/:contentId
    */
    public function contentType($contentID)
    {
        $this->validateInt($contentID);

        $returnValue = \ContentManager::getContentTypeNameFromId($contentID, true);

        return $return_value;
    }

    /*
    * @url GET /contents/filePath/:contentId
    */
    public function filePath($contentID)
    {
        $this->validateInt($contentID);

        $sql = "SELECT path FROM attachments WHERE `pk_attachment`=?";
        $rs  = $GLOBALS['application']->conn->Execute($sql, $contentID);

        if ($rs->_numOfRows < 1) {
            $returnValue = false;
        } else {
            $returnValue = $rs->fields['path'];
        }

        return $returnValue;
    }

    /*
    * @url GET /contents/loadcategoryname/:contentId
    */
    public function loadCategoryName($id)
    {
        $ccm = \ContentCategoryManager::get_instance();

        $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';

        $rs = $GLOBALS['application']->conn->GetOne($sql, $id);

        return $ccm->getName($rs);

    }

    /*
    * @url GET /contents/loadcategorytitle/:contentId
    */
    public function loadCategoryTitle($id)
    {
        $ccm = \ContentCategoryManager::get_instance();

        $sql = 'SELECT pk_fk_content_category FROM `contents_categories` WHERE pk_fk_content =?';

        $rs = $GLOBALS['application']->conn->GetOne($sql, $id);

        return $ccm->getTitle($rs);
    }

    /*
    * @url GET /contents/setnumviews/:contentId
    */
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
        if (is_array($id)) {
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
                    .'WHERE `content_status`=1 AND `pk_content`='.$id;
        }

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            return false;
        }
    }

    private function validateInt($number)
    {
        if (!is_numeric($number)) {
            throw new \RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new \RestException(400, 'parameter is not finite');
        }
    }
}
