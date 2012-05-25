<?php

/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
/**
 * Handles all the CRUD actions over newsletter.
 *
 * @package    Onm
 * @subpackage Model
 *
 **/

class NewNewsletter
{

    /**
     * Initializes the newsletter for a given id.
     *
     * @param string $id the content id to initilize.
     **/
    public function __construct($id=null)
    {
        $this->cache = new MethodCacheManager($this, array('ttl' => 300));

        if (!is_null($id)) {
            return $this->read($id);
        }
    }

    /**
     * Creates one newsletter given an array of data
     *
     * @param array $data array with data for saved
     *
     * @return void
     **/
    public function create($data)
    {

        $data['created'] = date("Y-m-d H:i:s");

        $sql = 'INSERT INTO `newsletter_archive` (`data`, `html`, `created`)'
             . ' VALUES (?,?,?)';

        $values = array($data['content'], $data['html'], $data['created']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();
        $this->read($this->id);

        return $this;
    }

    /**
     * Loads the data for an newsletter given its id
     *
     * @param  $id
     *
     * @return void
     **/

    public function read($id)
    {
        $sql = 'SELECT * FROM `newsletter_archive` WHERE pk_newsletter=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->loadData($rs->fields);

        return $this;
    }

    public function loadData($fields)
    {
        $this->id             = $fields['pk_newsletter'];
        $this->pk_newsletter  = $fields['pk_newsletter'];
        $this->data           = $fields['data'];
        $this->created        = $fields['created'];
        $this->html           = $fields['html'];
    }

    /**
     * Loads the data for some newsletters
     *
     * @param string $where clausule with condition  for sql execute.
     *
     * @return void
     **/

     public function search($where = '1=1')
    {
        $sql = 'SELECT * FROM `newsletter_archive` WHERE '.$where;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $newsletters = array();
        while (!$rs->EOF) {
            $obj = new NewNewsletter();
            $obj->loadData($rs->fields);

            $newsletters[] = $obj;

            $rs->MoveNext();
        }

        return $newsletters;
    }


}
