<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Explanation for this class.
 *
 * @package    Onm
 * @subpackage Utils
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class SessionManager implements ArrayAccess
{

    /**
     * Defines the max age of a session in seconds. Used for expire sessions.
     *
     * @var string
     **/
    const MAX_SESSION_LIFETIME = 259200;

    /**
     * The directory where sessions are saved.
     *
     * @var sessionDirectory
     **/
    protected $sessionDirectory = SYS_SESSION_PATH;

    /**
     * The singleton instance for this object.
     *
     * @var SessionManager
     **/
    protected static $_singleton = null;

    /**
     * Initializes this object.
     *
     * @param string $sessionSavePath path where save session files into.
     */
    private function __construct($sessionSavePath)
    {
        $this->sessionDirectory = realpath($sessionSavePath);
    }

    /**
     * Retrieves the singleton instance and initializes it if not available.
     *
     * @param string $sessionSavePath path where save sessions file into.
     *
     * @return SessionManager The instance for SessionManager
     *
     **/
    public static function getInstance($sessionSavePath)
    {
        if (!isset($sessionSavePath)) {
            $sessionSavePath = session_save_path();
        }
        if ( is_null(self::$_singleton)) {
            self::$_singleton = new SessionManager($sessionSavePath);
        }

        return( self::$_singleton );
    }

    /**
     * Initiliazes the session handler with all the settings.
     *
     * @param int $lifetime the time in seconds that the sessions will be valid.
     **/
    public function bootstrap($lifetime=null)
    {
        // Save the actual lifetime for this session in the session manager
        if (is_null($lifetime)) {
            $this->lifetime = self::MAX_SESSION_LIFETIME;
        } else {
            $this->lifetime = $lifetime;
        }

        if (is_null($lifetime)
            && !isset($_COOKIE['default_expire'])
        ) {
            $lifetime = self::MAX_SESSION_LIFETIME; // 60 minutes by default
        } elseif (isset($_COOKIE['default_expire'])) {
            $lifetime = intval($_COOKIE['default_expire']);
        }

        // Set session_save_path
        session_save_path($this->sessionDirectory);

        // set the cache expire to $lifetime minutes
        session_cache_expire($lifetime);

        // public, private, nocache, private_no_expire
        //  http://cz.php.net/manual/en/function.session-cache-limiter.php
        session_cache_limiter('nocache');

        // Now we can call to session_start
        session_start();
    }

    /**
     * Magic method for setting a key-value in the session variable.
     *
     * @param string $name  the name of the variable.
     * @param string $value the value for the variable.
     **/
    public function __set($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    public function __get($name)
    {
        if (!isset($_SESSION[$name])) {
            return null;
        }

        return $_SESSION[$name];
    }

    /**
    * Defined by ArrayAccess interface
    * Set a value given it's key e.g. $A['title'] = 'foo';
    * @param mixed key (string or integer)
    * @param mixed value
    * @return void
    */
    public function offsetSet($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    /**
    * Defined by ArrayAccess interface
    * Return a value given it's key e.g. echo $A['title'];
    * @param mixed key (string or integer)
    * @return mixed value
    */
    public function offsetGet($key)
    {
        return($_SESSION[$key]);
    }

    /**
    * Defined by ArrayAccess interface
    * Unset a value by it's key e.g. unset($A['title']);
    * @param mixed key (string or integer)
    * @return void
    */
    public function offsetUnset($key)
    {
        unset($_SESSION[$key]);
    }

    /**
    * Defined by ArrayAccess interface
    * Check value exists, given it's key e.g. isset($A['title'])
    * @param mixed key (string or integer)
    * @return boolean
    */
    public function offsetExists($key)
    {
        return isset($_SESSION[$key]);
    }

    /* Métodos para el control de la sesión y los usuarios activos */
    /**
     * Returns array of sessions openned.
     *
     * @return array the array of sessions
     **/
    public function getSessions()
    {
        $sessionDirectory = $this->sessionDirectory;
        $sessions = array();

        if (file_exists($sessionDirectory) && is_dir($sessionDirectory)) {

            $sessionFiles = glob($sessionDirectory.DS."sess*");
            foreach ($sessionFiles as $file) {
                $contents = file_get_contents($file);
                if (!empty($contents)) {
                    $session =
                        SessionManager::unserializeSession($contents);

                    if (isset($session['userid'])) {
                        $sessions[] = array(
                            'userid'     => $session['userid'],
                            'username'   => $session['username'],
                            'isAdmin'    => $session['isAdmin'],
                            'expire'     => $session['expire'],
                            'authMethod' => $session['authMethod'],
                        );
                   }

                } else {
                    @unlink($sessionDirectory.'/'.$file);
                }
            }
        }

        return $sessions;
    }

    /**
     * Removes one user session given an user id.
     *
     * @param int $userid the user id.
     **/
    public function purgeSession($userId)
    {
        $sessionDirectory = $this->sessionDirectory;

        if (is_dir($sessionDirectory)) {
            $sessionFiles = glob($sessionDirectory.DS."sess*");
            foreach ($sessionFiles as $session) {
                $contents = file_get_contents($session);
                if (!empty($contents)) {
                    $sessionContents = SessionManager::unserializeSession($contents);
                    if (isset($sessionContents['userid'])
                        && ($sessionContents['userid'] == $userId)
                    ) {
                        @unlink($session);
                    }
                } else {
                    @unlink($session);
                }
            }
        }
        apc_delete(APC_PREFIX ."_"."num_sessions");
    }

    /**
     * Unserializes the session information.
     * http://es2.php.net/manual/en/function.session-decode.php#79244
     * @param string $data the serialized data for the session.
     **/
    public function unserializeSession($data)
    {
        $vars = preg_split(
            '/([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff^|]*)\|/',
            $data,
            -1,
            PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE
        );

        $i=0;
        $result= array();
        while (isset($vars[$i])) {
            //TODO: the @ was written cause eht unserialize raises an notice
            // Try to fix this with other way
            $result[$vars[$i++]]=@unserialize($vars[$i]);
            $i++;
        }

        return $result;
    }

    /**
     * Deletes all expired session files from instance tmp folder
     *
     * This function deletes all the expired session files from the instance tmp
     * folder. A session file is marked as expired if it has a modification time
     * greater than 72 hours.
     * @return void
     **/
    public function cleanExpiredSessionFiles()
    {
        $sessionDir = $this->sessionDirectory;  // your sessions directory
        $compareTime = time() - self::MAX_SESSION_LIFETIME;  // Expire 72 hours
        $count = 0;

        foreach (glob($sessionDir."/*") as $file) {
            $contents = file_get_contents($file);
            $sessionContents = SessionManager::unserializeSession($contents);
            $time = time();
            if ($compareTime >= filemtime($file)
                || (is_array($sessionContents)
                    && array_key_exists('expire', $sessionContents)
                    && ($sessionContents['expire'] < $time))
            ) {
                if (unlink($file)) {
                    $count++;
                }
            }
        }
        $GLOBALS['application']->logger->debug("Expired session files deleted. {$count}");
    }

}
