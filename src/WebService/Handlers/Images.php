<?php

class Images
{
    public $restler;

    /*
    * @url GET /images/id/:id
    */
    public function id($id)
    {
        $this->validateInt(func_get_args());

        $image = new Photo($id);

        return $image;
    }

    private function validateInt($number)
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
