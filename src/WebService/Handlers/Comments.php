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
 * Handles REST actions for comments.
 *
 * @package WebService
 **/
class Comments
{
    public $restler;

    /*
    * @url GET /comments/count/:id
    */
    public function count($id)
    {
        $this->validateInt(func_get_args());

        $sql = 'SELECT count(pk_comment)
                FROM comments, contents
                WHERE comments.fk_content = ?
                    AND content_status=1
                    AND in_litter=0
                    AND pk_content=pk_comment';
        $rs = $GLOBALS['application']->conn->GetOne($sql, array($id));

        return intval($rs);
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
