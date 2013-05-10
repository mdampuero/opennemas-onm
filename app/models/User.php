<?php
/**
 * Defines the User class
 *
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @package    Model
 **/

/**
 * User
 *
 * @package    Model
 **/
class User
{
    /**
     * The user id
     *
     * @var int
     **/
    public $id               = null;

    /**
     * The login name aka username
     *
     * @var string
     **/
    public $login            = null;

    /**
     * Encrypted password
     *
     * @var string
     **/
    public $password         = null;

    /**
     * Seconds the session will be valid
     *
     * @var int
     **/
    public $sessionexpire    = null;

    /**
     * The user email
     *
     * @var
     **/
    public $email            = null;

    /**
     * The user name
     *
     * @var string
     **/
    public $name             = null;

    /**
     * The type of user
     *
     * @var string
     **/
    public $type             = null;

    /**
     * The amount of money in the user wallet
     *
     * @var int
     **/
    public $deposit          = null;

    /**
     * The login token, used for restore passwords and more
     *
     * @var string
     **/
    public $token            = null;

    /**
     * Whether the user can login or not
     *
     * @var string
     **/
    public $authorize        = null;

    /**
     * The user group id
     *
     * @var id
     **/
    public $id_user_group    = null;

    /**
     * The list of categories this user has access
     *
     * @var string
     **/
    public $accesscategories = null;

    /**
     * The user group id
     *
     * @var int
     **/
    public $fk_user_group    = null;

    /**
     * User login token
     *
     * @var string
     **/
    public $clientLoginToken = null;

    /**
     * Meta information for the user
     *
     * @var string
     **/
    public $meta = array();

    /**
     * Initializes the object instance
     *
     * @param int $id User Id
     *
     * @return void
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }

        if (!property_exists($this, 'cache')) {
            $this->cache = null;
        }

        // Use MethodCacheManager
        if (is_null($this->cache)) {
            $this->cache = new MethodCacheManager($this, array('ttl' => 60));
        } else {
            $this->cache->setCacheLife(60); // 60 seconds
        }
    }

    /**
     * Creates a new user given an array of data
     *
     * @param array $data the user data
     *
     * @return boolean true if the user was created
     **/
    public function create($data)
    {
        if ($this->checkIfUserExists($data)) {
            throw new \Exception(_('Already exists one user with that information'));
        }

        $sql =
            "INSERT INTO users "
            ."(`login`, `password`, `sessionexpire`, `email`, `name`, "
            ."`type`, `token`, `authorize`, `fk_user_group`) "
            ."VALUES (?,?,?,?,?,?,?,?,?)";
        $values = array(
            $data['login'],
            md5($data['password']),
            $data['sessionexpire'],
            $data['email'],
            $data['name'],
            $data['type'],
            $data['token'],
            $data['authorize'],
            $data['id_user_group']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }
        $this->id = $GLOBALS['application']->conn->Insert_ID();

        //Insertar las categorias de acceso.
        if (isset($data['ids_category'])) {
            $this->createAccessCategoriesDb($data['ids_category']);
        }

        return true;
    }

    /**
     * Loads the user information given its id
     *
     * @param int $id the user id
     *
     * @return User the user object instance
     **/
    public function read($id)
    {
        $sql = 'SELECT * FROM users WHERE pk_user = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

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
        $this->deposit          = $rs->fields['deposit'];
        $this->type             = $rs->fields['type'];
        $this->token            = $rs->fields['token'];
        $this->authorize        = $rs->fields['authorize'];
        $this->id_user_group    = $rs->fields['fk_user_group'];
        $this->accesscategories = $this->readAccessCategories();

        return $this;
    }

