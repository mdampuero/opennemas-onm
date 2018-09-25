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
 */

use Onm\Exception\UserAlreadyExistsException;

/**
 * User
 *
 * @package    Model
 */
class User
{
    /**
     * The user id
     *
     * @var int
     */
    public $id = null;

    /**
     * The username
     *
     * @var string
     */
    public $username = null;

    /**
     * Encrypted password
     *
     * @var string
     */
    public $password = null;

    /**
     * The user email
     *
     * @var
     */
    public $email = null;

    /**
     * The user real name
     *
     * @var string
     */
    public $name = null;

    /**
     * The user blog/page url
     *
     * @var int
     */
    public $url = null;

    /**
     * The user biography
     *
     * @var int
     */
    public $bio = null;

    /**
     * The user avatar image id
     *
     * @var string
     */
    public $avatar_img_id = null;

    /**
     * The type of user
     *
     * @var string
     */
    public $type = null;

    /**
     * The login token, used for restore passwords and more
     *
     * @var string
     */
    public $token = null;

    /**
     * Whether the user can login or not
     *
     * @var string
     */
    public $activated = null;

    /**
     * The user group id
     *
     * @var id
     */
    public $id_user_group = null;

    /**
     * The list of categories this user has access
     *
     * @var string
     */
    public $accesscategories = [];

    /**
     * The user group id
     *
     * @var int
     */
    public $fk_user_group = null;

