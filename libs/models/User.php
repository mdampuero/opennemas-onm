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
     * The list of categories this user has access
     *
     * @var string
     */
    public $accesscategories = [];

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
     */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
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
     * Increases the paywall subscription time given the subscription name
     *
     * @param string $planTime the name of the plan
     */
    public function addSubscriptionLimit($planTime = 0)
    {
        $newTime = $planTime->format('Y-m-d H:i:s');

        $this->setMeta([ 'paywall_time_limit' => $newTime ]);
    }

    /**
     * Returns a list of User objects where the users has paywall subscription
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
}