    /**
     * Updates the user information given an array of data
     *
     * @param array $data the new user data
     *
     * @return boolean true if the user was updated
     **/
    public function update($data)
    {
        if (!isset($data['id_user_group'])
            || empty($data['id_user_group'])
        ) {
            $data['id_user_group'] = $this->id_user_group;
        }

        // Init transaction
        $GLOBALS['application']->conn->BeginTrans();

        if (isset($data['password']) && (strlen($data['password']) > 0)) {
            $sql = "UPDATE users
                    SET `login`=?, `password`= ?, `sessionexpire`=?,
                        `email`=?, `name`=?, `fk_user_group`=?
                    WHERE pk_user=?";

            $values = array(
                $data['login'],
                md5($data['password']),
                $data['sessionexpire'],
                $data['email'],
                $data['name'],
                $data['id_user_group'],
                intval($data['id'])
            );

        } else {
            $sql = "UPDATE users
                    SET `login`=?, `sessionexpire`=?, `email`=?,
                        `name`=?, `fk_user_group`=?
                    WHERE pk_user=?";

            $values = array(
                $data['login'],
                $data['sessionexpire'],
                $data['email'],
                $data['name'],
                $data['id_user_group'],
                intval($data['id'])
            );
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            // Rollback
            $GLOBALS['application']->conn->RollbackTrans();

            \Application::logDatabaseError();

            return false;
        }

        $this->id = $data['id'];
        if (isset($data['ids_category'])) {
            $this->createAccessCategoriesDb($data['ids_category']);
        }

        // Finish transaction
        $GLOBALS['application']->conn->CommitTrans();

        return true;
    }

    /**
     * Deletes an user given its id
     *
     * @param int $id the user id
     *
     * @return boolean true if the user was deleted
     **/
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
     * @param array $data tuple with the login and email params
     *
     * @return boolean true if user exists
     **/
    public function checkIfUserExists($data)
    {
        $sql = "SELECT login FROM users WHERE login=? OR email=?";

        $values = array($data['login'], $data['email']);
        $rs = $GLOBALS['application']->conn->GetOne($sql, $values);

        return ($rs != false);
    }

    /**
     * Stores the list of categories an user has access
     *
     * @param int $IdsCategory the list of category ids
     *
     * @return boolean
     **/
    private function createAccessCategoriesDb($IdsCategory)
    {
        if ($this->deleteAccessCategoriesDb()) {
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

    /**
     * Adds access to one category to a user
     *
     * @param int $idUser the user id
     * @param int $idCategory the category id
     *
     * @return boolean true if the action was done
     **/
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

    /**
     * Deletes all the category-user assignments
     *
     * @param int $idUser the user id
     * @param int $idCategory the category id
     *
     * @return boolean
     **/
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

    /**
     * Loads and returns the categories an user has access
     *
     * @param int $id the user id
     *
     * @return array the list of category ids
     **/
    private function readAccessCategories($id = null)
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

    /**
     * Removes all the category access assignments to the current user
     *
     * @return boolean true if the action was performed
     **/
    private function deleteAccessCategoriesDb()
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


    /**
     * Tries to login a user given a login information
     *
     * @param string $login the username
     * @param string $password the password
     * @param string $loginToken the login token provided
     * @param string $loginCaptcha
     *
     * @return boolean true if the user has access
     **/
    public function login(
        $login,
        $password,
        $loginToken = null,
        $loginCaptcha = null
    ) {
        $result = false;

        $result = $this->authDatabase($login, $password);
        if (!$result) {
            $result = $this->authDatabase($login, $password, true);
        }

        return $result;
    }

    /**
     * Aauthenticate by using the database
     *
     * @param  string  $login
     * @param  string  $password
     * @param  loolean $managerDb
     *
     * @return boolean Return true if login exists and password match
     */
    public function authDatabase($login, $password, $managerDb = false)
    {
        $sql = 'SELECT * FROM users WHERE login=\''.strval($login).'\'';
        if (!$managerDb) {
            $rs = $GLOBALS['application']->conn->Execute($sql);
        } else {
            $rs =  \Onm\Instance\InstanceManager::getInstance()->getConnection()->Execute($sql);
        }

        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        $this->setValues($rs->fields);
        if ($this->password === md5($password)) {
            // Set access categories
            $this->accesscategories = $this->readAccessCategories();
            $this->authMethod = 'database';

            return true;
        } elseif ($this->password === $password) {
            // Frontend login from mail activation
            $this->authMethod = 'database';

            return true;
        }

        // Reset members properties
        $this->resetValues();

        return false;
    }

    /**
     * Get user data by email
     *
     * @param  string     $email
     * @return array|null
     */
    public function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email=?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($email));

        if (!$rs) {
            \Application::logDatabaseError();

            return null;
        }

        $this->id               = $rs->fields['pk_user'];
        $this->login            = $rs->fields['login'];
        $this->password         = $rs->fields['password'];
        $this->sessionexpire    = $rs->fields['sessionexpire'];
        $this->email            = $rs->fields['email'];
        $this->name             = $rs->fields['name'];
        $this->deposit          = $rs->fields['deposit'];
        $this->type             = $rs->fields['type'];
        $this->token            = $rs->fields['token'];
        $this->authorize        = $rs->fields['authorize'];
        $this->id_user_group    = $rs->fields['fk_user_group'];
        $this->accesscategories = $this->readAccessCategories();

        return $this;
    }

