<?php
/**
 * Defines the Onm\Rest\RestBase
 *
 * This file is part of the onm package.
 * (c) 2009-2013 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package Onm_Rest
 **/
namespace Onm\Rest;

use Luracast\Restler\RestException;

/**
 * Base class for the rest handlers
 *
 * @package Onm_Rest
 **/
abstract class RestBase
{
    /**
     * Validates a finite number
     *
     * This is used for checking the int parameters
     *
     * @param int $number the number to validate
     *
     * @return void
     */
    private function validateInt($number)
    {
        if (!is_numeric($number)) {
            throw new RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new RestException(400, 'parameter is not finite');
        }
    }

    /**
     * Private function for validating url paramaters
     *
     * Validates a url parameter
     *
     * This is used for checking the url parameters
     *
     * @return void
     */
    private function invalidUrlParam()
    {
        throw new RestException(400, 'parameter is not valid');
    }
}
