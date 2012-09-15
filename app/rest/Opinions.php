<?php

class Opinions
{
    public $restler;

    /*
    * @url GET /opinions/id/:id
    */
    public function id($id)
    {
        $this->_validateInt(func_get_args());

        $opinion = new Opinion($id);

        return $opinion;
    }

    private function _validateInt($number)
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
