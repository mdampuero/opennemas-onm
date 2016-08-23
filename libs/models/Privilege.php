<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Class Privilege
 *
 * Class to manage privileges
 *
 * @package    Onm
 * @subpackage Acl
 **/
class Privilege
{
    /**
     * The privilege id
     *
     * @var int
     **/
    public $pk_privilege      = null;

    /**
     * The privilege description
     *
     * @var string
     **/
    public $description       = null;

    /**
     * The privilege name
     *
     * @var string
     **/
    public $name              = null;

    /**
     * The privilege module name
     *
     * @var string
     **/
    public $module            = null;

    /**
     * the list of available privileges
     *
     * @var array
     **/
    public static $privileges = null;

    /**
     * Initializes the object isntance
     *
     * @param int $id Privilege Id
     *
     * @return Privilege the object instance
    */
    public function __construct($id = null)
    {
        self::loadPrivileges();

        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Reads a privilege information given the id
     *
     * @param int $id Privilege Id
     *
     * @return Privilege the privilege object
     */
    public function read($id)
    {
        foreach (self::$privileges as $privilege) {
            if ($privilege['pk_privilege'] == $id) {
                $this->load($privilege);

                return $this;
            }
        }
    }

    /**
     * Load properties in this instance
     *
     * @param  array|stdClass $data
     *
     * @return Privilege      Return this instance to chaining of methods
     */
    public function load($data)
    {
        $properties = $data;
        if (!is_array($data)) {
            $properties = get_object_vars($data);
        }

        foreach ($properties as $k => $v) {
            $this->{$k} = $v;
        }

        $this->pk_privilege = (int) $this->pk_privilege;

        // Lazy setting
        $this->id = $this->pk_privilege;

        return $this; // chaining methods
    }

    /**
     * Get privileges of system
     *
     * @param array Array of Privileges
     *
     * @return array the list of Privileges objects
     */
    public function find()
    {
        foreach (self::$privileges as $privilegeData) {
            $privilege = new Privilege();
            $privilege->load($privilegeData);

            $privileges[]  = $privilege;
        }

        return $privileges;
    }

    /**
     * Get modules name
     *
     * @return array Array of string
     **/
    public function getModuleNames()
    {
        $modules = array();
        foreach (self::$privileges as $privilege) {
            $modules []= $privilege['module'];
        }

        $modules = array_unique($modules);
        asort($modules);

        return array_values($modules);
    }

    /**
     * Returns all the privileges names
     *
     * @return array the list of privilege names
     **/
    public static function getPrivilegeNames()
    {
        $privileges = self::loadPrivileges();
        $privilegeNames = array();
        foreach ($privileges as $value) {
            $privilegeNames[] = $value['name'];
        }

        return $privilegeNames;
    }

    /**
     * Get privileges group by modules
     *
     * @param string $filter where condition for check.
     *
     * @return array modules with each privileges
     *
     **/
    public function getPrivilegesByModules()
    {
        $groupedPrivileges = array();
        foreach (self::$privileges as $privilegeData) {
            $privilege = new Privilege();
            $privilege->load($privilegeData);

            if (!array_key_exists($privilegeData['module'], $groupedPrivileges)) {
                $groupedPrivileges[$privilegeData['module']] = array();
            }
            $groupedPrivileges[$privilegeData['module']] []= $privilege;
        }

        ksort($groupedPrivileges);

        return $groupedPrivileges;
    }

    /**
     * Get privileges for a given user group id
     *
     * @param int $userGroupId the id of the user group
     *
     * @return array the list of privilege names
     *
     **/
    public static function getPrivilegesForUserGroup($userGroupId)
    {
        self::loadPrivileges();

        $privileges = [];
        try {
            $rs = getServicE('dbal_connection')->fetchAll(
                'SELECT pk_fk_privilege FROM user_groups_privileges '
                .'WHERE pk_fk_user_group = ? ORDER BY pk_fk_privilege',
                [ intval($userGroupId) ]
            );

            foreach ($rs as $privilegeDB) {
                if (array_key_exists($privilegeDB['pk_fk_privilege'], self::$privileges)) {
                    $privilege = self::$privileges[$privilegeDB['pk_fk_privilege']];
                    $privileges[$privilege['pk_privilege']] = $privilege['name'];
                }
            }

            return $privileges;
        } catch (\Exception $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Initializes the internal array of privileges
     *
     * @return void
     **/
    private static function loadPrivileges()
    {
        self::$privileges = array(
            1 => array(
                'pk_privilege' => '1',
                'name'         => 'CATEGORY_ADMIN',
                'description'  => _('List'),
                'module'       => 'CATEGORY',
            ),
            2 => array(
                'pk_privilege' => '2',
                'name'         => 'CATEGORY_AVAILABLE',
                'description'  => _('Activate/deactivate'),
                'module'       => 'CATEGORY',
            ),
            3 => array(
                'pk_privilege' => '3',
                'name'         => 'CATEGORY_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'CATEGORY',
            ),
            4 => array(
                'pk_privilege' => '4',
                'name'         => 'CATEGORY_DELETE',
                'description'  => _('Remove'),
                'module'       => 'CATEGORY',
            ),
            5 => array(
                'pk_privilege' => '5',
                'name'         => 'CATEGORY_CREATE',
                'description'  => _('Create'),
                'module'       => 'CATEGORY',
            ),
            6 => array(
                'pk_privilege' => '6',
                'name'         => 'ARTICLE_ADMIN',
                'description'  => _('List'),
                'module'       => 'ARTICLE',
            ),
            7 => array(
                'pk_privilege' => '7',
                'name'         => 'ARTICLE_FRONTPAGE',
                'description'  => _('Manage frontpages'),
                'module'       => 'ARTICLE',
            ),
            8 => array(
                'pk_privilege' => '8',
                'name'         => 'ARTICLE_PENDINGS',
                'description'  => _('List pending articles'),
                'module'       => 'ARTICLE',
            ),
            9 => array(
                'pk_privilege' => '9',
                'name'         => 'ARTICLE_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'ARTICLE',
            ),
            10 => array(
                'pk_privilege' => '10',
                'name'         => 'ARTICLE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'ARTICLE',
            ),
            11 => array(
                'pk_privilege' => '11',
                'name'         => 'ARTICLE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'ARTICLE',
            ),
            12 => array(
                'pk_privilege' => '12',
                'name'         => 'ARTICLE_CREATE',
                'description'  => _('Create'),
                'module'       => 'ARTICLE',
            ),
            13 =>  array(
                'pk_privilege' => '13',
                'name'         => 'ARTICLE_ARCHIVE',
                'description'  => _('Arquive/unarquive'),
                'module'       => 'ARTICLE',
            ),
            14 =>  array(
                'pk_privilege' => '14',
                'name'         => 'ARTICLE_FAVORITE',
                'description'  => _('Manager favorite flag'),
                'module'       => 'ARTICLE',
            ),
            15 => array(
                'pk_privilege' => '15',
                'name'         => 'ARTICLE_HOME',
                'description'  => _('Manage home frontpage'),
                'module'       => 'ARTICLE',
            ),
            16 => array(
                'pk_privilege' => '16',
                'name'         => 'ARTICLE_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'ARTICLE',
            ),
            17 => array(
                'pk_privilege' => '17',
                'name'         => 'ARTICLE_ARCHIVE_ADMI',
                'description'  => _('List articles in arquive'),
                'module'       => 'ARTICLE',
            ),
            18 => array(
                'pk_privilege' => '18',
                'name'         => 'ADVERTISEMENT_ADMIN',
                'description'  => _('List'),
                'module'       => 'ADVERTISEMENT',
            ),
            19 => array(
                'pk_privilege' => '19',
                'name'         => 'ADVERTISEMENT_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'ADVERTISEMENT',
            ),
            20 => array(
                'pk_privilege' => '20',
                'name'         => 'ADVERTISEMENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'ADVERTISEMENT',
            ),
            21 => array(
                'pk_privilege' => '21',
                'name'         => 'ADVERTISEMENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'ADVERTISEMENT',
            ),
            22 => array(
                'pk_privilege' => '22',
                'name'         => 'ADVERTISEMENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'ADVERTISEMENT',
            ),
            23 => array(
                'pk_privilege' => '23',
                'name'         => 'ADVERTISEMENT_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'ADVERTISEMENT',
            ),
            24 => array(
                'pk_privilege' => '24',
                'name'         => 'ADVERTISEMENT_HOME',
                'description'  => _('Manage advertisements for homepage'),
                'module'       => 'ADVERTISEMENT',
            ),
            25 => array(
                'pk_privilege' => '25',
                'name'         => 'ADVERTISEMENT_FAVORITE',
                'description'  => _('Manage favorite flag'),
                'module'       => 'ADVERTISEMENT',
            ),
            26 => array(
                'pk_privilege' => '26',
                'name'         => 'OPINION_ADMIN',
                'description'  => _('List'),
                'module'       => 'OPINION',
            ),
            27 => array(
                'pk_privilege' => '27',
                'name'         => 'OPINION_FRONTPAGE',
                'description'  => _('Manage frontpage'),
                'module'       => 'OPINION',
            ),
            28 => array(
                'pk_privilege' => '28',
                'name'         => 'OPINION_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'OPINION',
            ),
            29 => array(
                'pk_privilege' => '29',
                'name'         => 'OPINION_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'OPINION',
            ),
            30 => array(
                'pk_privilege' => '30',
                'name'         => 'OPINION_HOME',
                'description'  => _('Administrate opinion widget'),
                'module'       => 'OPINION',
            ),
            31 => array(
                'pk_privilege' => '31',
                'name'         => 'OPINION_DELETE',
                'description'  => _('Delete'),
                'module'       => 'OPINION',
            ),
            32 => array(
                'pk_privilege' => '32',
                'name'         => 'OPINION_CREATE',
                'description'  => _('Create'),
                'module'       => 'OPINION',
            ),
            33 => array(
                'pk_privilege' => '33',
                'name'         => 'OPINION_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'OPINION',
            ),
            34 => array(
                'pk_privilege' => '34',
                'name'         => 'COMMENT_ADMIN',
                'description'  => _('List'),
                'module'       => 'COMMENT',
            ),
            35 => array(
                'pk_privilege' => '35',
                'name'         => 'COMMENT_POLL',
                'description'  => _('Manage poll comments'),
                'module'       => 'COMMENT',
            ),
            37 => array(
                'pk_privilege' => '37',
                'name'         => 'COMMENT_AVAILABLE',
                'description'  => _('Approve/reject'),
                'module'       => 'COMMENT',
            ),
            38 => array(
                'pk_privilege' => '38',
                'name'         => 'COMMENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'COMMENT',
            ),
            39 => array(
                'pk_privilege' => '39',
                'name'         => 'COMMENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'COMMENT',
            ),
            40 => array(
                'pk_privilege' => '40',
                'name'         => 'COMMENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'COMMENT',
            ),
            41 => array(
                'pk_privilege' => '41',
                'name'         => 'COMMENT_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'COMMENT',
            ),
            42 => array(
                'pk_privilege' => '42',
                'name'         => 'ALBUM_ADMIN',
                'description'  => _('List'),
                'module'       => 'ALBUM',
            ),
            43 => array(
                'pk_privilege' => '43',
                'name'         => 'ALBUM_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'ALBUM',
            ),
            44 => array(
                'pk_privilege' => '44',
                'name'         => 'ALBUM_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'ALBUM',
            ),
            45 => array(
                'pk_privilege' => '45',
                'name'         => 'ALBUM_DELETE',
                'description'  => _('Delete'),
                'module'       => 'ALBUM',
            ),
            46 => array(
                'pk_privilege' => '46',
                'name'         => 'ALBUM_CREATE',
                'description'  => _('Create'),
                'module'       => 'ALBUM',
            ),
            47 => array(
                'pk_privilege' => '47',
                'name'         => 'ALBUM_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'ALBUM',
            ),
            48 => array(
                'pk_privilege' => '48',
                'name'         => 'VIDEO_ADMIN',
                'description'  => _('List'),
                'module'       => 'VIDEO',
            ),
            49 => array(
                'pk_privilege' => '49',
                'name'         => 'VIDEO_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'VIDEO',
            ),
            50 => array(
                'pk_privilege' => '50',
                'name'         => 'VIDEO_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'VIDEO',
            ),
            51 => array(
                'pk_privilege' => '51',
                'name'         => 'VIDEO_DELETE',
                'description'  => _('Delete'),
                'module'       => 'VIDEO',
            ),
            52 => array(
                'pk_privilege' => '52',
                'name'         => 'VIDEO_CREATE',
                'description'  => _('Create'),
                'module'       => 'VIDEO',
            ),
            53 => array(
                'pk_privilege' => '53',
                'name'         => 'VIDEO_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'VIDEO',
            ),
            60 => array(
                'pk_privilege' => '60',
                'name'         => 'PHOTO_ADMIN',
                'description'  => _('List'),
                'module'       => 'IMAGE',
            ),
            61 => array(
                'pk_privilege' => '61',
                'name'         => 'PHOTO_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'IMAGE',
            ),
            62 => array(
                'pk_privilege' => '62',
                'name'         => 'PHOTO_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'IMAGE',
            ),
            63 => array(
                'pk_privilege' => '63',
                'name'         => 'PHOTO_DELETE',
                'description'  => _('Delete'),
                'module'       => 'IMAGE',
            ),
            64 => array(
                'pk_privilege' => '64',
                'name'         => 'PHOTO_CREATE',
                'description'  => _('Create/upload'),
                'module'       => 'IMAGE',
            ),
            65 => array(
                'pk_privilege' => '65',
                'name'         => 'PHOTO_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'IMAGE',
            ),
            66 => array(
                'pk_privilege' => '66',
                'name'         => 'STATIC_PAGE_ADMIN',
                'description'  => _('List'),
                'module'       => 'STATIC',
            ),
            67 => array(
                'pk_privilege' => '67',
                'name'         => 'STATIC_PAGE_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'STATIC',
            ),
            68 => array(
                'pk_privilege' => '68',
                'name'         => 'STATIC_PAGE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'STATIC',
            ),
            69 => array(
                'pk_privilege' => '69',
                'name'         => 'STATIC_PAGE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'STATIC',
            ),
            70 => array(
                'pk_privilege' => '70',
                'name'         => 'STATIC_PAGE_CREATE',
                'description'  => _('Create'),
                'module'       => 'STATIC',
            ),
            71 => array(
                'pk_privilege' => '71',
                'name'         => 'KIOSKO_ADMIN',
                'description'  => _('List'),
                'module'       => 'KIOSKO',
            ),
            72 => array(
                'pk_privilege' => '72',
                'name'         => 'KIOSKO_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'KIOSKO',
            ),
            73 => array(
                'pk_privilege' => '73',
                'name'         => 'KIOSKO_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'KIOSKO',
            ),
            74 => array(
                'pk_privilege' => '74',
                'name'         => 'KIOSKO_DELETE',
                'description'  => _('Delete'),
                'module'       => 'KIOSKO',
            ),
            75 => array(
                'pk_privilege' => '75',
                'name'         => 'KIOSKO_CREATE',
                'description'  => _('Create'),
                'module'       => 'KIOSKO',
            ),
            76 => array(
                'pk_privilege' => '76',
                'name'         => 'KIOSKO_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'KIOSKO',
            ),
            77 => array(
                'pk_privilege' => '77',
                'name'         => 'POLL_ADMIN',
                'description'  => _('List'),
                'module'       => 'POLL',
            ),
            78 => array(
                'pk_privilege' => '78',
                'name'         => 'POLL_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'POLL',
            ),
            79 => array(
                'pk_privilege' => '79',
                'name'         => 'POLL_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'POLL',
            ),
            80 => array(
                'pk_privilege' => '80',
                'name'         => 'POLL_DELETE',
                'description'  => _('Delete'),
                'module'       => 'POLL',
            ),
            81 => array(
                'pk_privilege' => '81',
                'name'         => 'POLL_CREATE',
                'description'  => _('Create'),
                'module'       => 'POLL',
            ),
            82 => array(
                'pk_privilege' => '82',
                'name'         => 'AUTHOR_ADMIN',
                'description'  => _('List'),
                'module'       => 'AUTHOR',
            ),
            83 => array(
                'pk_privilege' => '83',
                'name'         => 'AUTHOR_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'AUTHOR',
            ),
            84 => array(
                'pk_privilege' => '84',
                'name'         => 'AUTHOR_DELETE',
                'description'  => _('Delete'),
                'module'       => 'AUTHOR',
            ),
            85 => array(
                'pk_privilege' => '85',
                'name'         => 'AUTHOR_CREATE',
                'description'  => _('Create'),
                'module'       => 'AUTHOR',
            ),
            86 => array(
                'pk_privilege' => '86',
                'name'         => 'USER_ADMIN',
                'description'  => _('List'),
                'module'       => 'USER',
            ),
            87 => array(
                'pk_privilege' => '87',
                'name'         => 'USER_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'USER',
            ),
            88 => array(
                'pk_privilege' => '88',
                'name'         => 'USER_DELETE',
                'description'  => _('Delete'),
                'module'       => 'USER',
            ),
            89 => array(
                'pk_privilege' => '89',
                'name'         => 'USER_CREATE',
                'description'  => _('Create'),
                'module'       => 'USER',
            ),
            90 => array(
                'pk_privilege' => '90',
                'name'         => 'PCLAVE_ADMIN',
                'description'  => _('List'),
                'module'       => 'PCLAVE',
            ),
            91 => array(
                'pk_privilege' => '91',
                'name'         => 'PCLAVE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'PCLAVE',
            ),
            92 => array(
                'pk_privilege' => '92',
                'name'         => 'PCLAVE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'PCLAVE',
            ),
            93 => array(
                'pk_privilege' => '93',
                'name'         => 'PCLAVE_CREATE',
                'description'  => _('Create'),
                'module'       => 'PCLAVE',
            ),
            95 => array(
                'pk_privilege' => '95',
                'name'         => 'GROUP_ADMIN',
                'description'  => _('List'),
                'module'       => 'GROUP',
            ),
            96 => array(
                'pk_privilege' => '96',
                'name'         => 'GROUP_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'GROUP',
            ),
            97 => array(
                'pk_privilege' => '97',
                'name'         => 'GROUP_DELETE',
                'description'  => _('Delete'),
                'module'       => 'GROUP',
            ),
            99 => array(
                'pk_privilege' => '99',
                'name'         => 'GROUP_CREATE',
                'description'  => _('Create'),
                'module'       => 'GROUP',
            ),
            104 => array(
                'pk_privilege' => '104',
                'name'         => 'ATTACHMENT_ADMIN',
                'description'  => _('List'),
                'module'       => 'FILE',
            ),
            105 => array(
                'pk_privilege' => '105',
                'name'         => 'ATTACHMENT_FRONTS',
                'description'  => _('File Fronts'),
                'module'       => 'FILE',
            ),
            106 => array(
                'pk_privilege' => '106',
                'name'         => 'ATTACHMENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'FILE',
            ),
            107 => array(
                'pk_privilege' => '107',
                'name'         => 'ATTACHMENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'FILE',
            ),
            108 => array(
                'pk_privilege' => '108',
                'name'         => 'ATTACHMENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'FILE',
            ),
            112 => array(
                'pk_privilege' => '112',
                'name'         => 'NEWSLETTER_ADMIN',
                'description'  => _('Manage Newsletter'),
                'module'       => 'NEWSLETTER',
            ),
            113 => array(
                'pk_privilege' => '113',
                'name'         => 'BACKEND_ADMIN',
                'description'  => _('Backend manager'),
                'module'       => 'BACKEND',
            ),
            114 => array(
                'pk_privilege' => '114',
                'name'         => 'CACHE_TPL_ADMIN',
                'description'  => _('Manage caches'),
                'module'       => 'CACHE',
            ),
            115 => array(
                'pk_privilege' => '115',
                'name'         => 'SEARCH_ADMIN',
                'description'  => _('Use search'),
                'module'       => 'SEARCH',
            ),
            116 => array(
                'pk_privilege' => '116',
                'name'         => 'TRASH_ADMIN',
                'description'  => _('List trashed elementes'),
                'module'       => 'SEARCH',
            ),
            117 => array(
                'pk_privilege' => '117',
                'name'         => 'WIDGET_ADMIN',
                'description'  => _('List'),
                'module'       => 'WIDGET',
            ),
            118 => array(
                'pk_privilege' => '118',
                'name'         => 'WIDGET_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'WIDGET',
            ),
            119 => array(
                'pk_privilege' => '119',
                'name'         => 'WIDGET_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'WIDGET',
            ),
            120 => array(
                'pk_privilege' => '120',
                'name'         => 'WIDGET_DELETE',
                'description'  => _('Delete'),
                'module'       => 'WIDGET',
            ),
            121 => array(
                'pk_privilege' => '121',
                'name'         => 'WIDGET_CREATE',
                'description'  => _('Create'),
                'module'       => 'WIDGET',
            ),
            122 => array(
                'pk_privilege' => '122',
                'name'         => 'MENU_ADMIN',
                'description'  => _('List'),
                'module'       => 'MENU',
            ),
            123 => array(
                'pk_privilege' => '123',
                'name'         => 'MENU_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'MENU',
            ),
            124 => array(
                'pk_privilege' => '124',
                'name'         => 'MENU_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'MENU',
            ),
            125 => array(
                'pk_privilege' => '125',
                'name'         => 'IMPORT_ADMIN',
                'description'  => _('Import news from agency'),
                'module'       => 'IMPORT',
            ),
            127 => array(
                'pk_privilege' => '127',
                'name'         => 'IMPORT_XML',
                'description'  => _('Import XML files'),
                'module'       => 'IMPORT',
            ),
            130 => array(
                'pk_privilege' => '130',
                'name'         => 'ONM_CONFIG',
                'description'  => _('Manage global settings'),
                'module'       => 'ONM',
            ),
            131 => array(
                'pk_privilege' => '131',
                'name'         => 'ONM_MANAGER',
                'description'  => _('Access instance manager'),
                'module'       => 'ONM',
            ),
            132 => array(
                'pk_privilege' => '132',
                'name'         => 'CONTENT_OTHER_UPDATE',
                'description'  => _('Modify other users\'s content'),
                'module'       => 'CONTENT',
            ),
            133 => array(
                'pk_privilege' => '133',
                'name'         => 'CONTENT_OTHER_DELETE',
                'description'  => _('Delete other users\'s content'),
                'module'       => 'CONTENT',
            ),
            134 => array(
                'pk_privilege' => '134',
                'name'         => 'ONM_SETTINGS',
                'description'  => _('Configure system-wide settings'),
                'module'       => 'ONM',
            ),
            135 => array(
                'pk_privilege' => '135',
                'name'         => 'GROUP_CHANGE',
                'description'  => _('Change the user group from one user'),
                'module'       => 'GROUP',
            ),
            137 => array(
                'pk_privilege' => '137',
                'name'         => 'BOOK_ADMIN',
                'description'  => _('List'),
                'module'       => 'BOOK',
            ),
            138 => array(
                'pk_privilege' => '138',
                'name'         => 'BOOK_CREATE',
                'description'  => _('Create'),
                'module'       => 'BOOK',
            ),
            139 => array(
                'pk_privilege' => '139',
                'name'         => 'BOOK_HOME',
                'description'  => _('Manage widget'),
                'module'       => 'BOOK',
            ),
            140 => array(
                'pk_privilege' => '140',
                'name'         => 'BOOK_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'BOOK',
            ),
            141 => array(
                'pk_privilege' => '141',
                'name'         => 'BOOK_SETTINGS',
                'description'  => _('Administrate settings'),
                'module'       => 'BOOK',
            ),
            142 => array(
                'pk_privilege' => '142',
                'name'         => 'BOOK_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'BOOK',
            ),
            143 => array(
                'pk_privilege' => '143',
                'name'         => 'BOOK_DELETE',
                'description'  => _('Delete'),
                'module'       => 'BOOK',
            ),
            144 => array(
                'pk_privilege' => '144',
                'name'         => 'BOOK_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'BOOK',
            ),
            145 => array(
                'pk_privilege' => '145',
                'name'         => 'SPECIAL_ADMIN',
                'description'  => _('List'),
                'module'       => 'SPECIAL',
            ),
            146 => array(
                'pk_privilege' => '146',
                'name'         => 'SPECIAL_CREATE',
                'description'  => _('Create'),
                'module'       => 'SPECIAL',
            ),
            147 => array(
                'pk_privilege' => '147',
                'name'         => 'SPECIAL_FAVORITE',
                'description'  => _('Manage widget'),
                'module'       => 'SPECIAL',
            ),
            148 => array(
                'pk_privilege' => '148',
                'name'         => 'SPECIAL_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'SPECIAL',
            ),
            149 => array(
                'pk_privilege' => '149',
                'name'         => 'SPECIAL_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'SPECIAL',
            ),
            150 => array(
                'pk_privilege' => '150',
                'name'         => 'SPECIAL_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'SPECIAL',
            ),
            151 => array(
                'pk_privilege' => '151',
                'name'         => 'SPECIAL_DELETE',
                'description'  => _('Delete'),
                'module'       => 'SPECIAL',
            ),
            152 => array(
                'pk_privilege' => '152',
                'name'         => 'SPECIAL_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'SPECIAL',
            ),
            153 => array(
                'pk_privilege' => '153',
                'name'         => 'SCHEDULE_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'SCHEDULE',
            ),
            154 => array(
                'pk_privilege' => '154',
                'name'         => 'SCHEDULE_ADMIN',
                'description'  => _('Manage agenda'),
                'module'       => 'SCHEDULE',
            ),
            155 => array(
                'pk_privilege' => '155',
                'name'         => 'VIDEO_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'VIDEO',
            ),
            156 => array(
                'pk_privilege' => '156',
                'name'         => 'VIDEO_FAVORITE',
                'description'  => _('Manage favorite flag'),
                'module'       => 'VIDEO',
            ),
            157 => array(
                'pk_privilege' => '157',
                'name'         => 'ALBUM_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'ALBUM',
            ),
            158 => array(
                'pk_privilege' => '158',
                'name'         => 'ALBUM_FAVORITE',
                'description'  => _('Manage favorite flag'),
                'module'       => 'ALBUM',
            ),
            159 => array(
                'pk_privilege' => '159',
                'name'         => 'ALBUM_SETTINGS',
                'description'  => _('Manage module setting'),
                'module'       => 'ALBUM',
            ),
            160 => array(
                'pk_privilege' => '160',
                'name'         => 'POLL_SETTINGS',
                'description'  => _('Manage module setting'),
                'module'       => 'POLL',
            ),
            161 => array(
                'pk_privilege' => '161',
                'name'         => 'OPINION_SETTINGS',
                'description'  => _('Manage module setting'),
                'module'       => 'OPINION',
            ),
            162 => array(
                'pk_privilege' => '162',
                'name'         => 'CATEGORY_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'CATEGORY',
            ),
            163 => array(
                'pk_privilege' => '163',
                'name'         => 'VIDEO_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'VIDEO',
            ),
            179 => array(
                'pk_privilege' => '179',
                'name'         => 'MENU_CREATE',
                'description'  => _('Create'),
                'module'       => 'MENU',
            ),
            164 => array(
                'pk_privilege' => '164',
                'name'         => 'MENU_DELETE',
                'description'  => _('Delete'),
                'module'       => 'MENU',
            ),
            165 => array(
                'pk_privilege' => '165',
                'name'         => 'IMPORT_EFE_FILE',
                'description'  => _('Import EFE articles file'),
                'module'       => 'IMPORT',
            ),
            166 => array(
                'pk_privilege' => '166',
                'name'         => 'LETTER_TRASH',
                'description'  => _('Send to trash and restore'),
                'module'       => 'LETTER',
            ),
            167 => array(
                'pk_privilege' => '167',
                'name'         => 'LETTER_DELETE',
                'description'  => _('Delete'),
                'module'       => 'LETTER',
            ),
            168 => array(
                'pk_privilege' => '168',
                'name'         => 'LETTER_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'LETTER',
            ),
            169 => array(
                'pk_privilege' => '169',
                'name'         => 'LETTER_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'LETTER',
            ),
            170 => array(
                'pk_privilege' => '170',
                'name'         => 'LETTER_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'LETTER',
            ),
            171 => array(
                'pk_privilege' => '171',
                'name'         => 'LETTER_FAVORITE',
                'description'  => _('Manage widget'),
                'module'       => 'LETTER',
            ),
            172 => array(
                'pk_privilege' => '172',
                'name'         => 'LETTER_CREATE',
                'description'  => _('Create'),
                'module'       => 'LETTER',
            ),
            173 => array(
                'pk_privilege' => '173',
                'name'         => 'LETTER_ADMIN',
                'description'  => _('List'),
                'module'       => 'LETTER',
            ),
            174 => array(
                'pk_privilege' => '174',
                'name'         => 'POLL_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'POLL',
            ),
            175 => array(
                'pk_privilege' => '175',
                'name'         => 'POLL_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'POLL',
            ),
            177 => array(
                'pk_privilege' => '177',
                'name'         => 'IMPORT_NEWS_AGENCY_CONFIG',
                'description'  => _('Config News Agency importer'),
                'module'       => 'IMPORT',
            ),
            178 => array(
                'pk_privilege' => '178',
                'name'         => 'SYNC_ADMIN',
                'description'  => _('Administer synchronization between Opennemas'),
                'module'       => 'SYNC_MANAGER',
            ),
            180 => array(
                'pk_privilege' => '180',
                'name'         => 'INSTANCE_SYNC_ADMIN',
                'description'  => _('Administer instances'),
                'module'       => 'INSTANCE_SYNC',
            ),
            181 => array(
                'pk_privilege' => '181',
                'name'         => 'SPECIAL_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'SPECIAL',
            ),
            182 => array(
                'pk_privilege' => '182',
                'name'         => 'PAYWALL_ADMIN',
                'description'  => _('Administer paywall'),
                'module'       => 'PAYWALL',
            ),
            183 => array(
                'pk_privilege' => '183',
                'name'         => 'ATTACHMENT_AVAILABLE',
                'description'  => _('Publish/unpublish'),
                'module'       => 'FILE',
            ),
            184 => array(
                'pk_privilege' => '184',
                'name'         => 'ATTACHMENT_HOME',
                'description'  => _('Manage frontpage'),
                'module'       => 'FILE',
            ),
            185 => array(
                'pk_privilege' => '185',
                'name'         => 'ATTACHMENT_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'FILE',
            ),
            186 => array(
                'pk_privilege' => '186',
                'name'         => 'KIOSKO_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'KIOSKO',
            ),
            187 => array(
                'pk_privilege' => '187',
                'name'         => 'OPINION_FAVORITE',
                'description'  => _('Manage favourite flag'),
                'module'       => 'OPINION',
            ),
            188 => array(
                'pk_privilege' => '188',
                'name'         => 'USER_EDIT_OWN_PROFILE',
                'description'  => _('Edit user own profile'),
                'module'       => 'USER',
            ),
            189 => array(
                'pk_privilege' => '189',
                'name'         => 'ADVERTISEMENT_SETTINGS',
                'description'  => _('Manage module settings'),
                'module'       => 'ADVERTISEMENT',
            ),
            // All existing privileges
            190 => [
                'pk_privilege' => '190',
                'name'         => 'MASTER',
                'description'  => _('Authenticate as a MASTER'),
                'module'       => 'SECURITY',
            ],
            // All existing privileges for owned instances and some edition
            // privileges in manager
            191 => [
                'pk_privilege' => '191',
                'name'         => 'PARTNER',
                'description'  => _('Authenticate as a PARTNER'),
                'module'       => 'SECURITY',
            ],
            // All privileges for active extensions in instance
            192 => [
                'pk_privilege' => '192',
                'name'         => 'ADMIN',
                'description'  => _('Authenticate as an ADMINISTRATOR'),
                'module'       => 'SECURITY',
            ],
            193 => [
                'pk_privilege' => '193',
                'name'         => 'INSTANCE_LIST',
                'description'  => _('List'),
                'module'       => 'INSTANCE',
            ],
            194 => [
                'pk_privilege' => '194',
                'name'         => 'INSTANCE_CREATE',
                'description'  => _('Create'),
                'module'       => 'INSTANCE',
            ],
            195 => [
                'pk_privilege' => '195',
                'name'         => 'INSTANCE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'INSTANCE',
            ],
            196 => [
                'pk_privilege' => '196',
                'name'         => 'INSTANCE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'INSTANCE',
            ],
            197 => [
                'pk_privilege' => '197',
                'name'         => 'INSTANCE_REPORT',
                'description'  => _('Report'),
                'module'       => 'INSTANCE',
            ],
            198 => [
                'pk_privilege' => '198',
                'name'         => 'EXTENSION_LIST',
                'description'  => _('List'),
                'module'       => 'EXTENSION',
            ],
            199 => [
                'pk_privilege' => '199',
                'name'         => 'EXTENSION_CREATE',
                'description'  => _('Create'),
                'module'       => 'EXTENSION',
            ],
            200 => [
                'pk_privilege' => '200',
                'name'         => 'EXTENSION_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'EXTENSION',
            ],
            201 => [
                'pk_privilege' => '201',
                'name'         => 'EXTENSION_DELETE',
                'description'  => _('Delete'),
                'module'       => 'EXTENSION',
            ],
            202 => [
                'pk_privilege' => '202',
                'name'         => 'EXTENSION_REPORT',
                'description'  => _('Report'),
                'module'       => 'EXTENSION',
            ],
            203 => [
                'pk_privilege' => '203',
                'name'         => 'NOTIFICATION_LIST',
                'description'  => _('List'),
                'module'       => 'NOTIFICATION',
            ],
            204 => [
                'pk_privilege' => '204',
                'name'         => 'NOTIFICATION_CREATE',
                'description'  => _('Create'),
                'module'       => 'NOTIFICATION',
            ],
            205 => [
                'pk_privilege' => '205',
                'name'         => 'NOTIFICATION_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'NOTIFICATION',
            ],
            206 => [
                'pk_privilege' => '206',
                'name'         => 'NOTIFICATION_DELETE',
                'description'  => _('Delete'),
                'module'       => 'NOTIFICATION',
            ],
            207 => [
                'pk_privilege' => '207',
                'name'         => 'NOTIFICATION_REPORT',
                'description'  => _('Report'),
                'module'       => 'NOTIFICATION',
            ],
            208 => [
                'pk_privilege' => '208',
                'name'         => 'CLIENT_LIST',
                'description'  => _('List'),
                'module'       => 'CLIENT',
            ],
            209 => [
                'pk_privilege' => '209',
                'name'         => 'CLIENT_CREATE',
                'description'  => _('Create'),
                'module'       => 'CLIENT',
            ],
            210 => [
                'pk_privilege' => '210',
                'name'         => 'CLIENT_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'CLIENT',
            ],
            211 => [
                'pk_privilege' => '211',
                'name'         => 'CLIENT_DELETE',
                'description'  => _('Delete'),
                'module'       => 'CLIENT',
            ],
            212 => [
                'pk_privilege' => '212',
                'name'         => 'CLIENT_REPORT',
                'description'  => _('Report'),
                'module'       => 'CLIENT',
            ],
            213 => [
                'pk_privilege' => '213',
                'name'         => 'PURCHASE_LIST',
                'description'  => _('List'),
                'module'       => 'PURCHASE',
            ],
            214 => [
                'pk_privilege' => '214',
                'name'         => 'PURCHASE_CREATE',
                'description'  => _('Create'),
                'module'       => 'PURCHASE',
            ],
            215 => [
                'pk_privilege' => '215',
                'name'         => 'PURCHASE_UPDATE',
                'description'  => _('Edit'),
                'module'       => 'PURCHASE',
            ],
            216 => [
                'pk_privilege' => '216',
                'name'         => 'PURCHASE_DELETE',
                'description'  => _('Delete'),
                'module'       => 'PURCHASE',
            ],
            217 => [
                'pk_privilege' => '217',
                'name'         => 'PURCHASE_REPORT',
                'description'  => _('Report'),
                'module'       => 'PURCHASE',
            ],
            218 => [
                'pk_privilege' => '218',
                'name'         => 'REPORT_LIST',
                'description'  => _('List'),
                'module'       => 'REPORT',
            ],
            219 => [
                'pk_privilege' => '219',
                'name'         => 'REPORT_DOWNLOAD',
                'description'  => _('Download'),
                'module'       => 'REPORT',
            ],
            220 => [
                'pk_privilege' => '220',
                'name'         => 'COMMAND_LIST',
                'description'  => _('List'),
                'module'       => 'COMMAND',
            ],
            221 => [
                'pk_privilege' => '221',
                'name'         => 'COMMAND_EXECUTE',
                'description'  => _('Execute'),
                'module'       => 'COMMAND',
            ],
            222 => [
                'pk_privilege' => '222',
                'name'         => 'OPCACHE',
                'description'  => _('List'),
                'module'       => 'OPCACHE',
            ],
        );

        return self::$privileges;
    }
}
