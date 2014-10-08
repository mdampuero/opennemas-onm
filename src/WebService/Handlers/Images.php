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
 * Handles REST actions for images.
 *
 * @package WebService
 **/
class Images
{
    public $restler;

    /*
    * @url GET /images/id/:id
    */
    public function id($id)
    {
        $this->validateInt(func_get_args());

        $er = getService('entity_repository');
        $image = $er->find('Photo', $id);

        return $image;
    }

    private function validateInt($number)
    {
        foreach ($number as $value) {
            if (!is_numeric($value)) {
                throw new \RestException(400, 'parameter is not a number');
            }
            if (is_infinite($value)) {
                throw new \RestException(400, 'parameter is not finite');
            }
        }
    }
}
