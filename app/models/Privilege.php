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
 * @author     Fran Dieguez <fran@openhost.es>
 **/
class Privilege
{
    public $id           = null;
    public $pk_privilege = null;
    public $description = null;
    public $name        = null;
    public $module      = null;
    public static $privileges = null;

    /**
     * Constructor
     *
     * @see Privilege::Privilege
     * @param int $id Privilege Id
    */
    public function __construct($id = null)
    {
        if (!is_null($id)) {
            $this->read($id);
        }
    }

    /**
     * Create a new Privilege
     *
     * @param  array   $data Data values to insert into database
     * @return boolean
     */
    public function create($data)
    {
        $sql = 'INSERT INTO `privileges` (`name`, `module`, `description`) VALUES (?, ?, ?)';
        $values = array($data['name'], $data['module'], $data['description']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();
    }

    /**
     * Read a privilege
     *
     * @param int $id Privilege Id
     * @return
     */
    public function read($id)
    {
        $sql = 'SELECT * FROM `privileges` WHERE `pk_privilege`=?';

        // Set fetch method to ADODB_FETCH_ASSOC
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $rs  = $GLOBALS['application']->conn->Execute($sql, array($id));
        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }

        $this->load($rs->fields);

        return $this;
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
     * Update privilege
     *
     * @param  array             $data
     * @return boolean|Privilege Return this instance or false if update operation fail
     */
    public function update($data)
    {
        $sql = "UPDATE `privileges` "
             . "SET `name`=?, `module`=?, `description`=? "
             . "WHERE `pk_privilege`=?";

        $values = array(
            $data['name'],
            $data['module'],
            $data['description'],
            $data['id']
        );

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $this->load($values);

        return $this;
    }

    /**
     * Remove a privilege
     *
     * @param  int     $id Privilege Id
     * @return boolean
     */
    public function delete($id)
    {
        $sql = 'DELETE FROM `privileges` WHERE `pk_privilege`=?';
        $values = array($id);

        $rs = $GLOBALS['application']->conn->Execute($sql, $values);
        if ($rs === false) {
            \Application::logDatabaseError();

            return false;
        }

        return true;
    }

    /**
     * Get privileges of system
     *
     * @param array Array of Privileges
     */
    public function get_privileges($filter = null)
    {
        $privileges = array();
        if (is_null($filter)) {
            $sql = 'SELECT * FROM privileges ORDER BY module';
        } else {
            $sql = 'SELECT * FROM privileges WHERE '.$filter
                 . ' ORDER BY module, pk_privilege';
        }

        // Set fetch method to ADODB_FETCH_ASSOC
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $rs = $GLOBALS['application']->conn->Execute($sql);

        while (!$rs->EOF) {
            $privilege = new Privilege();
            $privilege->load($rs->fields);

            $privileges[]  = $privilege;
            $rs->MoveNext();
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
        $sql = 'SELECT `module` FROM `privileges` '
             . 'WHERE (`module` IS NOT NULL) AND (`module`<> "") '
             . 'GROUP BY `module`';

        // Set fetch method to ADODB_FETCH_ASSOC
        $GLOBALS['application']->conn->SetFetchMode(ADODB_FETCH_ASSOC);

        $rs = $GLOBALS['application']->conn->Execute($sql);

        while (!$rs->EOF) {
            $modules[] = $rs->fields['module'];
            $rs->MoveNext();
        }

        return $modules;
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
        $privileges = array();
        if (is_null($filter)) {
            $sql = 'SELECT * FROM privileges ORDER BY module';
        } else {
            $sql = 'SELECT * FROM privileges WHERE '.$filter
                 . ' GROUP BY module';
        }

        $rs = $GLOBALS['application']->conn->Execute($sql);

        while (!$rs->EOF) {
            $privilege = new Privilege();
            $privilege->load($rs->fields);

            $module = $rs->fields['module'];
            $privileges[$module][] = $privilege;
            $rs->MoveNext();
        }

        return $privileges;
    }

    /**
     * @deprecated 0.5
    */
    public static function get_privileges_by_user($idUser)
    {
        self::loadPrivileges();
        $privileges = array();
        $sql = 'SELECT user_groups_privileges.pk_fk_privilege FROM users
                    INNER JOIN user_groups_privileges
                    ON user_groups_privileges.pk_fk_user_group = users.fk_user_group
                WHERE users.pk_user = ?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($idUser)));

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
                'description'  => _('Listado de secciones'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '2',
                'name'         => 'CATEGORY_AVAILABLE',
                'description'  => _('Aprobar SecciÃ³n'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '3',
                'name'         => 'CATEGORY_UPDATE',
                'description'  => _('Modificar SecciÃ³n'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '4',
                'name'         => 'CATEGORY_DELETE',
                'description'  => _('Eliminar SecciÃ³n'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '5',
                'name'         => 'CATEGORY_CREATE',
                'description'  => _('Crear SecciÃ³n'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '6',
                'name'         => 'ARTICLE_ADMIN',
                'description'  => _('Listados de ArtÃ­culos'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '7',
                'name'         => 'ARTICLE_FRONTPAGE',
                'description'  => _('AdministraciÃ³n de portadas'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '8',
                'name'         => 'ARTICLE_PENDINGS',
                'description'  => _('Listar noticias pendientes'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '9',
                'name'         => 'ARTICLE_AVAILABLE',
                'description'  => _('Aprobar Noticia'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '10',
                'name'         => 'ARTICLE_UPDATE',
                'description'  => _('Modificar ArtÃ­culo'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '11',
                'name'         => 'ARTICLE_DELETE',
                'description'  => _('Eliminar ArtÃ­culo'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '12',
                'name'         => 'ARTICLE_CREATE',
                'description'  => _('Crear ArtÃ­culo'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '13',
                'name'         => 'ARTICLE_ARCHIVE',
                'description'  => _('Recuperar/Archivar ArtÃ­culos de/a hemeroteca'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '14',
                'name'         => 'ARTICLE_CLONE',
                'description'  => _('Clonar ArtÃ­culo'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '15',
                'name'         => 'ARTICLE_HOME',
                'description'  => _('GestiÃ³n portada Home de artÃ­culos'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '16',
                'name'         => 'ARTICLE_TRASH',
                'description'  => _('gestiÃ³n papelera ArtÃ­culo'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '17',
                'name'         => 'ARTICLE_ARCHIVE_ADMI',
                'description'  => _('Listado de hemeroteca'),
                'module'       => 'ARTICLE',
            ),
            array(
                'pk_privilege' => '18',
                'name'         => 'ADVERTISEMENT_ADMIN',
                'description'  => _('Listado de  publicidad'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '19',
                'name'         => 'ADVERTISEMENT_AVAILA',
                'description'  => _('Aprobar publicidad'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '20',
                'name'         => 'ADVERTISEMENT_UPDATE',
                'description'  => _('Modificar publicidad'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '21',
                'name'         => 'ADVERTISEMENT_DELETE',
                'description'  => _('Eliminar publicidad'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '22',
                'name'         => 'ADVERTISEMENT_CREATE',
                'description'  => _('Crear publicidad'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '23',
                'name'         => 'ADVERTISEMENT_TRASH',
                'description'  => _('gestiÃ³n papelera publicidad'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '24',
                'name'         => 'ADVERTISEMENT_HOME',
                'description'  => _('gestiÃ³n de publicidad en Home'),
                'module'       => 'ADVERTISEMENT',
            ),
            array(
                'pk_privilege' => '26',
                'name'         => 'OPINION_ADMIN',
                'description'  => _('Listado de  opiniÃ³n'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '27',
                'name'         => 'OPINION_FRONTPAGE',
                'description'  => _('Portada Opinion'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '28',
                'name'         => 'OPINION_AVAILABLE',
                'description'  => _('Aprobar OpiniÃ³n'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '29',
                'name'         => 'OPINION_UPDATE',
                'description'  => _('Modificar OpiniÃ³n'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '30',
                'name'         => 'OPINION_HOME',
                'description'  => _('Publicar widgets home OpiniÃ³n'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '31',
                'name'         => 'OPINION_DELETE',
                'description'  => _('Eliminar OpiniÃ³n'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '32',
                'name'         => 'OPINION_CREATE',
                'description'  => _('Crear OpiniÃ³n'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '33',
                'name'         => 'OPINION_TRASH',
                'description'  => _('gestion papelera OpiniÃ³n'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '34',
                'name'         => 'COMMENT_ADMIN',
                'description'  => _('Listado de comentarios'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '35',
                'name'         => 'COMMENT_POLL',
                'description'  => _('Gestionar Comentarios de encuestas'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '36',
                'name'         => 'COMMENT_HOME',
                'description'  => _('Gestionar Comentarios de Home'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '37',
                'name'         => 'COMMENT_AVAILABLE',
                'description'  => _('Aprobar/Rechazar Comentario'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '38',
                'name'         => 'COMMENT_UPDATE',
                'description'  => _('Modificar Comentario'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '39',
                'name'         => 'COMMENT_DELETE',
                'description'  => _('Eliminar Comentario'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '40',
                'name'         => 'COMMENT_CREATE',
                'description'  => _('Crear Comentario'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '41',
                'name'         => 'COMMENT_TRASH',
                'description'  => _('gestiÃ³n papelera Comentarios'),
                'module'       => 'COMMENT',
            ),
            array(
                'pk_privilege' => '42',
                'name'         => 'ALBUM_ADMIN',
                'description'  => _('Listado de Ã¡lbumes'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '43',
                'name'         => 'ALBUM_AVAILABLE',
                'description'  => _('Aprobar Album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '44',
                'name'         => 'ALBUM_UPDATE',
                'description'  => _('Modificar Album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '45',
                'name'         => 'ALBUM_DELETE',
                'description'  => _('Eliminar Album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '46',
                'name'         => 'ALBUM_CREATE',
                'description'  => _('Crear Album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '47',
                'name'         => 'ALBUM_TRASH',
                'description'  => _('gestion papelera Album'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '48',
                'name'         => 'VIDEO_ADMIN',
                'description'  => _('Listado de videos'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '49',
                'name'         => 'VIDEO_AVAILABLE',
                'description'  => _('Aprobar video'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '50',
                'name'         => 'VIDEO_UPDATE',
                'description'  => _('Modificar video'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '51',
                'name'         => 'VIDEO_DELETE',
                'description'  => _('Eliminar video'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '52',
                'name'         => 'VIDEO_CREATE',
                'description'  => _('Crear video'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '53',
                'name'         => 'VIDEO_TRASH',
                'description'  => _('gestiÃ³n papelera video'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '60',
                'name'         => 'IMAGE_ADMIN',
                'description'  => _('Listado de imÃ¡genes'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '61',
                'name'         => 'IMAGE_AVAILABLE',
                'description'  => _('Aprobar Imagen'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '62',
                'name'         => 'IMAGE_UPDATE',
                'description'  => _('Modificar Imagen'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '63',
                'name'         => 'IMAGE_DELETE',
                'description'  => _('Eliminar Imagen'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '64',
                'name'         => 'IMAGE_CREATE',
                'description'  => _('Subir Imagen'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '65',
                'name'         => 'IMAGE_TRASH',
                'description'  => _('gestiÃ³n papelera Imagen'),
                'module'       => 'IMAGE',
            ),
            array(
                'pk_privilege' => '66',
                'name'         => 'STATIC_ADMIN',
                'description'  => _('Listado pÃ¡ginas estÃ¡ticas'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '67',
                'name'         => 'STATIC_AVAILABLE',
                'description'  => _('Aprobar PÃ¡gina EstÃ¡tica'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '68',
                'name'         => 'STATIC_UPDATE',
                'description'  => _('Modificar PÃ¡gina EstÃ¡tica'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '69',
                'name'         => 'STATIC_DELETE',
                'description'  => _('Eliminar PÃ¡gina EstÃ¡tica'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '70',
                'name'         => 'STATIC_CREATE',
                'description'  => _('Crear PÃ¡gina EstÃ¡tica'),
                'module'       => 'STATIC',
            ),
            array(
                'pk_privilege' => '71',
                'name'         => 'KIOSKO_ADMIN',
                'description'  => _('Listar PÃ¡gina Papel'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '72',
                'name'         => 'KIOSKO_AVAILABLE',
                'description'  => _('Aprobar PÃ¡gina Papel'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '73',
                'name'         => 'KIOSKO_UPDATE',
                'description'  => _('Modificar PÃ¡gina Papel'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '74',
                'name'         => 'KIOSKO_DELETE',
                'description'  => _('Eliminar PÃ¡gina Papel'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '75',
                'name'         => 'KIOSKO_CREATE',
                'description'  => _('Crear PÃ¡gina Papel'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '76',
                'name'         => 'KIOSKO_HOME',
                'description'  => _('Incluir en portada como favorito'),
                'module'       => 'KIOSKO',
            ),
            array(
                'pk_privilege' => '77',
                'name'         => 'POLL_ADMIN',
                'description'  => _('Listado encuestas'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '78',
                'name'         => 'POLL_AVAILABLE',
                'description'  => _('Aprobar Encuesta'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '79',
                'name'         => 'POLL_UPDATE',
                'description'  => _('Modificar Encuesta'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '80',
                'name'         => 'POLL_DELETE',
                'description'  => _('Eliminar Encuesta'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '81',
                'name'         => 'POLL_CREATE',
                'description'  => _('Crear Encuesta'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '82',
                'name'         => 'AUTHOR_ADMIN',
                'description'  => _('Listado autores OpiniÃ³n'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '83',
                'name'         => 'AUTHOR_UPDATE',
                'description'  => _('Modificar Autor'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '84',
                'name'         => 'AUTHOR_DELETE',
                'description'  => _('Eliminar Autor'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '85',
                'name'         => 'AUTHOR_CREATE',
                'description'  => _('Crear Autor'),
                'module'       => 'AUTHOR',
            ),
            array(
                'pk_privilege' => '86',
                'name'         => 'USER_ADMIN',
                'description'  => _('Listado de usuarios'),
                'module'       => 'USER',
            ),
            array(
                'pk_privilege' => '87',
                'name'         => 'USER_UPDATE',
                'description'  => _('Modificar Usuario'),
                'module'       => 'USER',
            ),
            array(
                'pk_privilege' => '88',
                'name'         => 'USER_DELETE',
                'description'  => _('Eliminar Usuario'),
                'module'       => 'USER',
            ),
            array(
                'pk_privilege' => '89',
                'name'         => 'USER_CREATE',
                'description'  => _('Crear Usuario'),
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
                'description'  => _('Grupo usuarios Admin'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '96',
                'name'         => 'GROUP_UPDATE',
                'description'  => _('Modificar Grupo Usuarios'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '97',
                'name'         => 'GROUP_DELETE',
                'description'  => _('Eliminar Grupo Usuarios'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '98',
                'name'         => 'GROUP_ADMIN',
                'description'  => _('Listado de Grupo Usuarios'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '99',
                'name'         => 'GROUP_CREATE',
                'description'  => _('Crear Grupo Usuarios'),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '100',
                'name'         => 'PRIVILEGE_UPDATE',
                'description'  => _('Modificar Privilegio'),
                'module'       => 'PRIVILEGE',
            ),
            array(
                'pk_privilege' => '101',
                'name'         => 'PRIVILEGE_DELETE',
                'description'  => _('Eliminar Privilegio'),
                'module'       => 'PRIVILEGE',
            ),
            array(
                'pk_privilege' => '102',
                'name'         => 'PRIVILEGE_ADMIN',
                'description'  => _('Listado de Privilegios'),
                'module'       => 'PRIVILEGE',
            ),
            array(
                'pk_privilege' => '103',
                'name'         => 'PRIVILEGE_CREATE',
                'description'  => _('Crear Privilegio'),
                'module'       => 'PRIVILEGE',
            ),
            array(
                'pk_privilege' => '104',
                'name'         => 'FILE_ADMIN',
                'description'  => _('Listado de ficheros y portadas'),
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
                'description'  => _('Modificar Fichero'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '107',
                'name'         => 'FILE_DELETE',
                'description'  => _('Eliminar Fichero'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '108',
                'name'         => 'FILE_CREATE',
                'description'  => _('Crear Fichero'),
                'module'       => 'FILE',
            ),
            array(
                'pk_privilege' => '110',
                'name'         => 'BADLINK_ADMIN',
                'description'  => _('Control Link Admin'),
                'module'       => 'BADLINK',
            ),
            array(
                'pk_privilege' => '111',
                'name'         => 'STATS_ADMIN',
                'description'  => _('Admin EstadÃ­sticas'),
                'module'       => 'STATS',
            ),
            array(
                'pk_privilege' => '112',
                'name'         => 'NEWSLETTER_ADMIN',
                'description'  => _('AdministraciÃ³n del boletÃ­n'),
                'module'       => 'NEWSLETTER',
            ),
            array(
                'pk_privilege' => '113',
                'name'         => 'BACKEND_ADMIN',
                'description'  => _('ConfiguraciÃ³n de backend'),
                'module'       => 'BACKEND',
            ),
            array(
                'pk_privilege' => '114',
                'name'         => 'CACHE_TPL_ADMIN',
                'description'  => _('GestiÃ³n de CachÃ©s Portadas'),
                'module'       => 'CACHE',
            ),
            array(
                'pk_privilege' => '115',
                'name'         => 'SEARCH_ADMIN',
                'description'  => _('Utilidades: bÃºsqueda avanzada'),
                'module'       => 'SEARCH',
            ),
            array(
                'pk_privilege' => '116',
                'name'         => 'TRASH_ADMIN',
                'description'  => _('GestiÃ³n papelera'),
                'module'       => 'TRASH',
            ),
            array(
                'pk_privilege' => '117',
                'name'         => 'WIDGET_ADMIN',
                'description'  => _('Listado de widgets'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '118',
                'name'         => 'WIDGET_AVAILABLE',
                'description'  => _('Aprobar Widget'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '119',
                'name'         => 'WIDGET_UPDATE',
                'description'  => _('Modificar Widget'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '120',
                'name'         => 'WIDGET_DELETE',
                'description'  => _('Eliminar Widget'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '121',
                'name'         => 'WIDGET_CREATE',
                'description'  => _('Crear Widget'),
                'module'       => 'WIDGET',
            ),
            array(
                'pk_privilege' => '122',
                'name'         => 'MENU_ADMIN',
                'description'  => _('Listado de menus'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '123',
                'name'         => 'MENU_AVAILABLE',
                'description'  => _('Leer menu'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '124',
                'name'         => 'MENU_UPDATE',
                'description'  => _('Modificar menu'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '125',
                'name'         => 'IMPORT_ADMIN',
                'description'  => _('Agencia importador'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '126',
                'name'         => 'IMPORT_EPRESS',
                'description'  => _('Importar EuropaPress'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '127',
                'name'         => 'IMPORT_XML',
                'description'  => _('Importar Ficheros XML'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '128',
                'name'         => 'IMPORT_EFE',
                'description'  => _('Importar de EFE'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '129',
                'name'         => 'CACHE_APC_ADMIN',
                'description'  => _('Gestion cache de APC'),
                'module'       => 'CACHE',
            ),
            array(
                'pk_privilege' => '130',
                'name'         => 'ONM_CONFIG',
                'description'  => _('Configurar Onm'),
                'module'       => 'ONM',
            ),
            array(
                'pk_privilege' => '131',
                'name'         => 'ONM_MANAGER',
                'description'  => _('Gestionar Onm'),
                'module'       => 'ONM',
            ),
            array(
                'pk_privilege' => '132',
                'name'         => 'CONTENT_OTHER_UPDATE',
                'description'  => _('Poder modificar contenido de otros usuarios'),
                'module'       => 'CONTENT',
            ),
            array(
                'pk_privilege' => '133',
                'name'         => 'CONTENT_OTHER_DELETE',
                'description'  => _('Poder eliminar contenido de otros usuarios'),
                'module'       => 'CONTENT',
            ),
            array(
                'pk_privilege' => '134',
                'name'         => 'ONM_SETTINGS',
                'description'  => _('Allow to configure system wide settings'),
                'module'       => 'ONM',
            ),
            array(
                'pk_privilege' => '135',
                'name'         => 'GROUP_CHANGE',
                'description'  => _(' Cambiar de grupo al usuario '),
                'module'       => 'GROUP',
            ),
            array(
                'pk_privilege' => '137',
                'name'         => 'BOOK_ADMIN',
                'description'  => _('Administrar modulo de libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '138',
                'name'         => 'BOOK_CREATE',
                'description'  => _('Subir libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '139',
                'name'         => 'BOOK_FAVORITE',
                'description'  => _('Gestionar Widget de libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '140',
                'name'         => 'BOOK_AVAILABLE',
                'description'  => _('Aprobar libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '141',
                'name'         => 'BOOK_SETTINGS',
                'description'  => _('Configurar modulo de libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '142',
                'name'         => 'BOOK_UPDATE',
                'description'  => _('Modificar libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '143',
                'name'         => 'BOOK_DELETE',
                'description'  => _('Eliminar libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '144',
                'name'         => 'BOOK_TRASH',
                'description'  => _('Vaciar papelera de libros'),
                'module'       => 'BOOK',
            ),
            array(
                'pk_privilege' => '145',
                'name'         => 'SPECIAL_ADMIN',
                'description'  => _('Administrar modulo de especiales '),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '146',
                'name'         => 'SPECIAL_CREATE',
                'description'  => _('Crear especiales'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '147',
                'name'         => 'SPECIAL_FAVORITE',
                'description'  => _('Gestionar widget especiales'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '148',
                'name'         => 'SPECIAL_AVAILABLE',
                'description'  => _('Aprobar especiales'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '149',
                'name'         => 'SPECIAL_SETTINGS',
                'description'  => _('Configurar modulo de especiales'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '150',
                'name'         => 'SPECIAL_UPDATE',
                'description'  => _('Modificar especiales'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '151',
                'name'         => 'SPECIAL_DELETE',
                'description'  => _('Eliminar especiales'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '152',
                'name'         => 'SPECIAL_TRASH',
                'description'  => _('Gestionar papelera especiales'),
                'module'       => 'SPECIAL',
            ),
            array(
                'pk_privilege' => '153',
                'name'         => 'SCHEDULE_SETTINGS',
                'description'  => _('Gestionar la agenda '),
                'module'       => 'SCHEDULE',
            ),
            array(
                'pk_privilege' => '154',
                'name'         => 'SCHEDULE_ADMIN',
                'description'  => _('Gestionar la agenda '),
                'module'       => 'SCHEDULE',
            ),
            array(
                'pk_privilege' => '155',
                'name'         => 'VIDEO_HOME',
                'description'  => _('Publicar video en home'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '156',
                'name'         => 'VIDEO_FAVORITE',
                'description'  => _('Gestionar Videos favoritos'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '157',
                'name'         => 'ALBUM_HOME',
                'description'  => _('Publicar album para home'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '158',
                'name'         => 'ALBUM_FAVORITE',
                'description'  => _('Gestionar álbumes favoritos'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '159',
                'name'         => 'ALBUM_SETTINGS',
                'description'  => _('Configurar módulo de álbumes'),
                'module'       => 'ALBUM',
            ),
            array(
                'pk_privilege' => '160',
                'name'         => 'POLL_SETTINGS',
                'description'  => _('Configurar módulos de encuestas'),
                'module'       => 'POLL',
            ),
            array(
                'pk_privilege' => '161',
                'name'         => 'OPINION_SETTINGS',
                'description'  => _('Configurar módulo de opinion'),
                'module'       => 'OPINION',
            ),
            array(
                'pk_privilege' => '162',
                'name'         => 'CATEGORY_SETTINGS',
                'description'  => _('Configurar módulo de categorias'),
                'module'       => 'CATEGORY',
            ),
            array(
                'pk_privilege' => '163',
                'name'         => 'VIDEO_SETTINGS',
                'description'  => _('Configurar módulo de video'),
                'module'       => 'VIDEO',
            ),
            array(
                'pk_privilege' => '164',
                'name'         => 'MENU_DELETE',
                'description'  => _('Eliminar menu'),
                'module'       => 'MENU',
            ),
            array(
                'pk_privilege' => '165',
                'name'         => 'IMPORT_EFE_FILE',
                'description'  => _('Importar ficheros EFE'),
                'module'       => 'IMPORT',
            ),
            array(
                'pk_privilege' => '166',
                'name'         => 'LETTER_TRASH',
                'description'  => _('Vaciar papelera de cartas'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '167',
                'name'         => 'LETTER_DELETE',
                'description'  => _('Eliminar cartas'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '168',
                'name'         => 'LETTER_UPDATE',
                'description'  => _('Modificar cartas'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '169',
                'name'         => 'LETTER_SETTINGS',
                'description'  => _('Configurar modulo de cartas'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '170',
                'name'         => 'LETTER_AVAILABLE',
                'description'  => _('Aprobar cartas'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '171',
                'name'         => 'LETTER_FAVORITE',
                'description'  => _('Gestionar Widget de cartas'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '172',
                'name'         => 'LETTER_CREATE',
                'description'  => _('Subir cartas'),
                'module'       => 'LETTER',
            ),
            array(
                'pk_privilege' => '173',
                'name'         => 'LETTER_ADMIN',
                'description'  => _('Admon. cartas'),
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