    /**
     * Check if the token for registration is same user token and get user data
     *
     * @param string $token the token
     *
     * @return user if exists false otherwise
     **/
    public function findByToken($token)
    {
        $sql   = 'SELECT * FROM users WHERE token=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, $token);

        if ($rs == false) {
            return null;
        }

        $this->id               = $rs->fields['pk_user'];
        $this->login            = $rs->fields['login'];
        $this->password         = $rs->fields['password'];
        $this->sessionexpire    = $rs->fields['sessionexpire'];
        $this->email            = $rs->fields['email'];
        $this->name             = $rs->fields['name'];
        $this->deposit          = $rs->fields['deposit'];
        $this->type             = $rs->fields['type'];
        $this->token            = $rs->fields['token'];
        $this->authorize        = $rs->fields['authorize'];
        $this->id_user_group    = $rs->fields['fk_user_group'];
        $this->accesscategories = $this->readAccessCategories();

        return $this;
    }

    /**
     * Set internal status of this object. If $data is empty don't do anything
     *
     * @param Array $data
     * @see User::resetValues
     */
    public function setValues($data)
    {
        if (!empty($data)) {
            $this->id            = $data['pk_user'];
            $this->login         = $data['login'];
            $this->password      = $data['password'];
            $this->sessionexpire = $data['sessionexpire'];
            $this->email         = $data['email'];
            $this->name          = $data['name'];
            $this->deposit       = array_key_exists('deposit', $data) ? $data['deposit'] : '';
            $this->type          = $data['type'];
            $this->token         = $data['token'];
            $this->authorize     = $data['authorize'];
            $this->fk_user_group = $data['fk_user_group'];

            if (isset($data['ids_category'])) {
                $this->accesscategories = $this->setAccessCategories($data['ids_category']);
            }
        }
    }

    /**
     * Set member properties to null
     *
     * @see User::setValues
     */
    public function resetValues()
    {
        $this->id           = null;
        $this->login        = null;
        $this->password     = null;
        $this->sessionexpire= null;
        $this->email        = null;
        $this->name         = null;
        $this->authorize    = null;
        $this->fk_user_group= null;
        $this->accesscategories = null;
    }

    /**
     * Sets the access categories to a user
     *
     * @param array $categoryIds the list of category ids
     *
     * @return array the same list
     **/
    public function setAccessCategories($categoryIds)
    {
        for ($iIndex=0; $iIndex < count($categoryIds); $iIndex++) {
            $contentCategories[] = new ContentCategory($categoryIds[$iIndex]);
        }

        return $contentCategories;
    }

    /**
     * Returns the list of category names the current user has
     *
     * @return array the list of category names
     **/
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

    /**
     * Returns the list of category ids a user has access given the user id
     *
     * @param int $id the user id
     *
     * @return array the list of category ids
     **/
    public function getAccessCategoryIds($id = null)
    {
        if (empty($this->accesscategories)) {
            $this->accesscategories = $this->readAccessCategories($id);
        }

        $categories = $this->accesscategories;

        usort(
            $categories,
            function (
                $a,
                $b
            ) {
                if ($a->posmenu == $b->posmenu) {
                    return 0;
                }
                return ($a->posmenu < $b->posmenu) ? -1 : 1;
            }
        );

        $ids = array();
        foreach ($categories as $category) {
            $ids[] = $category->pk_content_category;
        }

        return $ids;
    }

