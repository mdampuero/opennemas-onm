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

use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthUser;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

/**
 * User
 *
 * @package    Model
 **/
class User extends OAuthUser implements AdvancedUserInterface, EquatableInterface
{
    /**
     * The user id
     *
     * @var int
     **/
    public $id = null;

    /**
     * The username
     *
     * @var string
     **/
    public $username = null;

    /**
     * Encrypted password
     *
     * @var string
     **/
    public $password = null;

    /**
     * The user email
     *
     * @var
     **/
    public $email = null;

    /**
     * The user real name
     *
     * @var string
     **/
    public $name = null;

    /**
     * Seconds the session will be valid
     *
     * @var int
     **/
    public $sessionexpire = null;

    /**
     * The user blog/page url
     *
     * @var int
     **/
    public $url = null;

    /**
     * The user biography
     *
     * @var int
     **/
    public $bio = null;

    /**
     * The user avatar image id
     *
     * @var string
     **/
    public $avatar_img_id = null;

    /**
     * The type of user
     *
     * @var string
     **/
    public $type = null;

    /**
     * The amount of money in the user wallet
     *
     * @var int
     **/
    public $deposit = null;

    /**
     * The login token, used for restore passwords and more
     *
     * @var string
     **/
    public $token = null;

    /**
     * Whether the user can login or not
     *
     * @var string
     **/
    public $activated = null;

    /**
     * The user group id
     *
     * @var id
     **/
    public $id_user_group = null;

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
    public $fk_user_group = null;

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

        // Transform groups array to a string separated by comma
        $data['id_user_group'] = implode(',', $data['id_user_group']);

        $sql =
            "INSERT INTO users "
            ."(`username`, `password`, `sessionexpire`, `url`, `bio`, `avatar_img_id`, "
            ."`email`, `name`, `type`, `token`, `activated`, `fk_user_group`) "
            ."VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
        $values = array(
            $data['username'],
            md5($data['password']),
            $data['sessionexpire'],
            $data['url'],
            $data['bio'],
            $data['avatar_img_id'],
            $data['email'],
            $data['name'],
            $data['type'],
            $data['token'],
            $data['activated'],
            $data['id_user_group']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }
        $this->id = $GLOBALS['application']->conn->Insert_ID();

        //Insertar las categorias de acceso.
        if (isset($data['ids_category'])) {
            $this->createAccessCategoriesDb($data['ids_category']);
        }

        dispatchEventWithParams('user.create', array('user' => $this));

