<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Handles UserGroup CRUD actions.
 *
 * @package    Onm
 * @subpackage Model
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class UserGroup
{

    /**Id del grupo*/
    public $id         = null;

    /**Nombre del grupo*/
    public $name       = null;

    /**Lista de permisos activos para este grupo de usuarios*/
    public $privileges = null;

    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    public function create($data)
    {
        // Se inserta el grupo
        $sql = "INSERT INTO user_groups (`name`) VALUES (?)";
        $values = array($data['name']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }
        $this->id = $GLOBALS['application']->conn->Insert_ID();
        $this->name = $data['name'];

        //Se insertan los privilegios
        if (!is_null($data['privileges'])
            && count($data['privileges'] > 0)
        ) {
            return $this->insertPrivileges($data['privileges']);
        }

        return true;
    }

    public function read($id)
    {
        $sql = 'SELECT * FROM user_groups WHERE pk_user_group=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }
        $this->load($rs->fields);

        //Se cargan los privileges asociados
        $sql =  "SELECT pk_fk_privilege"
                ." FROM user_groups_privileges"
                ." WHERE pk_fk_user_group = ?";
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }
        while (!$rs->EOF) {
            $this->privileges[] = $rs->fields['pk_fk_privilege'];
            $rs->MoveNext();
        }
    }

    public function update($data)
    {
        if (!is_null($data['id'])) {
            $this->id = $data['id'];

            $sql = "UPDATE user_groups
                    SET `name`=?
                    WHERE pk_user_group=?";
            $values = array(
                $data['name'],
                $data['id']
            );

            $rs = $GLOBALS['application']->conn->Execute($sql, $values);

            if (!$rs) {
                \Application::logDatabaseError();

                return false;
            }

            // Se actualizan los privileges
            $this->deletePrivileges($data['id']);

            if (!is_null($data['privileges'])
                && count($data['privileges'] > 0)
            ) {
                //print 'Insertamos los privilegios';
                return $this->insertPrivileges($data['privileges']);
            }
        }

        return false;
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM user_groups WHERE pk_user_group=?';

        $values = array($id);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }

        // Se eliminan las referencias de los privileges
        $this->deletePrivileges($id);
    }

    public static function getGroupName($userGroupFK)
    {
        $sql = 'SELECT name FROM user_groups WHERE pk_user_group=?';
        $rs = $GLOBALS['application']->conn->GetOne($sql, $userGroupFK);

        return $rs;
    }

    public function find()
    {
        $types = array();
        $sql = "SELECT pk_user_group, name "
             . "FROM user_groups WHERE name <>'Masters'";
        $rs = $GLOBALS['application']->conn->Execute($sql);
        while (!$rs->EOF) {
            $userGroup = new UserGroup();
            $userGroup->set_values($rs->fields);
            $types[] = $userGroup;
            $rs->MoveNext();
        }

        return ($types);
    }

    public function containsPrivilege($privilegeID)
    {
        if (isset($this->privileges)) {
            return in_array(intval($privilegeID), $this->privileges);
        }

        return false;
    }

    private function insertPrivileges($data)
    {
        $sql =  "INSERT INTO user_groups_privileges"
                ."            (`pk_fk_user_group`, `pk_fk_privilege`)"
                ." VALUES (?,?)";
        for ($i = 0; $i < count($data); $i++) {
            $values = array($this->id, $data[$i]);

            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if (!$rs) {
                \Application::logDatabaseError();

                return (false);
            }
        }

        return true;
    }

    private function deletePrivileges($id)
    {
        $sql = 'DELETE FROM user_groups_privileges WHERE pk_fk_user_group=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));
        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }
    }

    private function load($data)
    {
        $this->id   = $data['pk_user_group'];
        $this->name = $data['name'];
    }
}