    /**
     * Returns a list of users that matches a search criteria
     *
     * @param array $filter the list of search criterias to use
     * @param string $orderBy the ORDER BY clause to use
     *
     * @return array list of users
     **/
    public function getUsers($filter = null, $orderBy = 'ORDER BY 1')
    {
        $items = array();
        $_where = $this->buildFilter($filter);

        $sql = 'SELECT * FROM `users` ' . $_where . ' ' . $orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs !== false) {
            while (!$rs->EOF) {
                $user = new User();
                $user->setValues($rs->fields);
                $user->meta = $user->getMeta();
                $items[] = $user;

                $rs->MoveNext();
            }
        }

        return $items;
    }

    /**
     * Returns the user name for a given user id
     *
     * @param int $id the user id
     *
     * @return string the user name
     **/
    public function getUserName($id)
    {
        $sql = 'SELECT name, login FROM users WHERE pk_user=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            \Application::logDatabaseError();

            return false;
        }

        return $rs->fields['login'];
    }

    /**
     * Sets user configurations given a named array
     *
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
     **/
    public function getMeta($meta = null)
    {
        if (count($this->meta) <= 0) {
            $sql = 'SELECT * FROM usermeta WHERE `user_id` = ?';

            $GLOBALS['application']->conn->fetchMode = ADODB_FETCH_ASSOC;
            $rs = $GLOBALS['application']->conn->Execute($sql, array($this->id));

            foreach ($rs as $value) {
                $this->meta[$rs->fields['meta_key']] = $rs->fields['meta_value'];
            }
        }

        if (is_string($meta)) {
            $value = null;
            if (array_key_exists($meta, $this->meta)) {
                $value = $this->meta[$meta];
            }
        } elseif (is_array($meta)) {
            $value = array_intersect_key($this->meta, array_flip($meta));
        } else {
            $value = $this->meta;
        }

        return $value;
    }

     /**
     * Remove user meta given a named array
     *
     * @param int $userId   the user id to set configs to
     * @param array  $userMeta a named array with settings and its values
     *
     * @return  boolean true if all went well
     */
    public function deleteMeta($userId)
    {
        $sql = 'DELETE FROM usermeta WHERE `user_id`=?';

        if ($GLOBALS['application']->conn->Execute($sql, array(intval($userId)))===false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Sets an user state to disabled/not authorized
     *
     * @param  int $id the use id
     *
     * @return boolean true if the action was done
     */
    public function unauthorizeUser($id)
    {
        $sql = "UPDATE users SET `authorize`=0 WHERE pk_user=".intval($id);

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Sets an users state to enabled/authorized/activated
     *
     * @param  int $id the use id
     *
     * @return boolean true if the action was done
     */
    public function authorizeUser($id)
    {
        $sql = "UPDATE users SET `authorize`=1 WHERE pk_user=".intval($id);

        if ($GLOBALS['application']->conn->Execute($sql) === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Checks if an email is already in use by frontend users
     *
     * @param  email $email the email address to look for
     *
     * @return bool if is in use this email
     */
    public function checkIfExistsUserEmail($email)
    {
        $sql = 'SELECT count(*) AS num  FROM `users` WHERE email = ?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($email));
        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        return ($rs->fields['num'] > 0);
    }

    /**
     * Checks if an user name (login) is already in use by frontend users
     *
     * @param  $userName The user name to log in
     * @return bool if is in use this user name (login)
     */
    public function checkIfExistsUserName($userName)
    {
        $sql = 'SELECT count(*) AS num '
            . 'FROM `users` WHERE login = "'.$userName.'"';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        return ($rs->fields['num'] > 0);
    }

    /**
     * Generate new token and update user with it
     *
     * @param int $id the user id
     * @param string $token the new user token
     *
     * @return boolen
     **/
    public function updateUserToken($id, $token)
    {
        $sql = "UPDATE users SET `token`= '".$token."' WHERE pk_user=".intval($id);
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            \Application::logDatabaseError();
            return false;
        }

        return true;
    }

    /**
     * Updates the users password
     *
     * @param int $id the user id
     * @param string $pass the new user password
     *
     * @return boolean true if the pass was updated
     **/
    public function updateUserPassword($id, $pass)
    {
        $sql = "UPDATE users SET `password`= '".$pass."' WHERE pk_user=?";
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if ($rs === false) {
            \Application::logDatabaseError();
            return false;
        }

        return true;
    }

    /**
     * Increases the paywall subscription time given the subscription name
     *
     * @param string $planTime the name of the plan
     *
     * @return void
     **/
    public function addSubscriptionLimit($planTime = 0)
    {
        $newTime = $planTime->format('Y-m-d H:i:s');

        $this->setMeta(array('paywall_time_limit' => $newTime));
    }

    /**
     * Returns a list of User objects where the users has paywall subscription
     *
     * @return void
     **/
    public static function getUsersWithSubscription($config = array())
    {
        $defaultConfig = array(
            'limit' => null,
        );

        $config = array_merge($defaultConfig, $config);

        $limit = '';
        if ($config['limit'] > 0) {
            $limit = 'LIMIT '.$config['limit'];
        }
        $currentTime = new \DateTime();
        $currentTime->setTimezone(new \DateTimeZone('UTC'));

        $currentTime = $currentTime->format('Y-m-d H:i:s');

        $sql = "SELECT user_id FROM usermeta WHERE `meta_key`= 'paywall_time_limit' && `meta_value` > ? $limit";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql, array($currentTime));

        if ($rs === false) {
            \Application::logDatabaseError();
            return array();
        }
        $users = array();
        while (!$rs->EOF) {

            $user = new \User($rs->fields['user_id']);
            $user->meta = $user->getMeta();

            if ($user->meta['paywall_time_limit']) {
                $user->meta['paywall_time_limit'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $user->meta['paywall_time_limit'],
                    new \DateTimeZone('UTC')
                );
            }
            $users []= $user;

            $rs->MoveNext();
        }

        return $users;
    }

    /**
     * Returns a list of User objects where the users has paywall subscription
     *
     * @return void
     **/
    public static function countUsersWithSubscription($limit = array())
    {
        $currentTime = new \DateTime();
        $currentTime->setTimezone(new \DateTimeZone('UTC'));

        $currentTime = $currentTime->format('Y-m-d H:i:s');

        $sql = "SELECT count(user_id) as count FROM usermeta ".
               "WHERE `meta_key`= 'paywall_time_limit' && `meta_value` > ?";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql, array($currentTime));

        if ($rs === false) {
            \Application::logDatabaseError();
            return 0;
        }



        return $rs->fields['count'];
    }

    /**
     * Increases the paywall subscription time given the subscription name
     *
     * @param string $planTime the name of the plan
     *
     * @return void
     **/
    public function addRegisterDate()
    {
        $currentTime = new \DateTime();
        $currentTime->setTimezone(new \DateTimeZone('UTC'));
        $currentTime = $currentTime->format('Y-m-d H:i:s');

        $this->setMeta(array('register_date' => $currentTime));
    }

    /**
     * Returns a valid SQL WHERE clause for the given filter
     *
     * @param array $filter the list of filters
     *
     * @return string the WHERE clause
     **/
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
                $parts[] = '`login` LIKE "%' . $filter['login'] . '%"';
            }

            if (isset($filter['name']) && !empty($filter['name'])) {
                $parts[] = 'MATCH(`name`) AGAINST ("' . $filter['name'] . '" IN BOOLEAN MODE)';
            }

            if (isset($filter['group']) && intval($filter['group'])>0) {
                $parts[] = '`fk_user_group` = ' . $filter['group'] . '';
            }

            if (isset($filter['email']) && !empty($filter['email'])>0) {
                $parts[] = '`email` LIKE "%' . $filter['email'] . '%"';
            }

            if (count($parts) > 0) {
                $newFilter .= ' AND ' . implode(' AND ', $parts);
            }
        }

        return $newFilter;
    }
}
