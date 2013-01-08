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
        self::loadPrivileges();
        $privileges = self::$privileges;

        $modules = array();
        foreach ($privileges as $privilege) {
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
        self::loadPrivileges();
        $privileges = self::$privileges;

        $groupedPrivileges = array();
        foreach ($privileges as $privilegeData) {
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
     * @deprecated 0.5
    */
    public static function getPrivilegesForUserGroup($userGroupId)
    {
        self::loadPrivileges();

        $sql = 'SELECT pk_fk_privilege FROM users, user_groups_privileges
                WHERE pk_fk_user_group = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($userGroupId)));

        $privileges = array();
        while (!$rs->EOF) {
            if (array_key_exists($rs->fields['pk_fk_privilege'], self::$privileges)) {
                $privilege = self::$privileges[$rs->fields['pk_fk_privilege']];
                $privileges[] = $privilege['name'];
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
            array(
                'pk_privilege' => '1',
                'name'         => 'CATEGORY_ADMIN',
                'description'  => _('List categories'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '2',
                'name'         => 'CATEGORY_AVAILABLE',
                'description'  => _('Activate/deactivate categories'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '3',
                'name'         => 'CATEGORY_UPDATE',
                'description'  => _('Edit categories'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '4',
                'name'         => 'CATEGORY_DELETE',
                'description'  => _('Remove categories'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '5',
                'name'         => 'CATEGORY_CREATE',
                'description'  => _('Create categories'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '6',
                'name'         => 'ARTICLE_ADMIN',
                'description'  => _('List articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '7',
                'name'         => 'ARTICLE_FRONTPAGE',
                'description'  => _('Frontpages administration'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '8',
                'name'         => 'ARTICLE_PENDINGS',
                'description'  => _('List pending articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '9',
                'name'         => 'ARTICLE_AVAILABLE',
                'description'  => _('Publish/unpublish articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '10',
                'name'         => 'ARTICLE_UPDATE',
                'description'  => _('Edit articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '11',
                'name'         => 'ARTICLE_DELETE',
                'description'  => _('Delete articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '12',
                'name'         => 'ARTICLE_CREATE',
                'description'  => _('Create articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '13',
                'name'         => 'ARTICLE_ARCHIVE',
                'description'  => _('Arquive/unarquive articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '14',
                'name'         => 'ARTICLE_CLONE',
                'description'  => _('Clone articles'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '15',
                'name'         => 'ARTICLE_HOME',
                'description'  => _('Home frontpage administration'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '16',
                'name'         => 'ARTICLE_TRASH',
                'description'  => _('Send/restore articles to trash'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '17',
                'name'         => 'ARTICLE_ARCHIVE_ADMI',
                'description'  => _('List articles in arquive'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '18',
                'name'         => 'ADVERTISEMENT_ADMIN',
                'description'  => _('List advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '19',
                'name'         => 'ADVERTISEMENT_AVAILA',
                'description'  => _('Publish/unpublish advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '20',
                'name'         => 'ADVERTISEMENT_UPDATE',
                'description'  => _('Edit advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '21',
                'name'         => 'ADVERTISEMENT_DELETE',
                'description'  => _('Delete advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '22',
                'name'         => 'ADVERTISEMENT_CREATE',
                'description'  => _('Create advertisements'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '23',
                'name'         => 'ADVERTISEMENT_TRASH',
                'description'  => _('Send/restore advertisements to trash'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '24',
                'name'         => 'ADVERTISEMENT_HOME',
                'description'  => _('Adminstrate advertisments for homepage'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '26',
                'name'         => 'OPINION_ADMIN',
                'description'  => _('List opinion articles'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '27',
                'name'         => 'OPINION_FRONTPAGE',
                'description'  => _('Manage opinions frontpage'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '28',
                'name'         => 'OPINION_AVAILABLE',
                'description'  => _('Publish/unpublish opinion articles'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '29',
                'name'         => 'OPINION_UPDATE',
                'description'  => _('Edit opinion articles'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '30',
                'name'         => 'OPINION_HOME',
                'description'  => _('Administrate opinion widget'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '31',
                'name'         => 'OPINION_DELETE',
                'description'  => _('Delete opinion articles'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '32',
                'name'         => 'OPINION_CREATE',
                'description'  => _('Create opinion articles'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '33',
                'name'         => 'OPINION_TRASH',
                'description'  => _('Send/restore opinion articles to trash'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '34',
                'name'         => 'COMMENT_ADMIN',
                'description'  => _('List comments'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '35',
                'name'         => 'COMMENT_POLL',
                'description'  => _('Administrer poll comments'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '36',
                'name'         => 'COMMENT_HOME',
                'description'  => _('Administer home comments'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '37',
                'name'         => 'COMMENT_AVAILABLE',
                'description'  => _('Approve/reject comments'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '38',
                'name'         => 'COMMENT_UPDATE',
                'description'  => _('Edit comments'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '39',
                'name'         => 'COMMENT_DELETE',
                'description'  => _('Delete comment'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '40',
                'name'         => 'COMMENT_CREATE',
                'description'  => _('Create comment'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '41',
                'name'         => 'COMMENT_TRASH',
                'description'  => _('Sent/Restore comments from trash'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '42',
                'name'         => 'ALBUM_ADMIN',
                'description'  => _('List albums'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '43',
                'name'         => 'ALBUM_AVAILABLE',
                'description'  => _('Publish/unpublish albums'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '44',
                'name'         => 'ALBUM_UPDATE',
                'description'  => _('Edit album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '45',
                'name'         => 'ALBUM_DELETE',
                'description'  => _('Delete album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '46',
                'name'         => 'ALBUM_CREATE',
                'description'  => _('Create album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '47',
                'name'         => 'ALBUM_TRASH',
                'description'  => _('Send/restore albums from trash'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '48',
                'name'         => 'VIDEO_ADMIN',
                'description'  => _('List albums'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '49',
                'name'         => 'VIDEO_AVAILABLE',
                'description'  => _('Publish/unpublish videos'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '50',
                'name'         => 'VIDEO_UPDATE',
                'description'  => _('Edit videos'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '51',
                'name'         => 'VIDEO_DELETE',
                'description'  => _('Delete videos'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '52',
                'name'         => 'VIDEO_CREATE',
                'description'  => _('Create videos'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '53',
                'name'         => 'VIDEO_TRASH',
                'description'  => _('Send/Restore videos from trash'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '60',
                'name'         => 'IMAGE_ADMIN',
                'description'  => _('List images'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '61',
                'name'         => 'IMAGE_AVAILABLE',
                'description'  => _('Publish/unpublish images'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '62',
                'name'         => 'IMAGE_UPDATE',
                'description'  => _('Edit images'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '63',
                'name'         => 'IMAGE_DELETE',
                'description'  => _('Delete images'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '64',
                'name'         => 'IMAGE_CREATE',
                'description'  => _('Create/upoad images'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '65',
                'name'         => 'IMAGE_TRASH',
                'description'  => _('Send/restore images from trash'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '66',
                'name'         => 'STATIC_ADMIN',
                'description'  => _('List static pages'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '67',
                'name'         => 'STATIC_AVAILABLE',
                'description'  => _('Publish/unpublish static pages'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '68',
                'name'         => 'STATIC_UPDATE',
                'description'  => _('Edit static pages'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '69',
                'name'         => 'STATIC_DELETE',
                'description'  => _('Delete static pages'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '70',
                'name'         => 'STATIC_CREATE',
                'description'  => _('Create static pages'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '71',
                'name'         => 'KIOSKO_ADMIN',
                'description'  => _('List epapers'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '72',
                'name'         => 'KIOSKO_AVAILABLE',
                'description'  => _('Publish/unpublish epapers'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '73',
                'name'         => 'KIOSKO_UPDATE',
                'description'  => _('Edit epapers'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '74',
                'name'         => 'KIOSKO_DELETE',
                'description'  => _('Delete epapers'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '75',
                'name'         => 'KIOSKO_CREATE',
                'description'  => _('Create epapers'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '76',
                'name'         => 'KIOSKO_HOME',
                'description'  => _('Administer home epapers'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '77',
                'name'         => 'POLL_ADMIN',
                'description'  => _('List polls'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '78',
                'name'         => 'POLL_AVAILABLE',
                'description'  => _('Publish/unpublish polls'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '79',
                'name'         => 'POLL_UPDATE',
                'description'  => _('Edit polls'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '80',
                'name'         => 'POLL_DELETE',
                'description'  => _('Delete polls'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '81',
                'name'         => 'POLL_CREATE',
                'description'  => _('Create polls'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '82',
                'name'         => 'AUTHOR_ADMIN',
                'description'  => _('List opinion authors'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '83',
                'name'         => 'AUTHOR_UPDATE',
                'description'  => _('Edit authors'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '84',
                'name'         => 'AUTHOR_DELETE',
                'description'  => _('Delete authors'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '85',
                'name'         => 'AUTHOR_CREATE',
                'description'  => _('Create author'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '86',
                'name'         => 'USER_ADMIN',
                'description'  => _('List users'),
                'module'       => 'USER',
            ),
            array(
                'pk_privilege' => '87',
                'name'         => 'USER_UPDATE',
                'description'  => _('Edit users'),
                'module'       => 'USER',
            ),
            array(
                'pk_privilege' => '88',
                'name'         => 'USER_DELETE',
                'description'  => _('Delete users'),
                'module'       => 'USER',
            ),
            array(
                'pk_privilege' => '89',
                'name'         => 'USER_CREATE',
                'description'  => _('Create users'),
                'module'       => 'USER',
            ),
            array(
                'pk_privilege' => '90',
                'name'         => 'PCLAVE_ADMIN',
                'description'  => _('Listado de palabras clave'),
                'module'       => 'PCLAVE',
            ),
            array(
                'pk_privilege' => '91',
                'name'         => 'PCLAVE_UPDATE',
                'description'  => _('Modificar Palabra Clave'),
                'module'       => 'PCLAVE',
            ),
            array(
                'pk_privilege' => '92',
                'name'         => 'PCLAVE_DELETE',
                'description'  => _('Eliminar Palabra Clave'),
                'module'       => 'PCLAVE',
            ),
            array(
                'pk_privilege' => '93',
                'name'         => 'PCLAVE_CREATE',
                'description'  => _('Crear Palabra Clave'),
                'module'       => 'PCLAVE',
            ),
            array(
                'pk_privilege' => '95',
                'name'         => 'GROUP_ADMIN',
                'description'  => _('List user groups'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '96',
                'name'         => 'GROUP_UPDATE',
                'description'  => _('Edit user groups'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '97',
                'name'         => 'GROUP_DELETE',
                'description'  => _('Delete user groups'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '98',
                'name'         => 'GROUP_ADMIN',
                'description'  => _('List user groups'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '99',
                'name'         => 'GROUP_CREATE',
                'description'  => _('Create user groups'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '104',
                'name'         => 'FILE_ADMIN',
                'description'  => _('List files'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '105',
                'name'         => 'FILE_FRONTS',
                'description'  => _('GestiÃ³n de portadas'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '106',
                'name'         => 'FILE_UPDATE',
                'description'  => _('Edit files'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '107',
                'name'         => 'FILE_DELETE',
                'description'  => _('Delete files'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '108',
                'name'         => 'FILE_CREATE',
                'description'  => _('Create files'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '112',
                'name'         => 'NEWSLETTER_ADMIN',
                'description'  => _('Newsletter administration'),
                'module'       => 'NEWSLETTER',
            ),
            array(
                'pk_privilege' => '113',
                'name'         => 'BACKEND_ADMIN',
                'description'  => _('Manage backend settings'),
                'module'       => 'BACKEND',
            ),
            array(
                'pk_privilege' => '114',
                'name'         => 'CACHE_TPL_ADMIN',
                'description'  => _('Manage caches'),
                'module'       => 'CACHE',
            ),
            array(
                'pk_privilege' => '115',
                'name'         => 'SEARCH_ADMIN',
                'description'  => _('Use search'),
                'module'       => 'SEARCH',
            ),
            array(
                'pk_privilege' => '116',
                'name'         => 'TRASH_ADMIN',
                'description'  => _('Gestión papelera'),
                'module'       => 'List trashed elements',
            ),
            array(
                'pk_privilege' => '117',
                'name'         => 'WIDGET_ADMIN',
                'description'  => _('List widgets'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '118',
                'name'         => 'WIDGET_AVAILABLE',
                'description'  => _('Publish/unpublish widgets'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '119',
                'name'         => 'WIDGET_UPDATE',
                'description'  => _('Edit widgets'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '120',
                'name'         => 'WIDGET_DELETE',
                'description'  => _('Delete widgets'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '121',
                'name'         => 'WIDGET_CREATE',
                'description'  => _('Create widgets'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '122',
                'name'         => 'MENU_ADMIN',
                'description'  => _('List menus'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '123',
                'name'         => 'MENU_AVAILABLE',
                'description'  => _('Publish/unpublish menu'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '124',
                'name'         => 'MENU_UPDATE',
                'description'  => _('Edit menus'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '125',
                'name'         => 'IMPORT_ADMIN',
                'description'  => _('Import agency'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '126',
                'name'         => 'IMPORT_EPRESS',
                'description'  => _('Import EuropaPress articles'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '127',
                'name'         => 'IMPORT_XML',
                'description'  => _('Import XML files'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '128',
                'name'         => 'IMPORT_EFE',
                'description'  => _('Import EFE articles'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '130',
                'name'         => 'ONM_CONFIG',
                'description'  => _('Manage opennemas settings'),
                'module'       => 'ONM',
            ),
            array(
                'pk_privilege' => '131',
                'name'         => 'ONM_MANAGER',
                'description'  => _('Access opennemas manager'),
                'module'       => 'ONM',
            ),
            array(
                'pk_privilege' => '132',
                'name'         => 'CONTENT_OTHER_UPDATE',
                'description'  => _('Modify other users\'s content'),
                'module'       => 'CONTENT',
            ),
            array(
                'pk_privilege' => '133',
                'name'         => 'CONTENT_OTHER_DELETE',
                'description'  => _('Delete other users\'s content'),
                'module'       => 'CONTENT',
            ),
            array(
                'pk_privilege' => '134',
                'name'         => 'ONM_SETTINGS',
                'description'  => _('Configure system-wide settings'),
                'module'       => 'ONM',
            ),
            array(
                'pk_privilege' => '135',
                'name'         => 'GROUP_CHANGE',
                'description'  => _('Change the user group from one user'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '137',
                'name'         => 'BOOK_ADMIN',
                'description'  => _('List books'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '138',
                'name'         => 'BOOK_CREATE',
                'description'  => _('Create books'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '139',
                'name'         => 'BOOK_FAVORITE',
                'description'  => _('Manage books widget elements'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '140',
                'name'         => 'BOOK_AVAILABLE',
                'description'  => _('Publish/unpublish books'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '141',
                'name'         => 'BOOK_SETTINGS',
                'description'  => _('Manage book module settings'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '142',
                'name'         => 'BOOK_UPDATE',
                'description'  => _('Edit books'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '143',
                'name'         => 'BOOK_DELETE',
                'description'  => _('Delete books'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '144',
                'name'         => 'BOOK_TRASH',
                'description'  => _('Send/restore books from trash'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '145',
                'name'         => 'SPECIAL_ADMIN',
                'description'  => _('List specials'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '146',
                'name'         => 'SPECIAL_CREATE',
                'description'  => _('Create specials'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '147',
                'name'         => 'SPECIAL_FAVORITE',
                'description'  => _('Manage specials widget elements'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '148',
                'name'         => 'SPECIAL_AVAILABLE',
                'description'  => _('Publish/unpublish specials'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '149',
                'name'         => 'SPECIAL_SETTINGS',
                'description'  => _('Manage specials module settings'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '150',
                'name'         => 'SPECIAL_UPDATE',
                'description'  => _('Edit specials'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '151',
                'name'         => 'SPECIAL_DELETE',
                'description'  => _('Delete specials'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '152',
                'name'         => 'SPECIAL_TRASH',
                'description'  => _('Send/restore specials from trash'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '153',
                'name'         => 'SCHEDULE_SETTINGS',
                'description'  => _('Manage agenda settings'),
                'module'       => 'SCHEDULE',
            ),
            array(
                'pk_privilege' => '154',
                'name'         => 'SCHEDULE_ADMIN',
                'description'  => _('Manage agenda'),
                'module'       => 'SCHEDULE',
            ),
            array(
                'pk_privilege' => '155',
                'name'         => 'VIDEO_HOME',
                'description'  => _('Manage videos frontpage'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '156',
                'name'         => 'VIDEO_FAVORITE',
                'description'  => _('Manage favorite flag for videos'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '157',
                'name'         => 'ALBUM_HOME',
                'description'  => _('Manage albums frontpage'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '158',
                'name'         => 'ALBUM_FAVORITE',
                'description'  => _('Manage favorite flags for albums'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '159',
                'name'         => 'ALBUM_SETTINGS',
                'description'  => _('Manage albums module setting'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '160',
                'name'         => 'POLL_SETTINGS',
                'description'  => _('Manage polls module setting'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '161',
                'name'         => 'OPINION_SETTINGS',
                'description'  => _('Manage opinions module setting'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '162',
                'name'         => 'CATEGORY_SETTINGS',
                'description'  => _('Manage categories module settings'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '163',
                'name'         => 'VIDEO_SETTINGS',
                'description'  => _('Manage videos module settings'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '164',
                'name'         => 'MENU_DELETE',
                'description'  => _('Delete menu'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '165',
                'name'         => 'IMPORT_EFE_FILE',
                'description'  => _('Import EFE articles file'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '166',
                'name'         => 'LETTER_TRASH',
                'description'  => _('Send/restore letters from trash'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '167',
                'name'         => 'LETTER_DELETE',
                'description'  => _('Delete letters'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '168',
                'name'         => 'LETTER_UPDATE',
                'description'  => _('Edit letters'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '169',
                'name'         => 'LETTER_SETTINGS',
                'description'  => _('Manage letters module settings'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '170',
                'name'         => 'LETTER_AVAILABLE',
                'description'  => _('Publish/unpublish letters'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '171',
                'name'         => 'LETTER_FAVORITE',
                'description'  => _('Manage letter\'s widget'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '172',
                'name'         => 'LETTER_CREATE',
                'description'  => _('Create letters'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '173',
                'name'         => 'LETTER_ADMIN',
                'description'  => _('List letters'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '174',
                'name'         => 'POLL_FAVORITE',
                'description'  => _('Añadir a widgets'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '175',
                'name'         => 'POLL_HOME',
                'description'  => _('Añadir al widget de portada'),
                'module'       => 'POLL',
            ),
        );
    }
}
