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
 * Handles REST actions for authors.
 *
 * @package WebService
 **/
class Authors
{
    public $restler;

    /*
    * @url GET /authors/id/:id
    */
    public function id($id = null)
    {
        $this->validateInt($id);

        $ur = getService('user_repository');

        $author = $ur->find($id);

        return $author;
    }

    /*
    * @url GET /authors/photo/:id
    */
    public function photo($id = null)
    {
        $this->validateInt($id);

        $sql = 'SELECT `avatar_img_id` FROM users WHERE id = ?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return false;
        }

        // Get photo object from avatar_img_id
        $er = getService('entity_repository');
        $photo = $er->find('Photo', $rs->fields['avatar_img_id']);

        return $photo;
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
