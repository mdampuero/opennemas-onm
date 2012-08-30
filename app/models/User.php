<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * User
 *
 * @package    Onm
 * @subpackage Model
 **/
class User
{
    public $id               = null;
    public $login            = null;
    public $password         = null;
    public $sessionexpire    = null;
    public $email            = null;
    public $name             = null;
    public $firstname        = null;
    public $lastname         = null;
    public $address          = null;
    public $phone            = null;
    public $authorize        = null;
    public $id_user_group    = null;
    public $accesscategories = null;
    public $fk_user_group    = null;

    /**
     * @var string
     */
    public $authMethod = null;
    public $clientLoginToken = null;

    /**
     * Initializes the object instance
     *
     * @see MethodCacheManager
     * @param int $id User Id
     */
    public function __construct($id=null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }

        if (!property_exists($this, 'cache')) {
            $this->cache = null;
        }

        // Use MethodCacheManager
        if ( is_null($this->cache) ) {
            $this->cache = new MethodCacheManager($this, array('ttl' => 60));
        } else {
            $this->cache->setCacheLife(60); // 60 seconds
        }
    }

    public function create($data)
    {
        if ($this->checkIfUserExists($data)) {
            throw new \Exception(_('Already exists one user with that information'));
        }

        $sql = "INSERT INTO users (`login`, `password`, `sessionexpire`,
                                      `email`, `name`, `firstname`,
                                      `lastname`, `fk_user_group`)
                    VALUES (?,?,?,?,?,?,?,?)";
        $values = array(
            $data['login'],
            md5($data['password']),
            $data['sessionexpire'],
            $data['email'],
            $data['name'],
            $data['firstname'],
            $data['lastname'],
            $data['id_user_group']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }
        $this->id = $GLOBALS['application']->conn->Insert_ID();

        //Insertar las categorias de acceso.
        if (isset($data['ids_category'])) {
            $this->createAccessCategoriesDB($data['ids_category']);
        }

        return true;
    }

    public function read($id)
    {
        $sql = 'SELECT * FROM users WHERE pk_user = '.intval($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->id               = $rs->fields['pk_user'];
        $this->login            = $rs->fields['login'];
        $this->password         = $rs->fields['password'];
        $this->sessionexpire    = $rs->fields['sessionexpire'];
        $this->email            = $rs->fields['email'];
        $this->name             = $rs->fields['name'];
        $this->firstname        = $rs->fields['firstname'];
        $this->lastname         = $rs->fields['lastname'];
        $this->authorize        = $rs->fields['authorize'];
        $this->id_user_group    = $rs->fields['fk_user_group'];
        $this->accesscategories = $this->readAccessCategories();
    }

    public function update($data)
    {
        if (!isset($data['id_user_group']) || empty($data['id_user_group']) ) {
            $data['id_user_group'] = $this->id_user_group;
        }

        // Init transaction
        $GLOBALS['application']->conn->BeginTrans();

        if (isset($data['password']) && (strlen($data['password']) > 0)) {
            $sql = "UPDATE users
                    SET `login`=?, `password`= ?, `sessionexpire`=?,
                        `email`=?, `name`=?, `firstname`=?, `lastname`=?,
                        `fk_user_group`=?
                    WHERE pk_user=?";

            $values = array(
                $data['login'],
                md5($data['password']),
                $data['sessionexpire'],
                $data['email'],
                $data['name'],
                $data['firstname'],
                $data['lastname'],
                $data['id_user_group'],
                intval($data['id'])
            );

        } else {
            $sql = "UPDATE users
                    SET `login`=?, `sessionexpire`=?, `email`=?,
                        `name`=?, `firstname`=?, `lastname`=?,
                        `fk_user_group`=?
                    WHERE pk_user=?";

            $values = array(
                $data['login'],
                $data['sessionexpire'],
                $data['email'],
                $data['name'],
                $data['firstname'],
                $data['lastname'],
                $data['id_user_group'],
                intval($data['id'])
            );
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            // Rollback
            $GLOBALS['application']->conn->RollbackTrans();

            \Application::logDatabaseError();

            return;
        }

        $this->id = $data['id'];
        if (isset($data['ids_category'])) {
            $this->createAccessCategoriesDB($data['ids_category']);
        }

        // Finish transaction
        $GLOBALS['application']->conn->CommitTrans();
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM users WHERE pk_user=?';

        if ($GLOBALS['application']->conn->Execute($sql, array(intval($id)))===false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Checks if a user exists given some information.
     *
     * @return bool true if user exists
     **/
    public function checkIfUserExists($data)
    {
        $sql = "SELECT login FROM users WHERE login=? OR email=?";

        $values = array($data['login'], $data['email']);
        $rs = $GLOBALS['application']->conn->GetOne($sql, $values);

        return ($rs != false);
    }

    private function createAccessCategoriesDB($IdsCategory)
    {
        if ( $this->deleteAccessCategoriesDB() ) {
            $sql = "INSERT INTO users_content_categories
                                (`pk_fk_user`, `pk_fk_content_category`)
                    VALUES (?,?)";

            $values = array();
            for ($iIndex = 0; $iIndex < count($IdsCategory); $iIndex++) {
                $values[] = array($this->id, $IdsCategory[$iIndex]);
            }

            // bulk insert
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);
            if ($rs === false) {
                $GLOBALS['application']->conn->RollbackTrans();

                \Application::logDatabaseError();

                return false;
            }

            $this->readAccessCategories($this->id);

            return true;
        }

        return false;
    }

    public function addCategoryToUser ($idUser, $idCategory)
    {
        apc_delete(APC_PREFIX . "_readAccessCategories".$idUser);

        $sql = "INSERT INTO users_content_categories "
             . "(`pk_fk_user`, `pk_fk_content_category`) "
             .  "VALUES (?,?)";

        $values = array($idUser, $idCategory);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $newUserCategories = self::readAccessCategories($idUser);

        return true;
    }

    public function delCategoryToUser($idUser, $idCategory)
    {
        apc_delete(APC_PREFIX . "_readAccessCategories".$idUser);

        $sql = 'DELETE FROM users_content_categories '
             . 'WHERE pk_fk_content_category=?';
        $values = array(intval($idCategory));

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }
        $this->accesscategories = self::readAccessCategories($idUser);

        return true;
    }

    private function readAccessCategories($id=null)
    {
        $id = (!is_null($id))? $id: $this->id;
        $fetchedFromAPC = false;
        if (extension_loaded('apc')) {
            $key = APC_PREFIX . "_readAccessCategories".$id;
            $contentCategories = apc_fetch($key, $fetchedFromAPC);
        }
         // If was not fetched from APC now is turn of DB
        if (!$fetchedFromAPC) {

            $sql = 'SELECT pk_fk_content_category '
                 . 'FROM users_content_categories '
                 . 'WHERE pk_fk_user=?';
            $values = array(intval($id));
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);

            if (!$rs) {
                \Application::logDatabaseError();

                return null;
            }

            $contentCategories = array();
            while (!$rs->EOF) {
                 $contentCategory =
                    new ContentCategory($rs->fields['pk_fk_content_category']);
                 $contentCategories[] = $contentCategory;
                 $rs->MoveNext();
            }
            if (extension_loaded('apc')) {
                $key = APC_PREFIX . "_readAccessCategories".$id;
                apc_store($key, $contentCategories);
            }
        }

        return $contentCategories;
    }

    private function deleteAccessCategoriesDB()
    {
        $sql = 'DELETE FROM users_content_categories WHERE pk_fk_user=?';
        $values = array(intval($this->id));
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            $GLOBALS['application']->conn->RollbackTrans();

            \Application::logDatabaseError();

            return false;
        }
         apc_delete(APC_PREFIX . "_readAccessCategories".$this->id);

        return true;
    }

    public function login(
        $login,
        $password,
        $loginToken=null,
        $loginCaptcha=null
    )
    {
        $result = false;

        if ($this->isValidEmail($login)) {
            $result = $this->authGoogleClientLogin($login,
                $password, $loginToken, $loginCaptcha);
        } else {
            $result = $this->authDatabase($login, $password);
        }

        return $result;
    }

    /**
     * Check email is valid to login
     *
     * @param  string  $email
     * @return boolean
     */
    public function isValidEmail($email)
    {
        return preg_match('/.+@.+\..+/', $email);
    }

    /**
     * Try authenticate with database
     *
     * @param  string  $login
     * @param  string  $password
     * @return boolean Return true if login exists and password match
     */
    public function authDatabase($login, $password)
    {
        $sql = 'SELECT * FROM users WHERE login=\''.strval($login).'\'';
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $this->set_values($rs->fields);
        if ($this->password === md5($password)) {
            // Set access categories
            $this->accesscategories = $this->readAccessCategories();
            $this->authMethod = 'database';

            return true;
        }

        // Reset members properties
        $this->reset_values();

        return false;
    }

    /**
     * Get a password from a login
     *
     * @param  string $login
     * @return string Return the password of login
     */
    public function getPwd($login)
    {
        $sql = 'SELECT password FROM users WHERE login=\''.strval($login).'\'';
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $this->set_values($rs->fields);

        return $this->password;
    }

    /**
     * Get user data by email
     *
     * @param  string     $email
     * @return array|null
     */
    public function getUserDataByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($email));

        if (!$rs) {
            \Application::logDatabaseError();

            return null;
        }

        return $rs->fields;
    }

    /**
     * Set internal status of this object. If $data is empty don't do anything
     *
     * @param Array $data
     * @see User::reset_values
     */
    public function set_values($data)
    {
        if (!empty($data)) {
            $this->id           = $data['pk_user'];
            $this->login        = $data['login'];
            $this->password     = $data['password'];
            $this->sessionexpire= $data['sessionexpire'];
            $this->email        = $data['email'];
            $this->name         = $data['name'];
            $this->firstname    = $data['firstname'];
            $this->lastname     = $data['lastname'];
            $this->authorize    = $data['authorize'];
            $this->fk_user_group= $data['fk_user_group'];

            if (isset($data['ids_category'])) {
                $this->accesscategories =
                    $this->setAccessCategories($data['ids_category']);
            }
        }
    }

    /**
     * Set member properties to null
     *
     * @see User::set_values
     */
    public function reset_values()
    {
        $this->id           = null;
        $this->login        = null;
        $this->password     = null;
        $this->sessionexpire= null;
        $this->email        = null;
        $this->name         = null;
        $this->firstname    = null;
        $this->lastname     = null;
        $this->address      = null;
        $this->authorize    = null;
        $this->phone        = null;
        $this->fk_user_group= null;
        $this->accesscategories = null;
    }

    public function setAccessCategories($IdsCategory)
    {
        for ($iIndex=0; $iIndex<count($IdsCategory); $iIndex++) {
            $contentCategories[] = new ContentCategory($IdsCategory[$iIndex]);
        }

        return $contentCategories;
    }

    public function getAccessCategoriesName()
    {
        if (!empty($this->accesscategories)) {
            foreach ($this->accesscategories as $category) {
                $names[] = $category->name;
            }

            return $names;
        }

        return null;
    }

    public function getAccessCategoryIds($id = null)
    {
        if ( empty($this->accesscategories) ) {
            $this->accesscategories = $this->readAccessCategories($id);
        }

        $categories = $this->accesscategories;

        usort(
            $categories,
            function ($a, $b)
            {
                if ($a->posmenu == $b->posmenu) {
                    return 0;
                }
                return ($a->posmenu < $b->posmenu) ? -1 : 1;
            });

        $ids = array();
        foreach ($categories as $category) {
            $ids[] = $category->pk_content_category;
        }

        return $ids;
    }

    public function get_users($filter = null, $_order_by = 'ORDER BY 1')
    {
        $items = array();
        $_where = $this->buildFilter($filter);

        $sql = 'SELECT * FROM `users` ' . $_where . ' ' . $_order_by;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs !== false) {
            while (!$rs->EOF) {
                $user = new User();

                $user->set_values($rs->fields);
                $items[] = $user;

                $rs->MoveNext();
            }
        }

        return $items;
    }

    private function buildFilter($filter)
    {
        $newFilter = ' WHERE 1=1 ';

        if (!is_null($filter) && is_string($filter)) {
            if (preg_match('/^[ ]*where/i', $filter)) {
                $newFilter .= '  AND ' . $filter;
            }
        } elseif (!is_null($filter) && is_array($filter)) {
            $parts = array();

            if (isset($filter['base']) && !empty($filter['base'])) {
                $parts[] = $filter['base'];
            }

            if (isset($filter['login']) && !empty($filter['login'])) {
                $parts[] = '`login` LIKE "' . $filter['login'] . '%"';
            }

            if (isset($filter['name']) && !empty($filter['name'])) {
                $parts[] = 'MATCH(`name`, `firstname`, `lastname`) AGAINST ("' . $filter['name'] . '" IN BOOLEAN MODE)';
            }

            if (isset($filter['group']) && intval($filter['group'])>0) {
                $parts[] = '`fk_user_group` = ' . $filter['group'] . '';
            }

            if (count($parts) > 0) {
                $newFilter .= ' AND ' . implode(' OR ', $parts);
            }
        }

        return $newFilter;
    }

    public function get_user_name($id)
    {
        $sql = 'SELECT name, login FROM users WHERE pk_user=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }
        //Se cambia name por login.
        return $rs->fields['login'];
    }

    /**
     * Sets user configurations given a named array
     *
     * @param int $userId   the user id to set configs to
     * @param array  $userMeta a named array with settings and its values
     *
     * @return  boolean true if all went well
     */
    public function setMeta($userMeta = array())
    {
        $sql = 'REPLACE INTO usermeta (`user_id`, `meta_key`, `meta_value`) VALUES (?, ?, ?)';

        $values = array();
        foreach ($userMeta as $key => $value) {
            $values []= array($this->id, $key, $value);
        }

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if (!$rs) {
            return false;
        }

        return true;
    }

    /**
     * Returns the values of a user meta option
     *
     * @param string/array $meta an array or an string with the user meta name
     *
     * @return array/string an 2-dimensional array or an string with the user option values
     * @author
     **/
    public function getMeta($meta = array())
    {
        if (is_string($meta)) {
            $cleanMeta = array($meta);
        } else {
            $cleanMeta = $meta;
        }

        $metaNameSQL = array();
        foreach ($cleanMeta as $key) {
            $metaNameSQL []= $GLOBALS['application']->conn->qstr($key);
        }
        $metaNameSQL = implode(', ', $metaNameSQL);

        $sql = 'SELECT * FROM usermeta WHERE `user_id` = ? AND `meta_key` IN ('.$metaNameSQL.')';

        $GLOBALS['application']->conn->fetchMode = ADODB_FETCH_ASSOC;
        $rs = $GLOBALS['application']->conn->Execute($sql, array($this->id));

        if (!$rs) {
            return false;
        }

        if (is_array($meta)) {
            $returnValues = array();
            foreach ($rs as $value) {
                $returnValues [$rs->fields['meta_key']] = $rs->fields['meta_value'];
            }
        } else {
            $returnValues = $rs->fields['meta_value'];
        }


        return $returnValues;
    }
}

