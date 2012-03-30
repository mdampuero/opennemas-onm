<?php

class CategoryRest
{
    public $restler;

    /*
    * @url GET /articleRest/id/:id
    */
    function id ($n1)
    {
        $this->_validateInt(func_get_args());

        $cm = new ContentManager();
        $categoryContents = $cm->getContentsForHomepageOfCategory($n1);

        return $categoryContents;
    }

    private function _validateInt ($number)
    {
        foreach ($number as $value) {
            if (!is_numeric($value)) {
                throw new RestException(400, 'parameter is not a number');
            }
            if (is_infinite($value)) {
                throw new RestException(400, 'parameter is not finite');
            }
        }
    }

}
