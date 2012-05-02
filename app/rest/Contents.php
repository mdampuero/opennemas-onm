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
