<?php
/**
 * Handles UserGroup CRUD actions.
 *
 * @package    Model
 */
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
 */
class UserGroup
{
    /**
     * The user group id
     *
     * @var int
     */
    public $id = null;

    /**
     * The user group name
     *
     * @var string
     */
    public $name = null;

    /**
     * List of privileges for this user group
     *
     * @var array
     */
    public $privileges = null;

    /**
     * Loads the user group information given its id
     *
     * @param int $id the user group id to load
     *
     * @return UserGroup the user group object instance
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            return $this->read($id);
        }
    }

    /**
     * Creates a new user group given its information
     *
     * @param array $data the user group data
     *
     * @return boolean true if all went well
     */
    public function create($data)
    {
        $conn = getService('dbal_connection');
        try {
            $conn->beginTransaction();
            $rs = $conn->insert(
                "user_groups",
                [ 'name' => $data['name'] ]
            );

            $this->id = (int) getService('dbal_connection')->lastInsertId();
            $this->name = $data['name'];

            //Se insertan los privilegios
            if (!is_null($data['privileges'])
                && count($data['privileges'] > 0)
            ) {
                $this->insertPrivileges($data['privileges']);
            }

            $conn->commit();

            dispatchEventWithParams('usergroup.create', array('usergroup' => $this));

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Loads the user group information given its id
     *
     * @param int $id the user group id to load
     *
     * @return UserGroup the user group object instance
     */
    public function read($id)
    {
        try {
            // Load the user group info
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT * FROM user_groups WHERE pk_user_group=?',
                [ $id ]
            );

            if (is_null($rs)) {
                return;
            }

            $this->load($rs);

            // Load the user group privileges
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT pk_fk_privilege"." FROM user_groups_privileges WHERE pk_fk_user_group = ?",
                [ $id ]
            );
            foreach ($rs as $row) {
                $this->privileges[] = $row['pk_fk_privilege'];
            }

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    /**
     * Updates the user group information given an array
     *
     * @param array $data the data to update the user group
     *
     * @return boolean true if the data was saved
     */
    public function update($data)
    {
        if ((int) $data['id'] <= 0) {
            return false;
        }

        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();

            // Updating the user group data
            $rs = $conn->update(
                "user_groups",
                [ 'name' => $data['name'] ],
                [ 'pk_user_group' => (int) $data['id'] ]
            );

            $data['pk_user_group'] = $data['id'];
            $this->load($data);

            // Updating the privileges, first remove previous and later insert new privileges
            $this->deletePrivileges($data['id']);

            if (!is_null($data['privileges'])
                && count($data['privileges'] > 0)
            ) {
                $this->insertPrivileges($data['privileges']);
            }

            $conn->commit();

            dispatchEventWithParams('usergroup.update', array('usergroup' => $this));

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Deletes an user group and privilege assigned given its id
     *
     * @param int $id the user group id to delete
     *
     * @return boolean true if the user group was deleted properly
     */
    public function delete($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        $conn = getService('dbal_connection');

        try {
            $conn->beginTransaction();
            $rs = $conn->delete(
                "user_groups",
                [ 'pk_user_group' => $id ]
            );

            if (!$rs) {
                return false;
            }

            $this->deletePrivileges($id);

            $conn->commit();

            dispatchEventWithParams('usergroup.delete', array('usergroup' => $this));

            return true;
        } catch (\Exception $e) {
            $conn->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns the user group name given an user group id
     *
     * @param int $id the user group id
     *
     * @return string the name of the group
     */
    public static function getGroupName($id)
    {
        try {
            $rs = getService('dbal_connection')->fetchAssoc(
                'SELECT name FROM user_groups WHERE pk_user_group=?',
                [ $id ]
            );

            if (!is_array($rs) || (is_array($rs) && !array_key_exists('name', $rs))) {
                return false;
            }

            return $rs['name'];
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns all the user groups in the system, excluding the Master group
     *
     * @return array a list of UserGroup objects
     */
    public function find()
    {
        $userGroups = [];

        try {
            $rs = getService('dbal_connection')->fetchAll(
                "SELECT pk_user_group, name FROM user_groups WHERE name <>'Masters'"
            );

            foreach ($rs as $row) {
                $userGroup = new UserGroup();
                $userGroup->load($row);
                $userGroups[] = $userGroup;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }

        return $userGroups;
    }

    /**
     * Whether the group contains a privilege given the privilege id
     *
     * @param int $privilegeID the privilege ID to check for
     *
     * @return boolean true if the group has the privilege
     */
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
     */
    private function insertPrivileges($privilegeIds)
    {
        foreach ($privilegeIds as $privilegeId) {
            $rs = getService('dbal_connection')->insert(
                "user_groups_privileges",
                [
                  'pk_fk_user_group' => (int) $this->id,
                  'pk_fk_privilege'  => (int) $privilegeId
                ]
            );
        }
    }

    /**
     * Removes the privilege assigned to the user group
     *
     * @param int $id the privilege id to remove
     *
     * @return boolean true if the privilege was removed
     */
    private function deletePrivileges($id)
    {
        if ((int) $id <= 0) {
            return false;
        }

        try {
            $rs = getService('dbal_connection')->delete(
                "user_groups_privileges",
                [ 'pk_fk_user_group' => intval($id) ]
            );

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Hydrates the object given an array with the new data
     *
     * @param array $data the data to load into the user group object
     *
     * @return UserGroup the user group object instance with the new information
     */
    public function load($data)
    {
        $this->id   = $data['pk_user_group'];
        $this->name = $data['name'];

        return $this;
    }
}
