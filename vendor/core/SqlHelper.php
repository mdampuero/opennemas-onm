<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * SqlHelper
 *
 * @package    Onm
 * @subpackage Utils
 **/
class SqlHelper
{
    /**
     * Build "update" query using $fields array to write "set" sentence
     *
     * @see SqlHelper::bindAndUpdate
     * @param string      $table
     * @param array       $fields Array with name of fields to update
     * @param string      $filter String for where sentece
     * @param object|null $conn   ADOConnection instance
     */
    public static function update($table, $fields, $filter, $conn = null)
    {
        $sql = 'UPDATE `%s` SET %s WHERE %s';

        $set = array();
        $values = array();
        foreach ($fields as $k => $field) {
            $set[]    = '`' . $k . '` = ?';
            $values[] = $field;
        }

        $sql = sprintf($sql, $table, implode(', ', $set), $filter);

        $conn = (!is_null($conn))? $conn: $GLOBALS['application']->conn;
        if ($conn->Execute($sql, $values) === false) {
            $errorMsg = $conn->ErrorMsg();

            throw new Exception($errorMsg);
        }
    }

    /**
     * Search into $data values that match with keys into $fields to build
     * new array for use SqlHelper::update
     * Also check if values isset and not empty
     *
     * <code>
     *  $filter = '`pk_content` = ' . $pk_content;
     *  $fields = array(
     *      'starttime', 'endtime', 'content_status', 'available',
     *      'fk_user_last_editor', 'frontpage', 'in_home', 'permalink'
     *  );
     *  SqlHelper::bindAndUpdate('contents', $fields, $_POST, $filter);
     * </code>
     *
     * @uses SqlHelper::update
     * @param string      $table
     * @param array       $fields Array with name of fields to update
     * @param array       $data   Array keyField => valueField, equals to POST
     * @param string      $filter String for where sentece
     * @param object|null $conn   ADOConnection instance
     */
    public static function bindAndUpdate(
        $table,
        $fields,
        $data,
        $filter,
        $conn = null
    ) {
        $merged = array();
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $merged[ $field ] = $data[$field];
            }
        }

        SqlHelper::update($table, $merged, $filter, $conn);
    }

    /**
     * Build "insert" query using $fields array to write "values" sentence
     *
     * @see SqlHelper::bindAndUpdate
     * @param string      $table
     * @param array       $fields Array with name of fields to update
     * @param object|null $conn   ADOConnection instance
     */
    public function insert($table, $fields, $conn = null)
    {
        $sql = 'INSERT INTO `%s` (%s) VALUES (%s)';

        $set = array();
        $values = array();
        foreach ($fields as $k => $field) {
            $set[]    = '`' . $k . '`';
            $values[] = $field;
        }

        $marks = implode(', ', array_fill(0, count($set), '?'));
        $sql = sprintf($sql, $table, implode(', ', $set), $marks);

        $conn = (!is_null($conn))? $conn: $GLOBALS['application']->conn;
        if ($conn->Execute($sql, $values) === false) {
            $errorMsg = $conn->ErrorMsg();

            throw new Exception($errorMsg);
        }
    }

    /**
     * Search into $data values that match with keys into $fields to build
     * new array for use SqlHelper::insert
     * Also check if values isset and not empty
     *
     * <code>
     *  $fields = array(
     *      'starttime', 'endtime', 'content_status', 'available',
     *       'fk_user_last_editor', 'frontpage', 'in_home', 'permalink'
     *  );
     *  SqlHelper::bindAndInsert('contents', $fields, $_POST);
     * </code>
     *
     * @uses SqlHelper::insert
     * @param string      $table
     * @param array       $fields Array with name of fields to update
     * @param array       $data   Array keyField => valueField, equals to POST
     * @param object|null $conn   ADOConnection instance
     */
    public function bindAndInsert($table, $fields, $data, $conn = null)
    {
        $merged = array();
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $merged[ $field ] = $data[$field];
            }
        }

        SqlHelper::insert($table, $merged, $conn);
    }
}
