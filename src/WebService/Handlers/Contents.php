<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace WebService\Handlers;

use Luracast\Restler\RestException;

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

        preg_match("@(?P<date>\d{14})(?P<id>\d{6,})@", $id, $matches);

        // Get real content id
        $refactorID = 0;
        if (array_key_exists('id', $matches) &&
            array_key_exists('date', $matches) &&
            (
                substr($matches['id'], 0, -6) === '' ||
                substr((int)$matches['id'], 0, -6) > 0
            )
        ) {
            $refactorID = (int) $matches['id'];
        }

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

        return $returnValue;
    }

    /*
    * @url GET /contents/filePath/:contentId
    */
    public function filePath($contentID)
    {
        $this->validateInt($contentID);

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                "SELECT path FROM attachments WHERE `pk_attachment`=?",
                [ $contentID ]
            );

            if (count($rs) < 1) {
                $returnValue = false;
            } else {
                $returnValue = $rs['path'];
            }

            return $returnValue;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /*
    * @url GET /contents/loadcategoryname/:contentId
    */
    public function loadCategoryName($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT name FROM `contents_categories`,`content_categories` '
                .'WHERE pk_fk_content_category = pk_content_category AND pk_fk_content =?',
                [ $id ]
            );

            if (count($rs) < 1) {
                $returnValue = false;
            } else {
                $returnValue = $rs['name'];
            }

            return $returnValue;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /*
    * @url GET /contents/loadcategorytitle/:contentId
    */
    public function loadCategoryTitle($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT title FROM `contents_categories`,`content_categories` '
                .'WHERE pk_fk_content_category = pk_content_category AND pk_fk_content =?',
                [ $id ]
            );

            if (count($rs) < 1) {
                $returnValue = false;
            } else {
                $returnValue = $rs['title'];
            }

            return $returnValue;
        } catch (\Exception $e) {
            error_log($e->getMessage());
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
