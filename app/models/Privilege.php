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
    public $id                = null;
    public $pk_privilege      = null;
    public $description       = null;
    public $name              = null;
    public $module            = null;
    public static $privileges = null;

    /**
     * Constructor
     *
     * @see Privilege::Privilege
     * @param int $id Privilege Id
    */
    public function __construct($id = null)
    {
        self::loadPrivileges();

        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Read a privilege
     *
     * @param int $id Privilege Id
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
    public function find($filter = null)
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
     * @param array Array of string
     */
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
     * Get privileges group by modules
     *
     * @param string $filter where condition for check.
     *
     * @return array modules with each privileges
     *
     */
    public function getPrivilegesByModules($filter = null)
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

        return $groupedPrivileges;
    }

    /**
     * Get privileges for a given user group id
     *
     * @param int $userGroupId the id of the user group
     *
     * @return array the list of privilege names
     *
     */
    public static function getPrivilegesForUserGroup($userGroupId)
    {
        $sql = 'SELECT pk_fk_privilege FROM users, user_groups_privileges
                WHERE pk_fk_user_group = ? ORDER BY pk_fk_privilege';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($userGroupId)));

        $privileges = array();
        while (!$rs->EOF) {
            if (array_key_exists($rs->fields['pk_fk_privilege'], self::$privileges)) {
                $privilege = self::$privileges[$rs->fields['pk_fk_privilege']];
                $privileges[$privilege['pk_privilege']] = $privilege['name'];
            }
            $rs->MoveNext();
        }



        return $privileges;
    }

    /**
     * Initializes the internal array of privileges
     *
     * @return void
     **/
    private static function loadPrivileges()
    {
        self::$privileges = $privileges = array(
            1 => array(
                'pk_privilege' => '1',
                'name'         => 'CATEGORY_ADMIN',
                'description'  => _('List categories'),
                'module'       => 'CATEGORY',
            ),
            2 => array(
                'pk_privilege' => '2',
                'name'         => 'CATEGORY_AVAILABLE',
                'description'  => _('Activate/deactivate categories'),
                'module'       => 'CATEGORY',
            ),
            3 => array(
                'pk_privilege' => '3',
                'name'         => 'CATEGORY_UPDATE',
                'description'  => _('Edit categories'),
                'module'       => 'CATEGORY',
            ),
            4 => array(
                'pk_privilege' => '4',
                'name'         => 'CATEGORY_DELETE',
                'description'  => _('Remove categories'),
                'module'       => 'CATEGORY',
            ),
            5 => array(
                'pk_privilege' => '5',
                'name'         => 'CATEGORY_CREATE',
                'description'  => _('Create categories'),
                'module'       => 'CATEGORY',
            ),
            6 => array(
                'pk_privilege' => '6',
                'name'         => 'ARTICLE_ADMIN',
                'description'  => _('List articles'),
                'module'       => 'ARTICLE',
            ),
            7 => array(
                'pk_privilege' => '7',
                'name'         => 'ARTICLE_FRONTPAGE',
                'description'  => _('Frontpages administration'),
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
                'description'  => _('Publish/unpublish articles'),
                'module'       => 'ARTICLE',
            ),
            10 => array(
                'pk_privilege' => '10',
                'name'         => 'ARTICLE_UPDATE',
                'description'  => _('Edit articles'),
                'module'       => 'ARTICLE',
            ),
            11 => array(
                'pk_privilege' => '11',
                'name'         => 'ARTICLE_DELETE',
                'description'  => _('Delete articles'),
                'module'       => 'ARTICLE',
            ),
            12 => array(
                'pk_privilege' => '12',
                'name'         => 'ARTICLE_CREATE',
                'description'  => _('Create articles'),
                'module'       => 'ARTICLE',
            ),
           13 =>  array(
                'pk_privilege' => '13',
                'name'         => 'ARTICLE_ARCHIVE',
                'description'  => _('Arquive/unarquive articles'),
                'module'       => 'ARTICLE',
            ),
            14 => array(
                'pk_privilege' => '14',
                'name'         => 'ARTICLE_CLONE',
                'description'  => _('Clone articles'),
                'module'       => 'ARTICLE',
            ),
            15 => array(
                'pk_privilege' => '15',
                'name'         => 'ARTICLE_HOME',
                'description'  => _('Home frontpage administration'),
                'module'       => 'ARTICLE',
            ),
            16 => array(
                'pk_privilege' => '16',
                'name'         => 'ARTICLE_TRASH',
                'description'  => _('Send/restore articles to trash'),
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
                'description'  => _('List advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            19 => array(
                'pk_privilege' => '19',
                'name'         => 'ADVERTISEMENT_AVAILA',
                'description'  => _('Publish/unpublish advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            20 => array(
                'pk_privilege' => '20',
                'name'         => 'ADVERTISEMENT_UPDATE',
                'description'  => _('Edit advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            21 => array(
                'pk_privilege' => '21',
                'name'         => 'ADVERTISEMENT_DELETE',
                'description'  => _('Delete advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            22 => array(
                'pk_privilege' => '22',
                'name'         => 'ADVERTISEMENT_CREATE',
                'description'  => _('Create advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            23 => array(
                'pk_privilege' => '23',
                'name'         => 'ADVERTISEMENT_TRASH',
                'description'  => _('Send/restore advertisements to trash'),
                'module'       => 'ADVERTISEMENT',
            ),
            24 => array(
                'pk_privilege' => '24',
                'name'         => 'ADVERTISEMENT_HOME',
                'description'  => _('Adminstrate advertisments for homepage'),
                'module'       => 'ADVERTISEMENT',
            ),
            26 => array(
                'pk_privilege' => '26',
                'name'         => 'OPINION_ADMIN',
                'description'  => _('List opinion articles'),
                'module'       => 'OPINION',
            ),
            27 => array(
                'pk_privilege' => '27',
                'name'         => 'OPINION_FRONTPAGE',
                'description'  => _('Manage opinions frontpage'),
                'module'       => 'OPINION',
            ),
            28 => array(
                'pk_privilege' => '28',
                'name'         => 'OPINION_AVAILABLE',
                'description'  => _('Publish/unpublish opinion articles'),
                'module'       => 'OPINION',
            ),
            29 => array(
                'pk_privilege' => '29',
                'name'         => 'OPINION_UPDATE',
                'description'  => _('Edit opinion articles'),
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
                'description'  => _('Delete opinion articles'),
                'module'       => 'OPINION',
            ),
            32 => array(
                'pk_privilege' => '32',
                'name'         => 'OPINION_CREATE',
                'description'  => _('Create opinion articles'),
                'module'       => 'OPINION',
            ),
            33 => array(
                'pk_privilege' => '33',
                'name'         => 'OPINION_TRASH',
                'description'  => _('Send/restore opinion articles to trash'),
                'module'       => 'OPINION',
            ),
            34 => array(
                'pk_privilege' => '34',
                'name'         => 'COMMENT_ADMIN',
                'description'  => _('List comments'),
                'module'       => 'COMMENT',
            ),
            35 => array(
                'pk_privilege' => '35',
                'name'         => 'COMMENT_POLL',
                'description'  => _('Administrer poll comments'),
                'module'       => 'COMMENT',
            ),
            36 => array(
                'pk_privilege' => '36',
                'name'         => 'COMMENT_HOME',
                'description'  => _('Administer home comments'),
                'module'       => 'COMMENT',
            ),
            37 => array(
                'pk_privilege' => '37',
                'name'         => 'COMMENT_AVAILABLE',
                'description'  => _('Approve/reject comments'),
                'module'       => 'COMMENT',
            ),
            38 => array(
                'pk_privilege' => '38',
                'name'         => 'COMMENT_UPDATE',
                'description'  => _('Edit comments'),
                'module'       => 'COMMENT',
            ),
            39 => array(
                'pk_privilege' => '39',
                'name'         => 'COMMENT_DELETE',
                'description'  => _('Delete comment'),
                'module'       => 'COMMENT',
            ),
            40 => array(
                'pk_privilege' => '40',
                'name'         => 'COMMENT_CREATE',
                'description'  => _('Create comment'),
                'module'       => 'COMMENT',
            ),
            41 => array(
                'pk_privilege' => '41',
                'name'         => 'COMMENT_TRASH',
                'description'  => _('Sent/Restore comments from trash'),
                'module'       => 'COMMENT',
            ),
            42 => array(
                'pk_privilege' => '42',
                'name'         => 'ALBUM_ADMIN',
                'description'  => _('List albums'),
                'module'       => 'ALBUM',
            ),
            43 => array(
                'pk_privilege' => '43',
                'name'         => 'ALBUM_AVAILABLE',
                'description'  => _('Publish/unpublish albums'),
                'module'       => 'ALBUM',
            ),
            44 => array(
                'pk_privilege' => '44',
                'name'         => 'ALBUM_UPDATE',
                'description'  => _('Edit album'),
                'module'       => 'ALBUM',
            ),
            45 => array(
                'pk_privilege' => '45',
                'name'         => 'ALBUM_DELETE',
                'description'  => _('Delete album'),
                'module'       => 'ALBUM',
            ),
            46 => array(
                'pk_privilege' => '46',
                'name'         => 'ALBUM_CREATE',
                'description'  => _('Create album'),
                'module'       => 'ALBUM',
            ),
            47 => array(
                'pk_privilege' => '47',
                'name'         => 'ALBUM_TRASH',
                'description'  => _('Send/restore albums from trash'),
                'module'       => 'ALBUM',
            ),
            48 => array(
                'pk_privilege' => '48',
                'name'         => 'VIDEO_ADMIN',
                'description'  => _('List albums'),
                'module'       => 'VIDEO',
            ),
            49 => array(
                'pk_privilege' => '49',
                'name'         => 'VIDEO_AVAILABLE',
                'description'  => _('Publish/unpublish videos'),
                'module'       => 'VIDEO',
            ),
            50 => array(
                'pk_privilege' => '50',
                'name'         => 'VIDEO_UPDATE',
                'description'  => _('Edit videos'),
                'module'       => 'VIDEO',
            ),
            51 => array(
                'pk_privilege' => '51',
                'name'         => 'VIDEO_DELETE',
                'description'  => _('Delete videos'),
                'module'       => 'VIDEO',
            ),
            52 => array(
                'pk_privilege' => '52',
                'name'         => 'VIDEO_CREATE',
                'description'  => _('Create videos'),
                'module'       => 'VIDEO',
            ),
            53 => array(
                'pk_privilege' => '53',
                'name'         => 'VIDEO_TRASH',
                'description'  => _('Send/Restore videos from trash'),
                'module'       => 'VIDEO',
            ),
            54 => array(
                'pk_privilege' => '60',
                'name'         => 'IMAGE_ADMIN',
                'description'  => _('List images'),
                'module'       => 'IMAGE',
            ),
            61 => array(
                'pk_privilege' => '61',
                'name'         => 'IMAGE_AVAILABLE',
                'description'  => _('Publish/unpublish images'),
                'module'       => 'IMAGE',
            ),
            62 => array(
                'pk_privilege' => '62',
                'name'         => 'IMAGE_UPDATE',
                'description'  => _('Edit images'),
                'module'       => 'IMAGE',
            ),
            63 => array(
                'pk_privilege' => '63',
                'name'         => 'IMAGE_DELETE',
                'description'  => _('Delete images'),
                'module'       => 'IMAGE',
            ),
            64 => array(
                'pk_privilege' => '64',
                'name'         => 'IMAGE_CREATE',
                'description'  => _('Create/upoad images'),
                'module'       => 'IMAGE',
            ),
            65 => array(
                'pk_privilege' => '65',
                'name'         => 'IMAGE_TRASH',
                'description'  => _('Send/restore images from trash'),
                'module'       => 'IMAGE',
            ),
            66 => array(
                'pk_privilege' => '66',
                'name'         => 'STATIC_ADMIN',
                'description'  => _('List static pages'),
                'module'       => 'STATIC',
            ),
            67 => array(
                'pk_privilege' => '67',
                'name'         => 'STATIC_AVAILABLE',
                'description'  => _('Publish/unpublish static pages'),
                'module'       => 'STATIC',
            ),
            68 => array(
                'pk_privilege' => '68',
                'name'         => 'STATIC_UPDATE',
                'description'  => _('Edit static pages'),
                'module'       => 'STATIC',
            ),
            69 => array(
                'pk_privilege' => '69',
                'name'         => 'STATIC_DELETE',
                'description'  => _('Delete static pages'),
                'module'       => 'STATIC',
            ),
            70 => array(
                'pk_privilege' => '70',
                'name'         => 'STATIC_CREATE',
                'description'  => _('Create static pages'),
                'module'       => 'STATIC',
            ),
            71 => array(
                'pk_privilege' => '71',
                'name'         => 'KIOSKO_ADMIN',
                'description'  => _('List epapers'),
                'module'       => 'KIOSKO',
            ),
            72 => array(
                'pk_privilege' => '72',
                'name'         => 'KIOSKO_AVAILABLE',
                'description'  => _('Publish/unpublish epapers'),
                'module'       => 'KIOSKO',
            ),
            73 => array(
                'pk_privilege' => '73',
                'name'         => 'KIOSKO_UPDATE',
                'description'  => _('Edit epapers'),
                'module'       => 'KIOSKO',
            ),
            74 => array(
                'pk_privilege' => '74',
                'name'         => 'KIOSKO_DELETE',
                'description'  => _('Delete epapers'),
                'module'       => 'KIOSKO',
            ),
            75 => array(
                'pk_privilege' => '75',
                'name'         => 'KIOSKO_CREATE',
                'description'  => _('Create epapers'),
                'module'       => 'KIOSKO',
            ),
            76 => array(
                'pk_privilege' => '76',
                'name'         => 'KIOSKO_HOME',
                'description'  => _('Administer home epapers'),
                'module'       => 'KIOSKO',
            ),
            77 => array(
                'pk_privilege' => '77',
                'name'         => 'POLL_ADMIN',
                'description'  => _('List polls'),
                'module'       => 'POLL',
            ),
            78 => array(
                'pk_privilege' => '78',
                'name'         => 'POLL_AVAILABLE',
                'description'  => _('Publish/unpublish polls'),
                'module'       => 'POLL',
            ),
            79 => array(
                'pk_privilege' => '79',
                'name'         => 'POLL_UPDATE',
                'description'  => _('Edit polls'),
                'module'       => 'POLL',
            ),
            80 => array(
                'pk_privilege' => '80',
                'name'         => 'POLL_DELETE',
                'description'  => _('Delete polls'),
                'module'       => 'POLL',
            ),
            81 => array(
                'pk_privilege' => '81',
                'name'         => 'POLL_CREATE',
                'description'  => _('Create polls'),
                'module'       => 'POLL',
            ),
            82 => array(
                'pk_privilege' => '82',
                'name'         => 'AUTHOR_ADMIN',
                'description'  => _('List opinion authors'),
                'module'       => 'AUTHOR',
            ),
            83 => array(
                'pk_privilege' => '83',
                'name'         => 'AUTHOR_UPDATE',
                'description'  => _('Edit authors'),
                'module'       => 'AUTHOR',
            ),
            84 => array(
                'pk_privilege' => '84',
                'name'         => 'AUTHOR_DELETE',
                'description'  => _('Delete authors'),
                'module'       => 'AUTHOR',
            ),
            85 => array(
                'pk_privilege' => '85',
                'name'         => 'AUTHOR_CREATE',
                'description'  => _('Create author'),
                'module'       => 'AUTHOR',
            ),
            86 => array(
                'pk_privilege' => '86',
                'name'         => 'USER_ADMIN',
                'description'  => _('List users'),
                'module'       => 'USER',
            ),
            87 => array(
                'pk_privilege' => '87',
                'name'         => 'USER_UPDATE',
                'description'  => _('Edit users'),
                'module'       => 'USER',
            ),
            88 => array(
                'pk_privilege' => '88',
                'name'         => 'USER_DELETE',
                'description'  => _('Delete users'),
                'module'       => 'USER',
            ),
            89 => array(
                'pk_privilege' => '89',
                'name'         => 'USER_CREATE',
                'description'  => _('Create users'),
                'module'       => 'USER',
            ),
            90 => array(
                'pk_privilege' => '90',
                'name'         => 'PCLAVE_ADMIN',
                'description'  => _('Listado de palabras clave'),
                'module'       => 'PCLAVE',
            ),
            91 => array(
                'pk_privilege' => '91',
                'name'         => 'PCLAVE_UPDATE',
                'description'  => _('Modificar Palabra Clave'),
                'module'       => 'PCLAVE',
            ),
            92 => array(
                'pk_privilege' => '92',
                'name'         => 'PCLAVE_DELETE',
                'description'  => _('Eliminar Palabra Clave'),
                'module'       => 'PCLAVE',
            ),
            93 => array(
                'pk_privilege' => '93',
                'name'         => 'PCLAVE_CREATE',
                'description'  => _('Crear Palabra Clave'),
                'module'       => 'PCLAVE',
            ),
            95 => array(
                'pk_privilege' => '95',
                'name'         => 'GROUP_ADMIN',
                'description'  => _('List user groups'),
                'module'       => 'GROUP',
            ),
            96 => array(
                'pk_privilege' => '96',
                'name'         => 'GROUP_UPDATE',
                'description'  => _('Edit user groups'),
                'module'       => 'GROUP',
            ),
            97 => array(
                'pk_privilege' => '97',
                'name'         => 'GROUP_DELETE',
                'description'  => _('Delete user groups'),
                'module'       => 'GROUP',
            ),
            98 => array(
                'pk_privilege' => '98',
                'name'         => 'GROUP_ADMIN',
                'description'  => _('List user groups'),
                'module'       => 'GROUP',
            ),
            99 => array(
                'pk_privilege' => '99',
                'name'         => 'GROUP_CREATE',
                'description'  => _('Create user groups'),
                'module'       => 'GROUP',
            ),
            104 => array(
                'pk_privilege' => '104',
                'name'         => 'FILE_ADMIN',
                'description'  => _('List files'),
                'module'       => 'FILE',
            ),
            99 => array(
                'pk_privilege' => '105',
                'name'         => 'FILE_FRONTS',
                'description'  => _('GestiÃ³n de portadas'),
                'module'       => 'FILE',
            ),
            106 => array(
                'pk_privilege' => '106',
                'name'         => 'FILE_UPDATE',
                'description'  => _('Edit files'),
                'module'       => 'FILE',
            ),
            107 => array(
                'pk_privilege' => '107',
                'name'         => 'FILE_DELETE',
                'description'  => _('Delete files'),
                'module'       => 'FILE',
            ),
            108 => array(
                'pk_privilege' => '108',
                'name'         => 'FILE_CREATE',
                'description'  => _('Create files'),
                'module'       => 'FILE',
            ),
            112 => array(
                'pk_privilege' => '112',
                'name'         => 'NEWSLETTER_ADMIN',
                'description'  => _('Newsletter administration'),
                'module'       => 'NEWSLETTER',
            ),
            113 => array(
                'pk_privilege' => '113',
                'name'         => 'BACKEND_ADMIN',
                'description'  => _('Manage backend settings'),
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
                'description'  => _('Gestión papelera'),
                'module'       => 'List trashed elements',
            ),
            117 => array(
                'pk_privilege' => '117',
                'name'         => 'WIDGET_ADMIN',
                'description'  => _('List widgets'),
                'module'       => 'WIDGET',
            ),
            118 => array(
                'pk_privilege' => '118',
                'name'         => 'WIDGET_AVAILABLE',
                'description'  => _('Publish/unpublish widgets'),
                'module'       => 'WIDGET',
            ),
            119 => array(
                'pk_privilege' => '119',
                'name'         => 'WIDGET_UPDATE',
                'description'  => _('Edit widgets'),
                'module'       => 'WIDGET',
            ),
            120 => array(
                'pk_privilege' => '120',
                'name'         => 'WIDGET_DELETE',
                'description'  => _('Delete widgets'),
                'module'       => 'WIDGET',
            ),
            121 => array(
                'pk_privilege' => '121',
                'name'         => 'WIDGET_CREATE',
                'description'  => _('Create widgets'),
                'module'       => 'WIDGET',
            ),
            122 => array(
                'pk_privilege' => '122',
                'name'         => 'MENU_ADMIN',
                'description'  => _('List menus'),
                'module'       => 'MENU',
            ),
            123 => array(
                'pk_privilege' => '123',
                'name'         => 'MENU_AVAILABLE',
                'description'  => _('Publish/unpublish menu'),
                'module'       => 'MENU',
            ),
            124 => array(
                'pk_privilege' => '124',
                'name'         => 'MENU_UPDATE',
                'description'  => _('Edit menus'),
                'module'       => 'MENU',
            ),
            125 => array(
                'pk_privilege' => '125',
                'name'         => 'IMPORT_ADMIN',
                'description'  => _('Import agency'),
                'module'       => 'IMPORT',
            ),
            126 => array(
                'pk_privilege' => '126',
                'name'         => 'IMPORT_EPRESS',
                'description'  => _('Import EuropaPress articles'),
                'module'       => 'IMPORT',
            ),
            127 => array(
                'pk_privilege' => '127',
                'name'         => 'IMPORT_XML',
                'description'  => _('Import XML files'),
                'module'       => 'IMPORT',
            ),
            128 => array(
                'pk_privilege' => '128',
                'name'         => 'IMPORT_EFE',
                'description'  => _('Import EFE articles'),
                'module'       => 'IMPORT',
            ),
            130 => array(
                'pk_privilege' => '130',
                'name'         => 'ONM_CONFIG',
                'description'  => _('Manage opennemas settings'),
                'module'       => 'ONM',
            ),
            131 => array(
                'pk_privilege' => '131',
                'name'         => 'ONM_MANAGER',
                'description'  => _('Access opennemas manager'),
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
                'description'  => _('List books'),
                'module'       => 'BOOK',
            ),
            138 => array(
                'pk_privilege' => '138',
                'name'         => 'BOOK_CREATE',
                'description'  => _('Create books'),
                'module'       => 'BOOK',
            ),
            139 => array(
                'pk_privilege' => '139',
                'name'         => 'BOOK_FAVORITE',
                'description'  => _('Manage books widget elements'),
                'module'       => 'BOOK',
            ),
            140 => array(
                'pk_privilege' => '140',
                'name'         => 'BOOK_AVAILABLE',
                'description'  => _('Publish/unpublish books'),
                'module'       => 'BOOK',
            ),
            141 => array(
                'pk_privilege' => '141',
                'name'         => 'BOOK_SETTINGS',
                'description'  => _('Manage book module settings'),
                'module'       => 'BOOK',
            ),
            142 => array(
                'pk_privilege' => '142',
                'name'         => 'BOOK_UPDATE',
                'description'  => _('Edit books'),
                'module'       => 'BOOK',
            ),
            143 => array(
                'pk_privilege' => '143',
                'name'         => 'BOOK_DELETE',
                'description'  => _('Delete books'),
                'module'       => 'BOOK',
            ),
            144 => array(
                'pk_privilege' => '144',
                'name'         => 'BOOK_TRASH',
                'description'  => _('Send/restore books from trash'),
                'module'       => 'BOOK',
            ),
            145 => array(
                'pk_privilege' => '145',
                'name'         => 'SPECIAL_ADMIN',
                'description'  => _('List specials'),
                'module'       => 'SPECIAL',
            ),
            146 => array(
                'pk_privilege' => '146',
                'name'         => 'SPECIAL_CREATE',
                'description'  => _('Create specials'),
                'module'       => 'SPECIAL',
            ),
            147 => array(
                'pk_privilege' => '147',
                'name'         => 'SPECIAL_FAVORITE',
                'description'  => _('Manage specials widget elements'),
                'module'       => 'SPECIAL',
            ),
            148 => array(
                'pk_privilege' => '148',
                'name'         => 'SPECIAL_AVAILABLE',
                'description'  => _('Publish/unpublish specials'),
                'module'       => 'SPECIAL',
            ),
            149 => array(
                'pk_privilege' => '149',
                'name'         => 'SPECIAL_SETTINGS',
                'description'  => _('Manage specials module settings'),
                'module'       => 'SPECIAL',
            ),
            150 => array(
                'pk_privilege' => '150',
                'name'         => 'SPECIAL_UPDATE',
                'description'  => _('Edit specials'),
                'module'       => 'SPECIAL',
            ),
            151 => array(
                'pk_privilege' => '151',
                'name'         => 'SPECIAL_DELETE',
                'description'  => _('Delete specials'),
                'module'       => 'SPECIAL',
            ),
            152 => array(
                'pk_privilege' => '152',
                'name'         => 'SPECIAL_TRASH',
                'description'  => _('Send/restore specials from trash'),
                'module'       => 'SPECIAL',
            ),
            153 => array(
                'pk_privilege' => '153',
                'name'         => 'SCHEDULE_SETTINGS',
                'description'  => _('Manage agenda settings'),
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
                'description'  => _('Manage videos frontpage'),
                'module'       => 'VIDEO',
            ),
            156 => array(
                'pk_privilege' => '156',
                'name'         => 'VIDEO_FAVORITE',
                'description'  => _('Manage favorite flag for videos'),
                'module'       => 'VIDEO',
            ),
            157 => array(
                'pk_privilege' => '157',
                'name'         => 'ALBUM_HOME',
                'description'  => _('Manage albums frontpage'),
                'module'       => 'ALBUM',
            ),
            158 => array(
                'pk_privilege' => '158',
                'name'         => 'ALBUM_FAVORITE',
                'description'  => _('Manage favorite flags for albums'),
                'module'       => 'ALBUM',
            ),
            159 => array(
                'pk_privilege' => '159',
                'name'         => 'ALBUM_SETTINGS',
                'description'  => _('Manage albums module setting'),
                'module'       => 'ALBUM',
            ),
            160 => array(
                'pk_privilege' => '160',
                'name'         => 'POLL_SETTINGS',
                'description'  => _('Manage polls module setting'),
                'module'       => 'POLL',
            ),
            161 => array(
                'pk_privilege' => '161',
                'name'         => 'OPINION_SETTINGS',
                'description'  => _('Manage opinions module setting'),
                'module'       => 'OPINION',
            ),
            162 => array(
                'pk_privilege' => '162',
                'name'         => 'CATEGORY_SETTINGS',
                'description'  => _('Manage categories module settings'),
                'module'       => 'CATEGORY',
            ),
            163 => array(
                'pk_privilege' => '163',
                'name'         => 'VIDEO_SETTINGS',
                'description'  => _('Manage videos module settings'),
                'module'       => 'VIDEO',
            ),
            164 => array(
                'pk_privilege' => '164',
                'name'         => 'MENU_DELETE',
                'description'  => _('Delete menu'),
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
                'description'  => _('Send/restore letters from trash'),
                'module'       => 'LETTER',
            ),
            167 => array(
                'pk_privilege' => '167',
                'name'         => 'LETTER_DELETE',
                'description'  => _('Delete letters'),
                'module'       => 'LETTER',
            ),
            168 => array(
                'pk_privilege' => '168',
                'name'         => 'LETTER_UPDATE',
                'description'  => _('Edit letters'),
                'module'       => 'LETTER',
            ),
            169 => array(
                'pk_privilege' => '169',
                'name'         => 'LETTER_SETTINGS',
                'description'  => _('Manage letters module settings'),
                'module'       => 'LETTER',
            ),
            170 => array(
                'pk_privilege' => '170',
                'name'         => 'LETTER_AVAILABLE',
                'description'  => _('Publish/unpublish letters'),
                'module'       => 'LETTER',
            ),
            171 => array(
                'pk_privilege' => '171',
                'name'         => 'LETTER_FAVORITE',
                'description'  => _('Manage letter\'s widget'),
                'module'       => 'LETTER',
            ),
            172 => array(
                'pk_privilege' => '172',
                'name'         => 'LETTER_CREATE',
                'description'  => _('Create letters'),
                'module'       => 'LETTER',
            ),
            173 => array(
                'pk_privilege' => '173',
                'name'         => 'LETTER_ADMIN',
                'description'  => _('List letters'),
                'module'       => 'LETTER',
            ),
            174 => array(
                'pk_privilege' => '174',
                'name'         => 'POLL_FAVORITE',
                'description'  => _('Añadir a widgets'),
                'module'       => 'POLL',
            ),
            175 => array(
                'pk_privilege' => '175',
                'name'         => 'POLL_HOME',
                'description'  => _('Añadir al widget de portada'),
                'module'       => 'POLL',
            ),
        );
    }
}