        return true;
    }

    /**
     * undocumented function
     *
     * @return void
     * @author
     **/
    public function __get($property)
    {
        switch ($property) {
            case 'photo':
                return $this->getPhoto();
                break;
            default:
                break;
        }
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
        $sql = 'SELECT * FROM users WHERE id = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            return null;
        }

        $this->id               = $rs->fields['id'];
        $this->username         = $rs->fields['username'];
        $this->password         = $rs->fields['password'];
        $this->sessionexpire    = $rs->fields['sessionexpire'];
        $this->url              = $rs->fields['url'];
        $this->bio              = $rs->fields['bio'];
        $this->avatar_img_id    = $rs->fields['avatar_img_id'];
        $this->email            = $rs->fields['email'];
        $this->name             = $rs->fields['name'];
        $this->deposit          = $rs->fields['deposit'];
        $this->type             = $rs->fields['type'];
        $this->token            = $rs->fields['token'];
        $this->activated        = $rs->fields['activated'];
        $this->id_user_group    = explode(',', $rs->fields['fk_user_group']);

        $database = $GLOBALS['application']->conn->connectionParams['dbname'];
        if ($database != 'onm-instances') {
            $this->accesscategories = $this->readAccessCategories();
        }

        // Get user meta information
        $this->meta = $this->getMeta();

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
        if ($this->checkIfUserExists($data)) {
            throw new \Exception(_('Already exists one user with that information'));
        }

        if (!isset($data['id_user_group'])
            || empty($data['id_user_group'])
        ) {
            $data['id_user_group'] = $this->id_user_group;
        }

        // Init transaction
        $GLOBALS['application']->conn->BeginTrans();

        // Transform groups array to a string separated by comma
        $data['id_user_group'] = implode(',', $data['id_user_group']);

        if (isset($data['password'])
            && (strlen($data['password']) > 0)
            && $data['password'] === $data['passwordconfirm']
        ) {
            $sql = "UPDATE users
                    SET `username`=?, `password`= ?, `sessionexpire`=?, `url`=?, `bio`=?,
                        `avatar_img_id`=?, `email`=?, `name`=?, `activated`=?, `fk_user_group`=?, type=?
                    WHERE id=?";

            $values = array(
                $data['username'],
                md5($data['password']),
                $data['sessionexpire'],
                $data['url'],
                $data['bio'],
                $data['avatar_img_id'],
                $data['email'],
                $data['name'],
                $data['activated'],
                $data['id_user_group'],
                $data['type'],
                intval($data['id'])
            );

        } else {
            $sql = "UPDATE users
                    SET `username`=?, `sessionexpire`=?, `email`=?, `url`=?, `bio`=?,
                        `avatar_img_id`=?, `name`=?, `activated`=?, `fk_user_group`=?, type=?
                    WHERE id=?";

            $values = array(
                $data['username'],
                $data['sessionexpire'],
                $data['email'],
                $data['url'],
                $data['bio'],
                $data['avatar_img_id'],
                $data['name'],
                $data['activated'],
                $data['id_user_group'],
                $data['type'],
                intval($data['id'])
            );
        }

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            // Rollback
            $GLOBALS['application']->conn->RollbackTrans();

            return false;
        }

        // Finish transaction
        $GLOBALS['application']->conn->CommitTrans();

        $this->id = $data['id'];
        if (array_key_exists('ids_category', $data)) {
            $this->createAccessCategoriesDb($data['ids_category']);
        }

        dispatchEventWithParams('user.update', array('user' => $this));

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
        $sql = 'DELETE FROM users WHERE id=?';

        if ($GLOBALS['application']->conn->Execute($sql, array(intval($id)))===false) {
            return false;
        }

        if (!$this->deleteMeta($id)) {
            return false;
        }

        dispatchEventWithParams('user.delete', array('user' => $this));

        return true;
    }

    /**
     * Returns the Photo object that represents the user avatar
     *
     * @return Photo the photo object
     **/
    public function getPhoto()
    {
        return new \Photo($this->avatar_img_id);
    }

    /**
     * Checks if a user exists given some information.
     *
     * @param array $data tuple with the username and email params
     *
     * @return boolean true if user exists
     **/
    public function checkIfUserExists($data)
    {
        $sql = "SELECT id FROM users WHERE username=? OR email=? OR email=? OR username=?";

        $values = array($data['username'], $data['email'], $data['username'], $data['email']);
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);

        // If is update, check for more than 1 result
        if (isset($data['id']) && $rs->_numOfRows == 1 && $rs->fields['id'] == $data['id']) {
            return false;
        }

        return ($rs->fields != null && $rs->fields != false);
    }

    /**
     * Stores the list of categories an user has access
     *
     * @param int $IdsCategory the list of category ids
     *
     * @return boolean
     **/
    private function createAccessCategoriesDb($categoryIds)
    {
        if ($this->deleteAccessCategoriesDb()) {
            $sql = "INSERT INTO users_content_categories (`pk_fk_user`, `pk_fk_content_category`)
                    VALUES (?,?)";

            if (count($categoryIds) > 0) {
                $values = array();
                for ($iIndex = 0; $iIndex < count($categoryIds); $iIndex++) {
                    $values[] = array($this->id, $categoryIds[$iIndex]);
                }

                $rs = $GLOBALS['application']->conn->Execute($sql, $values);
                if ($rs === false) {
                    $GLOBALS['application']->conn->RollbackTrans();

                    return false;
                }
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
    public function addCategoryToUser($idUser, $idCategory)
    {
        $sql = "INSERT INTO users_content_categories "
             . "(`pk_fk_user`, `pk_fk_content_category`) "
             .  "VALUES (?,?)";

        $values = array($idUser, $idCategory);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }

        self::readAccessCategories($idUser);

        $cache = getService('cache');
        $cache->delete(CACHE_PREFIX . "categories_for_user_".$idUser);

        dispatchEventWithParams('user.update', array('user' => $this));

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
        $cache = getService('cache');
        $cache->delete(CACHE_PREFIX . "categories_for_user_".$idUser);

        $sql = 'DELETE FROM users_content_categories '
             . 'WHERE pk_fk_content_category=?';
        $values = array(intval($idCategory));

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            return false;
        }
        $this->accesscategories = self::readAccessCategories($idUser);

        dispatchEventWithParams('user.update', array('user' => $this));

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
        $cache = getService('cache');

        $id = (!is_null($id))? $id: $this->id;

        $contentCategories = $cache->fetch(CACHE_PREFIX . "categories_for_user_".$id);
         // If was not fetched from APC now is turn of DB
        if (!$contentCategories) {

            $sql = 'SELECT pk_fk_content_category '
                 . 'FROM users_content_categories '
                 . 'WHERE pk_fk_user=?';
            $values = array(intval($id));
            $rs = $GLOBALS['application']->conn->Execute($sql, $values);

            if (!$rs) {
                return null;
            }

            $contentCategories = array();
            while (!$rs->EOF) {
                 $contentCategory = new ContentCategory($rs->fields['pk_fk_content_category']);
                 $contentCategories[] = $contentCategory;
                 $rs->MoveNext();
            }

            $cache->save(CACHE_PREFIX . "categories_for_user_".$id, $contentCategories);
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
        global $kernel;
        $cache = $kernel->getContainer()->get('cache');

        $sql = 'DELETE FROM users_content_categories WHERE pk_fk_user=?';
        $values = array(intval($this->id));
        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            $GLOBALS['application']->conn->RollbackTrans();

            return false;
        }

        $cache->delete(CACHE_PREFIX . "categories_for_user_".$this->id);

        dispatchEventWithParams('user.update', array('user' => $this));

        return true;
    }


    /**
     * Tries to login a user given a username information
     *
     * @param string $username the username
     * @param string $password the password
     * @param string $loginToken the login token provided
     * @param string $loginCaptcha
     * @param int    $time
     *
     * @return boolean true if the user has access
     **/
    public function login(
        $username,
        $password,
        $loginToken = null,
        $loginCaptcha = null,
        $time = null
    ) {
        $result = false;

        $result = $this->authDatabase($username, $password, false, $time);
        if (!$result) {
            $result = $this->authDatabase($username, $password, true, $time);
        }

        return $result;
    }

    /**
     * Authenticate by using the database
     *
     * @param  string  $username
     * @param  string  $password
     * @param  boolean $managerDb
     * @param  int     $time
     *
     * @return boolean Return true if username exists and password match
     */
    public function authDatabase($username, $password, $managerDb = false, $time = null)
    {
        $sql = 'SELECT * FROM users WHERE username=? OR email=?';
        if (!$managerDb) {
            $rs = $GLOBALS['application']->conn->Execute($sql, array(strval($username), strval($username)));
        } else {
            $conn = getService('db_conn_manager');
            $rs =  $conn->Execute($sql, array(strval($username), strval($username)));
        }

        if (!$rs) {
            return false;
        }

        $this->setValues($rs->fields);

        // Check if password came with md5 tag otherwise js is disabled
        if (strstr($password, 'md5:') && 'md5:'.md5($this->password.$time) === $password) {
            // Set access categories
            $this->accesscategories = $this->readAccessCategories();
            $this->authMethod = 'database';

            return true;
        } elseif ($this->password === md5($password)) { // Pass not md5 ecrypted, js disabled
            // Set access categories
            $this->accesscategories = $this->readAccessCategories();
            $this->authMethod = 'database';

            return true;
        } elseif ($this->password === $password) { // Frontend login from mail activation
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

        if (!$rs->fields) {
            return null;
        }

        $this->id               = $rs->fields['id'];
        $this->username         = $rs->fields['username'];
        $this->password         = $rs->fields['password'];
        $this->sessionexpire    = $rs->fields['sessionexpire'];
        $this->url              = $rs->fields['url'];
        $this->bio              = $rs->fields['bio'];
        $this->avatar_img_id    = $rs->fields['avatar_img_id'];
        $this->email            = $rs->fields['email'];
        $this->name             = $rs->fields['name'];
        $this->deposit          = $rs->fields['deposit'];
        $this->type             = $rs->fields['type'];
        $this->token            = $rs->fields['token'];
        $this->activated        = $rs->fields['activated'];
        $this->id_user_group    = explode(',', $rs->fields['fk_user_group']);
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
        if (empty($token)) {
            return null;
        }

        $sql   = 'SELECT * FROM users WHERE token=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, $token);

        if (!$rs->fields) {
            return null;
        }

        $this->id               = $rs->fields['id'];
        $this->username         = $rs->fields['username'];
        $this->password         = $rs->fields['password'];
        $this->sessionexpire    = $rs->fields['sessionexpire'];
        $this->url              = $rs->fields['url'];
        $this->bio              = $rs->fields['bio'];
        $this->avatar_img_id    = $rs->fields['avatar_img_id'];
        $this->email            = $rs->fields['email'];
        $this->name             = $rs->fields['name'];
        $this->deposit          = $rs->fields['deposit'];
        $this->type             = $rs->fields['type'];
        $this->token            = $rs->fields['token'];
        $this->activated        = $rs->fields['activated'];
        $this->id_user_group    = explode(',', $rs->fields['fk_user_group']);
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
            $this->id            = $data['id'];
            $this->username      = $data['username'];
            $this->password      = $data['password'];
            $this->sessionexpire = $data['sessionexpire'];
            $this->url           = $data['url'];
            $this->bio           = $data['bio'];
            $this->avatar_img_id = $data['avatar_img_id'];
            $this->email         = $data['email'];
            $this->name          = $data['name'];
            $this->deposit       = array_key_exists('deposit', $data) ? $data['deposit'] : '';
            $this->type          = $data['type'];
            $this->token         = $data['token'];
            $this->activated     = $data['activated'];
            $this->fk_user_group = explode(',', $data['fk_user_group']);

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
        $this->id               = null;
        $this->username         = null;
        $this->password         = null;
        $this->sessionexpire    = null;
        $this->url              = null;
        $this->bio              = null;
        $this->avatar_img_id    = null;
        $this->email            = null;
        $this->name             = null;
        $this->deposit          = null;
        $this->type             = null;
        $this->token            = null;
        $this->activated        = null;
        $this->fk_user_group    = null;
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

        if (count($this->accesscategories) < 1) {
            return array();
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

        $sql = 'SELECT * FROM `users`';
        if ($_where) {
            $sql .= ' WHERE ' . $_where;
        }

        $sql .= ' ' . $orderBy;

        $rs = $GLOBALS['application']->conn->Execute($sql);
        if ($rs !== false) {
            while (!$rs->EOF) {
                $user = new User($rs->fields['id']);
                $user->meta = $user->getMeta();
                $items[] = $user;

                $rs->MoveNext();
            }
        }

        return $items;
    }

    /**
     * Returns the username for a given user id
     *
     * @param int $id the user id
     *
     * @return string the user name
     **/
    public function findUserName($id)
    {
        $sql = 'SELECT username FROM users WHERE id=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            return false;

        }

        return $rs->fields['username'];
    }

    /**
     * Returns the user real name for a given user id
     *
     * @param int $id the user id
     *
     * @return string the user name
     **/
    public function getUserRealName($id)
    {
        if (isset($this) && !empty($this->id)) {
            return $this->name;
        }
        $sql = 'SELECT name FROM users WHERE id=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            return false;
        }

        return $rs->fields['name'];
    }

    /**
     * Returns the photo id associated to an user.
     *
     * @param string $id the user id.
     *
     * @return int the photo id
     */
    public function getUserPhotoId($id)
    {
        $sql = 'SELECT `avatar_img_id` FROM users WHERE id = ?';
        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));

        if (!$rs) {
            return false;
        }

        return $rs->fields['avatar_img_id'];
    }

    /**
     * Returns all the authors ORDER BY name.
     *
     * @return array multidimensional array with information about authors
     */
    public static function getAllUsersAuthors($filter = null)
    {
        if (!is_null($filter)) {
            $_where = $filter;
        } else {
            $_where = '';
        }

        $sql = 'SELECT `id` FROM users WHERE fk_user_group '.$_where.' LIKE "%3%" ORDER BY `name`';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            return array();
        }

        $i = 0;
        $authors = array();
        while (!$rs->EOF) {
            $authors[$i]         = new \User($rs->fields['id']);
            $authors[$i]->params = $authors[$i]->getMeta();
            if (array_key_exists('is_blog', $authors[$i]->params)) {
                $authors[$i]->is_blog = $authors[$i]->params['is_blog'];
            }

            $rs->MoveNext();
            $i++;
        }

        // Order names with accents
        uasort($authors, function ($a, $b) {
            $patterns = array(
                'a' => '(á|à|â|ä|Á|À|Â|Ä)',
                'e' => '(é|è|ê|ë|É|È|Ê|Ë)',
                'i' => '(í|ì|î|ï|Í|Ì|Î|Ï)',
                'o' => '(ó|ò|ô|ö|Ó|Ò|Ô|Ö)',
                'u' => '(ú|ù|û|ü|Ú|Ù|Û|Ü)'
            );
            $name1 = preg_replace(array_values($patterns), array_keys($patterns), $a->name);
            $name2 = preg_replace(array_values($patterns), array_keys($patterns), $b->name);
            return strcasecmp($name1, $name2);
        });

        return $authors;

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

        dispatchEventWithParams('user.update', array('user' => $this));

        return true;
    }

    public function setPassword($password)
    {
        $this->password = $password;

        $sql = 'UPDATE users SET `password` = ?, `token` = NULL WHERE `id` = ?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array($password, $this->id));

        if (!$rs) {
            return false;
        }

        dispatchEventWithParams('user.update', array('user' => $this));

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

            if (!$rs) {
                return array();
            }

            $this->meta = array();
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
            return false;
        }

        dispatchEventWithParams('user.update', array('user' => $this));

        return true;
    }

    /**
     * Remove user meta given a named array
     *
     * @param int $userId   the user id to set configs to
     * @param array  $userMeta a named array with settings and its values
     *
     * @return  boolean true if all went well
     */
    public function deleteMetaKey($userId, $metaKey)
    {
        $sql = 'DELETE FROM usermeta WHERE `user_id`=? AND `meta_key`=?' ;

        if ($GLOBALS['application']->conn->Execute($sql, array(intval($userId), $metaKey))===false) {
            return false;
        }

        dispatchEventWithParams('user.update', array('user' => $this));

        return true;
    }

    /**
     * Sets an user state to disabled/not activated
     *
     * @param  int $id the use id
     *
     * @return boolean true if the action was done
     */
    public function deactivateUser($id)
    {
        $sql = "UPDATE users SET `activated`=0 WHERE id=?";

        if ($GLOBALS['application']->conn->Execute($sql, array(intval($id))) === false) {
            return false;
        }

        dispatchEventWithParams('user.update', array('user' => $this));

        return true;
    }

    /**
     * Sets an users state to enabled/authorized/activated
     *
     * @param  int $id the use id
     *
     * @return boolean true if the action was done
     */
    public function activateUser($id)
    {
        $sql = "UPDATE users SET `activated`=1 WHERE id=?";

        if ($GLOBALS['application']->conn->Execute($sql, array(intval($id))) === false) {
            return false;
        }

        dispatchEventWithParams('user.update', array('user' => $this));

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
            return;
        }

        return ($rs->fields['num'] > 0);
    }

    /**
     * Checks if an username is already in use by frontend users
     *
     * @param  $userName The user name to log in
     * @return bool if is in use this username
     */
    public function checkIfExistsUserName($userName)
    {
        $sql = 'SELECT count(*) AS num FROM `users` WHERE username = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array($userName));

        if (!$rs) {
            return false;
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
        $sql = "UPDATE users SET `token`= ? WHERE id=?";
        $rs = $GLOBALS['application']->conn->Execute($sql, array($token, intval($id)));

        if ($rs === false) {
            return false;
        }

        dispatchEventWithParams('user.update', array('user' => $this));

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
        $sql = "UPDATE users SET `password`= ? WHERE id=?";
        $rs = $GLOBALS['application']->conn->Execute($sql, array(md5($pass), intval($id)));

        if ($rs === false) {
            return false;
        }

        dispatchEventWithParams('user.update', array('user' => $this));

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
            return array();
        }
        $users = array();
        while (!$rs->EOF) {

            $user = new \User($rs->fields['user_id']);
            $user->meta = $user->getMeta();

            // Set paywall values
            $user->paywall = 0;
            $user->last_login = 0;
            if (isset($user->meta['paywall_time_limit'])) {
                // Overload obj for ordering propouses
                $user->paywall = $user->meta['paywall_time_limit'];
                $user->meta['paywall_time_limit'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $user->meta['paywall_time_limit'],
                    new \DateTimeZone('UTC')
                );
            }
            if (isset($user->meta['last_login'])) {
                // Overload obj for ordering propouses
                $user->last_login = $user->meta['last_login'];
                $user->meta['last_login'] = \DateTime::createFromFormat(
                    'Y-m-d H:i:s',
                    $user->meta['last_login'],
                    new \DateTimeZone('UTC')
                );
            }
            $users []= $user;

            $rs->MoveNext();
        }

        return $users;
    }


    /**
     * Returns a list of User objects where the users are only registered not subscribed
     *
     * @return void
     **/
    public static function getUsersOnlyRegistered()
    {
        $sql = 'SELECT id FROM `users` WHERE type=1 ORDER BY name';
        $rs = $GLOBALS['application']->conn->Execute($sql);

        $users = array();
        if ($rs !== false) {
            while (!$rs->EOF) {
                $user = new User($rs->fields['id']);
                $user->meta = $user->getMeta();

                // Set paywall values
                $user->paywall = 0;
                $user->last_login = 0;
                if (isset($user->meta['paywall_time_limit'])) {
                    // Overload obj for ordering propouses
                    $user->paywall = $user->meta['paywall_time_limit'];
                    $user->meta['paywall_time_limit'] = \DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        $user->meta['paywall_time_limit'],
                        new \DateTimeZone('UTC')
                    );
                }
                if (isset($user->meta['last_login'])) {
                    // Overload obj for ordering propouses
                    $user->last_login = $user->meta['last_login'];
                    $user->meta['last_login'] = \DateTime::createFromFormat(
                        'Y-m-d H:i:s',
                        $user->meta['last_login'],
                        new \DateTimeZone('UTC')
                    );
                }
                $users[] = $user;

                $rs->MoveNext();
            }
        }

        // Exclude users with subscription
        $users = array_udiff(
            $users,
            self::getUsersWithSubscription(),
            function ($obj_a, $obj_b) {
                return $obj_a->id - $obj_b->id;
            }
        );

        // Reset array indexes to start on 0
        $users = array_values($users);

        return $users;
    }

    /**
     * Returns the total allowed users to create
     *
     * @param  int  $maxUsers
     *
     * @return int  total users
     **/
    public static function getTotalUsersRemaining($maxUsers = false)
    {
        // The value isn't set on DB or is set to 0 (no limit)
        if (!$maxUsers) {
            return -1;
        }

        // Get total created backend users - not masters nor only authors
        $sql = 'SELECT count(id) as total FROM `users`
                WHERE type=0 AND fk_user_group NOT REGEXP "^4$|^4,|,4,|,4$" AND
                fk_user_group NOT REGEXP "^3$"';

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            return false;
        }

        if ($rs->fields['total'] > $maxUsers) {
            return false;
        }

        return $maxUsers - $rs->fields['total'];
    }

    /**
     * Returns the total users that can be activated
     *
     * @param  int  $maxUsers
     *
     * @return int  total users
     **/
    public static function getTotalActivatedUsersRemaining($maxUsers = false)
    {
        // The value isn't set on DB or is set to 0 (no limit)
        if (!$maxUsers) {
            return -1;
        }

        // Get total activated backend users - not masters
        $sql = "SELECT count(id) as total FROM users
                WHERE type = 0 and activated = 1 and fk_user_group NOT REGEXP '^4$|^4,|,4,|,4$'";

        $rs = $GLOBALS['application']->conn->Execute($sql);

        if ($rs === false) {
            return false;
        }

        if ($rs->fields['total'] > $maxUsers) {
            return false;
        }

        return $maxUsers - $rs->fields['total'];
    }

    /**
     * Returns a list of User objects where the users has paywall subscription
     *
     * @return void
     **/
    public static function countUsersWithSubscription()
    {
        $currentTime = new \DateTime();
        $currentTime->setTimezone(new \DateTimeZone('UTC'));

        $currentTime = $currentTime->format('Y-m-d H:i:s');

        $sql = "SELECT count(user_id) as count FROM usermeta ".
               "WHERE `meta_key`= 'paywall_time_limit' && `meta_value` > ?";
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $GLOBALS['application']->conn->Execute($sql, array($currentTime));

        if ($rs === false) {
            return 0;
        }

        return $rs->fields['count'];
    }

    /**
     * Stores the register date for a frontend user
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
     * Set the last login date for a frontend user
     *
     * @return void
     **/
    public function setLastLoginDate()
    {
        $currentTime = new \DateTime();
        $currentTime->setTimezone(new \DateTimeZone('UTC'));
        $currentTime = $currentTime->format('Y-m-d H:i:s');

        $this->setMeta(array('last_login' => $currentTime));
    }

    /**
     * Process an uploaded photo for user
     *
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $file the uploaded file
     * @param string $userName the user real name
     *
     * @return Response the response object
     **/
    public function uploadUserAvatar($file, $userName)
    {
        // Generate image path and upload directory
        $relativeAuthorImagePath ="/authors/".$userName;
        $uploadDirectory =  MEDIA_IMG_PATH .$relativeAuthorImagePath;

        // Get original information of the uploaded image
        $originalFileName = $file->getClientOriginalName();
        $originalFileData = pathinfo($originalFileName);
        $fileExtension    = strtolower($originalFileData['extension']);

        // Generate new file name
        $currentTime = gettimeofday();
        $microTime   = intval(substr($currentTime['usec'], 0, 5));
        $newFileName = date("YmdHis").$microTime.".".$fileExtension;

        // Check upload directory
        if (!is_dir($uploadDirectory)) {
            \Onm\FilesManager::createDirectory($uploadDirectory);
        }

        // Upload file
        $file->move($uploadDirectory, $newFileName);

        // Get all necessary data for the photo
        $infor = new \MediaItem($uploadDirectory.'/'.$newFileName);
        $data = array(
            'title'       => $originalFileName,
            'name'        => $newFileName,
            'user_name'   => $newFileName,
            'path_file'   => $relativeAuthorImagePath,
            'nameCat'     => $userName,
            'category'    => '',
            'created'     => $infor->atime,
            'changed'     => $infor->mtime,
            'size'        => round($infor->size/1024, 2),
            'width'       => $infor->width,
            'height'      => $infor->height,
            'type'        => $infor->type,
            'author_name' => '',
        );

        // Create new photo
        $photo = new \Photo();
        $photoId = $photo->create($data);

        return $photoId;
    }

    /**
     * Returns a valid SQL WHERE clause for the given filter
     *
     * @param array $filter the list of filters
     *
     * @return string the WHERE clause
     **/
    public function buildFilter($filter)
    {
        $newFilter = '';

        if (!is_null($filter) && is_string($filter)) {
            if (preg_match('/^[ ]*where/i', $filter)) {
                $newFilter .= '  AND ' . $filter;
            }
        } elseif (!is_null($filter) && is_array($filter)) {
            $parts = array();

            if (isset($filter['base']) && !empty($filter['base'])) {
                $parts[] = $filter['base'];
            }

            if (isset($filter['type']) && $filter['type'] != '') {
                $parts[] = '`type` = '.$filter['type'].' AND activated=1';
            }

            if (isset($filter['name']) && !empty($filter['name'])) {
                $parts[] = '`name` LIKE "%' . $filter['name'] . '%" OR '.
                           '`username` LIKE "%' . $filter['name'] . '%" OR '.
                           '`email` LIKE "%' . $filter['name'] . '%"';
            }

            if (isset($filter['group']) && intval($filter['group'])>0) {
                $parts[] = '`fk_user_group` LIKE "%' . $filter['group'] . '%"';
            }

            if (count($parts) > 0) {
                $newFilter .= implode(' AND ', $parts);
            }
        }

        return $newFilter;
    }


    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array The user roles
     */
    public function getRoles()
    {
        if (!isset($this->roles)) {
            if (in_array('4', $this->id_user_group)
                || in_array('5', $this->id_user_group)
            ) {
                $this->roles = \Privilege::getPrivilegeNames();

                if (in_array('4', $this->id_user_group)) {
                    $this->roles[] = 'ROLE_MASTER';
                }

                if (in_array('5', $this->id_user_group)) {
                    $this->roles[] = 'ROLE_ADMIN';
                }
            } else {
                $this->roles = array();
                foreach ($this->id_user_group as $group) {
                    $groupPrivileges = \Privilege::getPrivilegesForUserGroup($group);
                    $this->roles = array_merge(
                        $this->roles,
                        $groupPrivileges
                    );
                }
            }

            if ((int) $this->type == 0) {
                $this->roles[] = 'ROLE_BACKEND';
            } else {
                $this->roles[] = 'ROLE_FRONTEND';
            }
        }

        return $this->roles;
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {

        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->read($this->id);

        unset($this->roles);
        unset($this->password);
        unset($this->token);

        return $this;
    }

    /**
     * Returns whether or not the given user is equivalent to this user.
     *
     * @return boolean
     */
    public function equals(UserInterface $user)
    {
        if ($user->getUsername() === $this->getUsername()) {
            return true;
        }

        return false;
    }

    /**
     * Checks whether the user's account has expired.
     *
     * @return boolean
     */
    public function isAccountNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is locked.
     *
     * @return boolean
     */
    public function isAccountNonLocked()
    {
        return true;
    }

    /**
     * Checks whether the user's credentials (password) has expired.
     *
     * @return boolean
     */
    public function isCredentialsNonExpired()
    {
        return true;
    }

    /**
     * Checks whether the user is enabled.
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return $this->activated;
    }

    /**
     * The equality comparison should neither be done by referential equality
     * nor by comparing identities (i.e. getId() === getId()).
     *
     * However, you do not need to compare every attribute, but only those that
     * are relevant for assessing whether re-authentication is required.
     *
     * @param  UserInterface $user
     * @return boolean
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof User
            && $this->getUsername() === $this->getUsername()
        ) {
            $isEqual = count($this->getRoles()) == count($user->getRoles());
            if ($isEqual) {
                foreach ($this->getRoles() as $role) {
                    $isEqual = $isEqual && in_array($role, $user->getRoles());
                }
            }
            return $isEqual;
        }

        return false;
    }

    /**
     * Returns whether or not user is in master group.
     *
     * @return boolean True if the users is in master group.
     */
    public function isMaster()
    {
        if (in_array('4', $this->id_user_group)) {
            return true;
        }

        return false;
    }

    /**
     * Returns whether or not user is in administrator group.
     *
     * @return boolean True if the users is in administrator group.
     */
    public function isAdmin()
    {
        if (in_array('4', $this->id_user_group)
            || in_array('5', $this->id_user_group)
        ) {
            return true;
        }

        return false;
    }
}
