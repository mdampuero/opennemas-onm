<?php

class Contents
{
    public $restler;

    /*
    * @url GET /contents/resolve/:id
    */
    function resolve ($id)
    {
        $this->_validateInt($id);

        $refactorID = Content::resolveID($id);

        return $refactorID;
    }

    /*
    * @url GET /contents/contenttype/:contentId
    */
    function contentType ($contentID)
    {

        $this->_validateInt($contentID);

        $sql = "SELECT name FROM content_types WHERE `pk_content_type`=$contentID";
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if($rs->_numOfRows < 1) {
            $return_value = false;
        } else {
            $return_value = ucfirst($rs->fields['name']);
        }

        return $return_value;
    }

    private function _validateInt ($number)
    {
        if (!is_numeric($number)) {
            throw new RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new RestException(400, 'parameter is not finite');
        }
    }

}
