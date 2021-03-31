<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Luracast\Restler\RestException;

/**
 * Handles REST actions for authors.
 *
 * @package WebService
 */
class Authors
{
    /*
    * @url GET /authors/id/:id
    */
    public function id($id = null)
    {
        $this->validateInt($id);

        try {
            $author = getService('api.service.author')->getItem($id);

            return $author;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
            return false;
        }
    }

    /*
    * @url GET /authors/photo/:id
    */
    public function photo($id = null)
    {
        $this->validateInt($id);

        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT `avatar_img_id` FROM users WHERE id = ?',
                [ $id ]
            );

            // Get photo object from avatar_img_id
            $photo = getService('api.service.photo')->getItem($rs['avatar_img_id']);

            return $photo;
        } catch (\Exception $e) {
            getService('error.log')->error($e->getMessage());
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