    /**
     * Meta information for the user
     *
     * @var string
     */
    public $meta = [];

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
    }

    /**
     * Creates a new user given an array of data
     *
     * @param array $data the user data
     *
     * @return boolean true if the user was created
     */
    public function create($data)
    {
        if ($this->checkIfUserExists($data)) {
            throw new UserAlreadyExistsException(
                _('Already exists one user with that information')
            );
        }

        // Transform groups array to a string separated by comma
        $data['id_user_group'] = implode(',', $data['id_user_group']);

        $values = [
            'username'      => $data['username'],
            'password'      => md5($data['password']),
            'url'           => $data['url'],
            'bio'           => $data['bio'],
            'avatar_img_id' => (int) $data['avatar_img_id'],
            'email'         => $data['email'],
            'name'          => $data['name'],
            'type'          => (int) $data['type'],
            'token'         => $data['token'],
            'activated'     => (int) $data['activated'],
            'fk_user_group' => $data['id_user_group']
        ];

        try {
            $conn = getService('orm.manager')->getConnection('instance');
            $conn->insert('users', $values);

            $this->id = $conn->lastInsertId();
        } catch (\Exception $e) {
            error_log('Unable to create the user with the provided info: ' . json_encode($values));
            return false;
        }

        /* Notice log of this action */
        logUserEvent(__METHOD__, $this->id, $data);

        dispatchEventWithParams('user.create', ['id' => $this->id]);

        return true;
    }

    /**
     * Calculates dynamic properties for the object
     *
     * @param string $property The property name
     *
     * @return mixed the property value
     */
    public function __get($property)
    {
        switch ($property) {
            case 'photo':
                return $this->getPhoto();
            case 'slug':
                return \Onm\StringUtils::generateSlug($this->name);
            default:
                return;
        }
    }

    /**
     * Checks if a property exists.
     *
     * @param string $name The property name.
     *
     * @return boolean True if the property exists. False otherwise.
     */
    public function __isset($name)
    {
        return property_exists($this, $name) || !empty($this->__get($name));
    }

    /**
     * Hydrates the object from an array of properties
     *
     * @return $this
     */
    public function load($data)
    {
        $this->id            = (int) $data['id'];
        $this->username      = $data['username'];
        $this->password      = $data['password'];
        $this->url           = $data['url'];
        $this->bio           = $data['bio'];
        $this->avatar_img_id = (int) $data['avatar_img_id'];
        $this->email         = $data['email'];
        $this->name          = $data['name'];
        $this->type          = (int) $data['type'];
        $this->token         = $data['token'];
        $this->activated     = (int) $data['activated'];
        $this->id_user_group = explode(',', $data['fk_user_group']);

        return $this;
    }

    /**
     * Loads the user information given its id
     *
     * @param int $id the user id
     *
     * @return User the user object instance
     */
    public function read($id)
    {
        try {
            $conn = getService('orm.manager')->getConnection('instance');
            $rs   = $conn->fetchAll(
                'SELECT * FROM users WHERE id = ?',
                [ intval($id) ]
            );

            if (!$rs) {
                return null;
            }

            $this->load($rs[0]);

            // Get user meta information
            $this->meta = $this->getMeta();

            return $this;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the user information given an array of data
     *
     * @param array $data the new user data
     *
     * @return boolean true if the user was updated
     */
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
        $conn = getService('orm.manager')->getConnection('instance');

        $conn->beginTransaction();

        // Transform groups array to a string separated by commas
        $data['id_user_group'] = implode(',', $data['id_user_group']);

        $values = [
            'username'      => $data['username'],
            'url'           => $data['url'],
            'bio'           => $data['bio'],
            'avatar_img_id' => (int) $data['avatar_img_id'],
            'email'         => $data['email'],
            'name'          => $data['name'],
            'activated'     => (int) $data['activated'],
            'id_user_group' => $data['id_user_group'],
            'type'          => (int) $data['type'],
        ];

        if (isset($data['password'])
            && (strlen($data['password']) > 0)
            && $data['password'] === $data['passwordconfirm']
        ) {
            $values['password'] = md5($data['password']);
        }

        try {
            $conn->update('users', $values, [ 'id' => intval($data['id']) ]);
        } catch (\Exception $e) {
            $conn->rollBack();
            return false;
        }

        // Finish transaction
        $conn->commit();

        $this->id = $data['id'];

        /* Notice log of this action */
        logUserEvent(__METHOD__, $this->id, $data);

        dispatchEventWithParams('user.update', [ 'id' => $this->id ]);

        return true;
    }

    /**
     * Deletes an user given its id
     *
     * @param int $id the user id
     *
     * @return boolean true if the user was deleted
     */
    public function delete($id)
    {
        try {
            getService('orm.manager')->getConnection('instance')
                ->delete('users', [ 'id' => intval($id)]);

            if (!$this->deleteMeta($id)) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }

        /* Notice log of this action */
        logUserEvent(__METHOD__, $id);

        dispatchEventWithParams('user.delete', [ 'id' => $this->id ]);

        return true;
    }

    /**
     * Returns the Photo object that represents the user avatar
     *
     * @return Photo the photo object
     */
    public function getPhoto()
    {
        $photo = null;

        if (!property_exists($this, 'photo')
            || ((property_exists($this, 'photo')
            && !is_object($this->photo)
            && $this->avatar_img_id != 0))
        ) {
            $this->photo = $photo = getService('entity_repository')
                ->find('Photo', $this->avatar_img_id);
        }

        return $photo;
    }

    /**
     * Checks if a user exists given some information.
     *
     * @param array $data tuple with the username and email params
     *
     * @return boolean true if user exists
     */
    public function checkIfUserExists($data)
    {
        // FIXME: why username and email twice in different order?
        $sql    = "SELECT id FROM users WHERE username=? OR email=? OR email=? OR username=?";
        $values = [ $data['username'], $data['email'], $data['username'], $data['email'] ];

        $rs = getService('orm.manager')->getConnection('instance')
            ->fetchAll($sql, $values);

        // If is update, check for more than 1 result
        if (isset($data['id']) && count($rs) == 1 && $rs[0]['id'] == $data['id']) {
            return false;
        }

        return !empty($rs);
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
        $rs  = getService('orm.manager')->getConnection('instance')
            ->fetchAll($sql, [ $email ]);

        if (!$rs) {
            return null;
        }

        $this->load($rs[0]);

        return $this;
    }

    /**
     * Check if the token for registration is same user token and get user data
     *
     * @param string $token the token
     *
     * @return user if exists false otherwise
     */
    public function findByToken($token)
    {
        if (empty($token)) {
            return null;
        }

        $sql = 'SELECT * FROM users WHERE token=?';
        $rs  = getService('orm.manager')->getConnection('instance')
            ->fetchAll($sql, [ $token ]);

        if (!$rs) {
            return null;
        }

        $this->load($rs[0]);

        return $this;
    }

    /**
     * Sets user configurations given a named array
     *
     * @param array  $userMeta a named array with settings and its values
     *
     * @return  boolean true if all went well
     */
    public function setMeta($userMeta = [])
    {
        try {
            foreach ($userMeta as $key => $value) {
                $this->meta[$key] = $value;

                $rs = getService('orm.manager')->getConnection('instance')->executeUpdate(
                    "REPLACE INTO usermeta (`user_id`, `meta_key`, `meta_value`) VALUES (?, ?, ?)",
                    [ $this->id, $key, $value ]
                );
            }

            dispatchEventWithParams('user.update', [ 'id' => $this->id ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function setPassword($password)
    {
        $this->password = $password;

        try {
            $rs = getService('orm.manager')->getConnection('instance')->update(
                "users",
                [ 'password' => $password, 'token' => null ],
                [ 'id'       => $this->id ]
            );

            dispatchEventWithParams('user.update', [ 'id' => $this->id ]);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Returns the values of a user meta option
     *
     * @param string/array $meta an array or an string with the user meta name
     *
     * @return array/string an 2-dimensional array or an string with the user option values
     */
    public function getMeta($meta = null)
    {
        if (count($this->meta) <= 0) {
            try {
                $rs = getService('orm.manager')->getConnection('instance')->fetchAll(
                    "SELECT * FROM usermeta WHERE `user_id` = ?",
                    [ $this->id ]
                );

                $this->meta = [];
                foreach ($rs as $value) {
                    $this->meta[$value['meta_key']] = $value['meta_value'];
                }
            } catch (\Exception $e) {
                error_log($e->getMessage());
                return false;
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
        try {
            $rs = getService('orm.manager')->getConnection('instance')->delete(
                "usermeta",
                [ 'user_id' => $userId, ]
            );

            dispatchEventWithParams('user.update', [ 'id' => $this->id ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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
        try {
            $rs = getService('orm.manager')->getConnection('instance')->delete(
                "usermeta",
                [
                    'user_id'  => $userId,
                    'meta_key' => $metaKey,
                ]
            );

            dispatchEventWithParams('user.update', [ 'id' => $this->id ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
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
        try {
            $rs = getService('orm.manager')->getConnection('instance')->fetchAssoc(
                'SELECT count(*) AS num  FROM `users` WHERE email = ?',
                [ $email ]
            );

            return $rs['num'] > 0;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Checks if an username is already in use by frontend users
     *
     * @param  $userName The user name to log in
     * @return bool if is in use this username
     */
    public function checkIfExistsUserName($userName)
    {
        try {
            $rs = getService('orm.manager')->getConnection('instance')->fetchAssoc(
                'SELECT count(*) AS num FROM `users` WHERE username = ?',
                [ $userName ]
            );

            return $rs['num'] > 0;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Generate new token and update user with it
     *
     * @param int $id the user id
     * @param string $token the new user token
     *
     * @return boolen
     */
    public function updateUserToken($id, $token)
    {
        try {
            $rs = getService('orm.manager')->getConnection('instance')->update(
                "users",
                [ 'token' => $token ],
                [ 'id'    => (int) $id ]
            );

            dispatchEventWithParams('user.update', [ 'id' => $this->id ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Updates the users password
     *
     * @param int $id the user id
     * @param string $pass the new user password
     *
     * @return boolean true if the pass was updated
     */
    public function updateUserPassword($id, $pass)
    {
        try {
            $rs = getService('orm.manager')->getConnection('instance')->update(
                "users",
                [ 'password' => md5($pass) ],
                [ 'id' => (int) $id ]
            );

            dispatchEventWithParams('user.update', [ 'id' => $this->id ]);

            return true;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Increases the paywall subscription time given the subscription name
     *
     * @param string $planTime the name of the plan
     *
     * @return void
     */
    public function addSubscriptionLimit($planTime = 0)
    {
        $newTime = $planTime->format('Y-m-d H:i:s');

        $this->setMeta([ 'paywall_time_limit' => $newTime ]);
    }

    /**
     * Returns a list of User objects where the users has paywall subscription
     *
     * @return void
     */
    public static function getUsersWithSubscription($config = [])
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date = $date->format('Y-m-d H:i:s');

        $oql = sprintf('paywall_time_limit > "%s"', $date);

        if (array_key_exists('limit', $config) && $config['limit'] > 0) {
            $oql = ' limit ' . $config['limit'];
        }

        return getService('orm.manager')
            ->getRepository('User', 'instance')
            ->findBy($oql);
    }

    /**
     * Returns a list of User objects where the users are only registered not subscribed
     *
     * @return void
     */
    public static function getUsersOnlyRegistered()
    {
        $sql = 'SELECT * FROM `users`'
            . ' WHERE type = 1 AND id NOT IN '
            . '(SELECT user_id FROM usermeta WHERE meta_key = "paywall_time_limit")';

        return getService('orm.manager')
            ->getRepository('User', 'instance')
            ->findBySql($sql);
    }

    /**
     * Returns a list of User objects where the users has paywall subscription
     *
     * @return integer The number of users with paywall subscription.
     */
    public static function countUsersWithSubscription()
    {
        $date = new \DateTime();
        $date->setTimezone(new \DateTimeZone('UTC'));
        $date = $date->format('Y-m-d H:i:s');

        $oql = sprintf('paywall_time_limit > "%s"', $date);

        return getService('orm.manager')->getRepository('User', 'instance')
            ->countBy($oql);
    }

    /**
     * Process an uploaded photo for user
     *
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $file the uploaded file
     * @param string $userName the user real name
     *
     * @return Response the response object
     */
    public function uploadUserAvatar($file, $userName)
    {
        // Generate image path and upload directory
        $relativeAuthorImagePath = "/authors/" . $userName;
        $uploadDirectory         = MEDIA_IMG_PATH . $relativeAuthorImagePath;

        // Get original information of the uploaded/local image
        $originalFileName = $file->getBaseName();
        $fileExtension    = $file->guessExtension();

        // Generate new file name
        $currentTime = gettimeofday();
        $microTime   = intval(substr($currentTime['usec'], 0, 5));
        $newFileName = date("YmdHis") . $microTime . "." . $fileExtension;

        // Check upload directory
        if (!is_dir($uploadDirectory)) {
            \Onm\FilesManager::createDirectory($uploadDirectory);
        }

        // Upload file
        $file->move($uploadDirectory, $newFileName);

        // Get all necessary data for the photo
        $infor = new \MediaItem($uploadDirectory . '/' . $newFileName);
        $data  = [
            'title'       => $originalFileName,
            'name'        => $newFileName,
            'user_name'   => $newFileName,
            'path_file'   => $relativeAuthorImagePath,
            'nameCat'     => $userName,
            'category'    => '',
            'created'     => $infor->atime,
            'changed'     => $infor->mtime,
            'size'        => round($infor->size / 1024, 2),
            'width'       => $infor->width,
            'height'      => $infor->height,
            'type'        => $infor->type,
            'author_name' => '',
        ];

        // Create new photo
        $photo   = new \Photo();
        $photoId = $photo->create($data);

        return $photoId;
    }
}
