<?php

class ArticleRest
{
    public $restler;

    /*
    * @url GET /articleRest/id/:id
    */
    function id ($n1 = 1)
    {
        $this->_validateInt(func_get_args());

        $article[] = new Article($n1);

        return $article;
    }

    /*
    * @url GET /articleRest/dayrange/:dayrange
    */
    function dayRange ($n1)
    {

        $cm = new ContentManager();

        $article = $cm->find('Article',
                             'fk_content_type=1 AND available=1 AND '.
                             'created >=DATE_SUB(CURDATE(), INTERVAL ' . $n1 . ' DAY)  ',
                             ' ORDER BY created DESC');

        return $article;
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
