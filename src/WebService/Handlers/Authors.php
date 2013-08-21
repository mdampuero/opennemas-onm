<?php

class Authors
{
    public $restler;

    /*
    * @url GET /authors/id/:id
    */
    public function id($id = null)
    {
        $this->validateInt($id);

        $author = new User($id);

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
        $photo = new Photo($rs->fields['avatar_img_id']);

        return $photo;
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
