<?php
/**
 * Handles UserGroup CRUD actions.
 *
 * @package    Model
 **/
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
 * @package    Model
 **/
class UserGroup
{
    /**
     * The user group id
     *
     * @var int
     **/
    public $id = null;

    /**
     * The user group name
     *
     * @var string
     **/
    public $name = null;

    /**
     * List of privileges for this user group
     *
     * @var array
     **/
    public $privileges = null;

    /**
     * Loads the user group information given its id
     *
     * @param int $id the user group id to load
     *
     * @return UserGroup the user group object instance
     **/
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Creates a new user group given its information
     *
     * @param array $data the user group data
     *
     * @return boolean true if all went well
     **/
    public function create($data)
    {
        $sql = "INSERT INTO user_groups (`name`) VALUES (?)";
        $values = array($data['name']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
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

    /**
     * Loads the user group information given its id
     *
     * @param int $id the user group id to load
     *
     * @return UserGroup the user group object instance
     **/
    public function read($id)
    {
        $sql = 'SELECT * FROM user_groups WHERE pk_user_group=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return;
        }
        $this->load($rs->fields);

        //Se cargan los privileges asociados
        $sql =  "SELECT pk_fk_privilege"
                ." FROM user_groups_privileges"
                ." WHERE pk_fk_user_group = ?";
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            return;
        }
        while (!$rs->EOF) {
            $this->privileges[] = $rs->fields['pk_fk_privilege'];
            $rs->MoveNext();
        }

        return $this;
    }

    /**
     * Updates the user group information given an array
     *
     * @param array $data the data to update the user group
     *
     * @return boolean true if the data was saved
     **/
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

    /**
     * Deletes an user group and privilege assigned given its id
     *
     * @param int $id the user group id to delete
     *
     * @return boolean true if the user group was deleted properly
     **/
    public function delete($id)
    {
        $sql = 'DELETE FROM user_groups WHERE pk_user_group=?';

        $values = array($id);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            return false;
        }

        return $this->deletePrivileges($id);
    }

    /**
     * Returns the user group name given an user group id
     *
     * @param int $id the user group id
     *
     * @return string the name of the group
     **/
    public static function getGroupName($id)
    {
        $sql = 'SELECT name FROM user_groups WHERE pk_user_group=?';
        $rs = $GLOBALS['application']->conn->GetOne($sql, $id);

        return $rs;
    }

    /**
     * Returns all the user groups in the system, excluding the Master group
     *
     * @return array a list of UserGroup objects
     **/
    public function find()
    {
        $userGroups = array();

        $rs = $GLOBALS['application']->conn->Execute(
            "SELECT pk_user_group, name FROM user_groups WHERE name <>'Masters'"
        );

        while (!$rs->EOF) {
            $userGroup = new UserGroup();
            $userGroup->load($rs->fields);
            $userGroups[] = $userGroup;
            $rs->MoveNext();
        }

        return $userGroups;
    }

    /**
     * Whether the group contains a privilege given the privilege id
     *
     * @param int $privilegeID the privilege ID to check for
     *
     * @return boolean true if the group has the privilege
     **/
    public function containsPrivilege($privilegeID)
    {
        if (isset($this->privileges)) {
            return in_array(intval($privilegeID), $this->privileges);
        }

        return false;
    }

    /**
     * Assign the privileges to a group given an array of privilege ids
     *
     * @param array $privilegeIds a list of privileges to assign to this group
     *
     * @return boolean true if all went well
     **/
    private function insertPrivileges($privilegeIds)
    {
        foreach ($privilegeIds as $privilegeId) {
            $rs = $GLOBALS['application']->conn->Execute(
                "INSERT INTO user_groups_privileges
                (`pk_fk_user_group`, `pk_fk_privilege`)
                VALUES (?,?)",
                array($this->id, $privilegeId)
            );
            if (!$rs) {
                return false;
            }
        }

        return true;
    }

    /**
     * Removes the privilege assigned to the user group
     *
     * @param int $id the privilege id to remove
     *
     * @return boolean true if the privilege was removed
     **/
    private function deletePrivileges($id)
    {
        $sql = 'DELETE FROM user_groups_privileges WHERE pk_fk_user_group=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));
        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Hydrates the object given an array with the new data
     *
     * @param array $data the data to load into the user group object
     *
     * @return UserGroup the user group object instance with the new information
     **/
    public function load($data)
    {
        $this->id   = $data['pk_user_group'];
        $this->name = $data['name'];

        return $this;
    }
}
