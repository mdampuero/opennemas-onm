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

        $author = new Author($id);
        $author->get_author_photos();

        return $author;
    }

    /*
    * @url GET /authors/photo/:id
    */
    public function photo($id = null)
    {
        $this->validateInt($id);

        // Fetch photo images for this author
        $sql = 'SELECT * FROM author_imgs WHERE fk_author = ? ORDER BY pk_img ASC';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            Application::logDatabaseError();
            return;
        }

        $i = 0;
        $photos = array();
        while (!$rs->EOF) {
            $photos[$i] = new stdClass();

            $photos[$i]->pk_img      = $rs->fields['pk_img'];
            $photos[$i]->fk_author   = $rs->fields['fk_author'];
            $photos[$i]->fk_photo    = $rs->fields['fk_photo'];
            $photos[$i]->path_img    = $rs->fields['path_img'];
            $photos[$i]->path_file   = $rs->fields['path_img'];
            $photos[$i]->description = $rs->fields['description'];

            $i++;
            $rs->MoveNext();
        }

        if (!empty($photos)) {
            return $photos;
        }

        return null;
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

