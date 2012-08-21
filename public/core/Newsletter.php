<?php
/*
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
/**
 * Newsletter
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/

use Onm\Settings as s,
    Onm\Message  as m;

class Newsletter
{
    const ITEMS_MAX_LIMIT = 50;

    protected $namespace        = null;
    protected $_accountsProvider = null;
    protected $itemsProvider    = null;
    protected $templateProvider = null;
    protected $tablename        = 'newsletter_archive';

    public $errors = array(); // mail errors, bounces, ...

    public function __construct($config)
    {
        $this->setup($config['namespace']);
    }

    public function setup($namespace)
    {
        $accountsProvider = $namespace . 'Newsletter_Accounts_Provider';
        if (class_exists($accountsProvider)) {
            $this->_accountsProvider = new $accountsProvider();
        }

        $itemsProvider = $namespace . 'Newsletter_Items_Provider';
        if (class_exists($itemsProvider)) {
            $this->itemsProvider = new $itemsProvider();
        }
    }

    public function getAccountsProvider()
    {
        return $this->_accountsProvider;
    }

    public function getItemsProvider()
    {
        return $this->itemsProvider;
    }

    public function render()
    {
        $tpl = new Template(TEMPLATE_USER);
        $ccm = new ContentCategoryManager();

        $data = json_decode($_REQUEST['postmaster']);
        $i = 0;
        foreach ($data->articles as $tok) {
            $category = $ccm->get_name($tok->category);
            $date = date('Y-m-d', strtotime(str_replace('/', '-',
                substr($tok->created, 6))));
            $data->articles[$i]->date= $date;
            $data->articles[$i]->cat = $category;
            if (is_array($tok->params)
                && array_key_exists('agencyBulletin', $tok->params)
            ) {
                $data->articles[$i]->agency = $tok->params['agencyBulletin'];
            } else {
                $data->articles[$i]->agency = '';
            }
            $i++;
        }
        $i = 0;
        if (isset($data->opinions) && !empty($data->opinions)) {
            foreach ($data->opinions as $tok) {
                $date = date('Y-m-d',
                    strtotime(str_replace('/', '-', substr($tok->created, 6))));
                $data->opinions[$i]->date = $date;
                $slug = StringUtils::get_title($data->opinions[$i]->author);
                $data->opinions[$i]->slug= $slug;
                $i++;
            }
        }
        $tpl->assign('data', $data);

        //render menu
        $menuFrontpage= Menu::renderMenu('frontpage');
        $tpl->assign('menuFrontpage', $menuFrontpage->items);

        //render ads
        $advertisement = Advertisement::getInstance();
        $banners = $advertisement->getAdvertisements(array(1001, 1009), 0);
        $cm = new ContentManager();
        $banners = $cm->getInTime($banners);

        $advertisement->render($banners, $advertisement);

        /*Fetch inmenu categorys*/
        $categoriesInMenu = $ccm->find('internal_category != 0 '
            .'AND fk_content_category =0 AND inmenu=1', 'ORDER BY posmenu');
        $tpl->assign('inmenu_categorys', $categoriesInMenu);

        // VIERNES 4 DE SEPTIEMBRE 2009
        $days = array(
            'Domingo',
            'Lunes',
            'Martes',
            'Miércoles',
            'Jueves',
            'Viernes',
            'Sábado'
        );
        $months = array(
            '', 'Enero', 'Febrero', 'Marzo',
            'Abril', 'Mayo', 'Junio',
            'Julio', 'Agosto', 'Septiembre',
            'Octubre', 'Noviembre', 'Diciembre'
        );
        $fullDate = $days[(int) date('w')].' '.date('j')
            .' de '.$months[(int) date('n')].' '.date('Y');
        $tpl->assign('current_date', $fullDate);

        $publicUrl = preg_replace('@^http[s]?://(.*?)/$@i', 'http://$1',
            SITE_URL);
        $tpl->assign('URL_PUBLIC', $publicUrl);

        $configurations = s::get(array(
            'newsletter_maillist',
            'newsletter_subscriptionType',
        ));

        $tpl->assign('conf', $configurations);
        $htmlContent = $tpl->fetch('newsletter/newsletter.tpl');

        return $htmlContent;
    }

    /**
     * Send mail to all users
     *
     */
    public function send($mailboxes, $htmlcontent, $params)
    {
        foreach ($mailboxes as $mailbox) {
            $this->sendToUser($mailbox, $htmlcontent, $params);
        }
    }

    public function sendToUser($mailbox, $htmlcontent, $params)
    {
        require_once SITE_LIBS_PATH.'phpmailer/class.phpmailer.php';

        $mail = new PHPMailer();
        $mail->SetLanguage('es');
        $mail->IsSMTP();
        $mail->Host = $params['mail_host'];
        if (!empty($params['mail_user'])
            && !empty($params['mail_password'])
        ) {
            $mail->SMTPAuth = true;
        } else {
            $mail->SMTPAuth = false;
        }

        $mail->CharSet = 'utf-8';

        $mail->Username = $params['mail_user'];
        $mail->Password = $params['mail_pass'];

        // Inject values by $params array
        $mail->From     = $params['mail_from'];
        $mail->FromName = $params['mail_from_name'];
        $mail->IsHTML(true);
        $this->HTML = $htmlcontent;

        $mail->AddAddress($mailbox->email, $mailbox->name);

        // embed image logo
        $mail->AddEmbeddedImage(SITE_PATH
            .'themes/xornal/images/xornal-boletin.jpg', 'logo-cid', 'Logotipo');

        $subject = (!isset($params['subject']))? '[Xornal]': $params['subject'];
        $mail->Subject  = $subject;

        // TODO: crear un filtro
        $this->HTML = preg_replace('/(>[^<"]*)["]+([^<"]*<)/', "$1&#34;$2",
            $this->HTML);
        $this->HTML = preg_replace("/(>[^<']*)[']+([^<']*<)/", "$1&#39;$2",
            $this->HTML);
        $this->HTML = str_replace('“', '&#8220;', $this->HTML);
        $this->HTML = str_replace('”', '&#8221;', $this->HTML);
        $this->HTML = str_replace('‘', '&#8216;', $this->HTML);
        $this->HTML = str_replace('’', '&#8217;', $this->HTML);

        $mail->Body = $this->HTML;

        if (!$mail->Send()) {
            $this->errors[] = "Error en el envío del mensaje ".$mail->ErrorInfo;

            return false;
        }

        return true;
    }

    /**
     * Set config values to send mails
     *
     * To establish life time to infinite and ignore button stop of the browser
     * <code>
     * set_time_limit(0);
     * ignore_user_abort(true);
     * </code>
    */
    public function setConfigMailing()
    {
        set_time_limit(0);
        ignore_user_abort(true);
    }

    /**
     *
     */
    public function setupDatabaseTable()
    {
        require_once SITE_LIBS_PATH.'adodb5/adodb-xmlschema.inc.php';
        $schema = new adoSchema($GLOBALS['application']->conn);

        // Schema for bulletins support.
        $axmls = '<?xml version="1.0"?>
                <schema version="0.2">
                  <table name="' . $this->tablename. '">
                    <desc>Table to archive newsletter.</desc>
                    <field name="pk_newsletter" type="I">
                      <descr>Identificator.</descr>
                      <KEY/>
                      <AUTOINCREMENT/>
                    </field>
                    <field name="data" type="XL"></field>
                    <field name="created" type="T">
                        <DEFTIMESTAMP />
                    </field>
                  </table>

                </schema>';

        $schema->ParseSchemaString($axmls)
               ->ExecuteSchema();
    }

    public function schema_exists()
    {
        $dict   = NewDataDictionary($GLOBALS['application']->conn);
        $tables = $dict->MetaTables();

        return in_array($this->tablename, $tables);
    }

    public function create($request)
    {
        $data = array();
        $data['data']    = $request['data'];
        $data['created'] = date("Y-m-d H:i:s");

        $sql = 'INSERT INTO `' . $this->tablename. '` (`data`, `created`) '
             . 'VALUES (?,?)';

        $values = array($data['data'], $data['created']);

        if ($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            \Application::logDatabaseError();

            return false;
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();
        $this->read($this->id);

        return $this;
    }

    public function read($id)
    {
        $sql = 'SELECT * FROM `' . $this->tablename.'` '
             . 'WHERE pk_newsletter=?';
        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));

        if (!$rs) {
            \Application::logDatabaseError();

            return;
        }
        $this->loadData($rs->fields);

        return $this;
    }

    public function loadData($fields)
    {
        $this->id             = $fields['pk_newsletter'];
        $this->pk_newsletter  = $fields['pk_newsletter'];
        $this->data           = $fields['data'];
        $this->created        = $fields['created'];
    }

    public function search($filter=null)
    {
        $newsletters = array();

        if (is_null($filter)) {
            $filter = '1=1';
        }

        $sql = 'SELECT * FROM `' . $this->tablename. '` WHERE '.$filter;
        $rs = $GLOBALS['application']->conn->Execute($sql);

        if (!$rs) {
            \Application::logDatabaseError();

            return $newsletters;
        }

        while (!$rs->EOF) {
            $obj = new Newsletter();
            $obj->loadData($rs->fields);
            $newsletters[] = $obj;

            $rs->MoveNext();
        }

        return $newsletters;
    }

    public function update()
    {
        // Nothing
    }

    public function delete($id)
    {
        $sql = 'DELETE FROM `' . $this->tablename. '` WHERE pk_newsletter=?';

        $rs = $GLOBALS['application']->conn->Execute($sql, array(intval($id)));
        if ($rs === false) {
            \Application::logDatabaseError();

            return;
        }
    }
}

/**
 * Account item for newsletter in PConecta
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
class Newsletter_Account
{
    public $email = null;
    public $name  = null;

    public function __construct($email, $name='')
    {
        $this->email = $email;
        $this->name  = $name;
    }

    public function __set($name, $value)
    {
        $this->{$name} = $value;
    }

    public function __get($name)
    {
        if (!property_exists($this, $name)) {
            return null;
        }

        return $this->{$name};
    }
}

/**
 * Newsletter item
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
class Newsletter_Item
{
    protected $values = null;

    public function __construct($values=array())
    {
        $this->values = $values;
    }

    public function __set($name, $value)
    {
        $this->values[$name] = $value;
    }

    public function __get($name)
    {
        if (!isset($this->values[$name])) {
            return null;
        }

        return $this->values[$name];
    }
}

/**
 * Abstract class for implement accounts provider
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
abstract class Newsletter_Accounts_Provider
{
    protected $conn     = null;
    protected $database = null;
    protected $table    = null;
    protected $fields   = null;
    protected $filter   = null;

    protected $accounts = null;

    abstract public function fetch();

    public function __construct(
        $conn,
        $table,
        $fields,
        $filter=null,
        $database=null
    )
    {
        // TODO: control errors
        $this->conn     = $conn;
        $this->database = $database;
        $this->table    = $table;
        $this->filter   = $filter;

        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }
        $this->fields = $fields;
    }

    public function getAccounts()
    {
        $this->fetch(); // populate $this->accounts

        return $this->accounts;
    }

    protected function buildQuery()
    {
        $fields = $this->fields;
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        if (!is_null($this->database)) {
            $table = $this->database . '.' . $this->table;
        } else {
            $table = $this->table;
        }

        $sql = 'SELECT ' . $fields . ' FROM ' . $table;

        if (!is_null($this->filter)) {
            $sql .= ' WHERE ' . $this->filter;
        }

        return $sql;
    }

    public function getIterator()
    {
        $accounts = $this->getAccounts();
        $obj = new ArrayObject($accounts);

        return $obj->getIterator();
    }
}

/**
 * Abstract class that implements items provider
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
abstract class Newsletter_Items_Provider
{
    protected $conn     = null;
    protected $database = null;
    protected $sources  = null;

    protected $items = null;

    abstract public function fetch(
        $source,
        $filter=null,
        $orderBy=null,
        $limit=null
    );

    /**
     *
     * <code>
     * // Sources sample
     *  $sources = array(
     *       'Article' => array(
     *           'table'  => 'contents',
     *           'fields' => array('pk_content', 'title',
     *                             'summary', 'permalink', 'created'),
     *           'conditions' => '(`in_litter`=0 AND `available`=1
     *                              AND `fk_content_type`=1)'
     *       ),
     *
     *       'Opinion' => array(
     *           'table'  => 'contents',
     *           'fields' => array('pk_content', 'title', 'summary',
     *                             'permalink', 'created'),
     *           'conditions' => '(`in_litter`=0 AND `available`=1
     *                             AND `fk_content_type`=4)'
     *       )
     *   );
     * </code>
    */
    public function __construct($conn, $sources, $database=null)
    {
        // TODO: control errors
        $this->conn     = $conn;
        $this->database = $database;
        $this->sources  = $sources;
    }

    /**
     * Get items
     */
    public function getItems($source, $filter=null, $orderBy=null, $limit=null)
    {
        //Newsletter_Item
        $this->items = $this->fetch($source, $filter, $orderBy, $limit);

        return $this->items;
    }

    protected function buildQuery($source, $filter)
    {
        $fields = $this->sources[$source]['fields'];
        if (is_array($fields)) {
            $fields = implode(',', $fields);
        }

        if (!is_null($this->database)) {
            $table = $this->database . '.' . $this->sources[$source]['table'];
        } else {
            $table = $this->sources[$source]['table'];
        }

        $sql = 'SELECT ' . $fields . ' FROM ' . $table;

        if (!is_null($this->filter) && is_string($filter)) {
            $sql .= ' WHERE ' . $filter;
        }

        return $sql;
    }
}

/**
 * Specialization of Accounts provider for PConecta
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
class PConecta_Newsletter_Accounts_Provider extends Newsletter_Accounts_Provider
{
    public function __construct()
    {
        // Inject dependencies
        parent::__construct($GLOBALS['application']->conn,
            'pc_users',
            'email,name,firstname,lastname',
            'status > 0 AND subscription = 1');
    }

    public function fetch()
    {
        $sql = $this->buildQuery();

        $order_by = ' ORDER BY firstname, lastname, name, email';
        $sql .= $order_by;

        $this->conn->SetFetchMode(ADODB_FETCH_ASSOC);
        $rs = $this->conn->Execute($sql);

        $this->accounts = array();

        if ($rs!==false) {
            while (!$rs->EOF) {
                $this->accounts[] = new Newsletter_Account(
                    /* email */
                    $rs->fields['email'],

                    /* firstname lastname, name */
                    $this->buildName($rs->fields['firstname'],
                                     $rs->fields['lastname'],
                                     $rs->fields['name'],
                                     $rs->fields['email'])
                ); // Newsletter_Account

                $rs->moveNext();
            }
        }

    }

    private function buildName($firstname, $lastname, $name, $email)
    {
        $name = $firstname . ' ' . $lastname . ', ' . $name;

        // Custom trim function to avoid trailing/initial commas
        $name = preg_replace('/^[ ]+\,/', '', $name);
        $name = preg_replace('/\,[ ]+$/', '', $name);
        $name = preg_replace('/[ ]+\,/', ',', $name);
        $name = trim($name);

        if (strlen($name) <= 0) {
            $name = $email;
        }

        return $name;
    }
}

/**
 * Specialization of items provider for PConecta
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
class PConecta_Newsletter_Items_Provider extends Newsletter_Items_Provider
{
    public function __construct()
    {
        $sources = array(
            'Article' => array(
                'table'  => 'contents',
                'fields' => array('pk_content', 'title',
                        'params', 'subtitle', 'created', 'category'),
                'conditions' => '`fk_content_type`=1'
            ),

            'Opinion' => array(
                'table'  => 'contents',
                'fields' => array('pk_content', 'title',
                        'permalink', 'created', 'author', 'type_opinion'),
                'conditions' => '`fk_content_type`=4'
            )
        );

        // Inject dependencies
        parent::__construct($GLOBALS['application']->conn, $sources);
    }

    /**
     *
     */
    public function fetch($source, $filters=null, $order_by=null, $limit=null)
    {
        $cm = new ContentManager();

        // $order_by
        if (is_null($order_by)) {
            $order_by = 'created DESC';
        }

        // $limit
        if (is_null($limit)) {
            $limit = '';
        } else {
            if (!preg_match('/^limit/i', $limit)) {
                $limit = 'LIMIT ' . $limit;
            }
        }

        if (is_array($filters)
            && isset($filters['category'])
            && !empty($filters['category'])
        ) {
            $category = $filters['category'];
            unset($filters['category']);

            $filter = $this->_cond2str($filters,
                $this->sources[$source]['conditions']);

            $items = $cm->find_by_category($source, $category,
               $filter, 'ORDER BY ' . $order_by . ' ' . $limit);

            // Filter in time {{{
            $items = $cm->getInTime($items);
            // }}}

        } else {
            $filter = $this->_cond2str($filters,
                $this->sources[$source]['conditions']);
            $items = $cm->find($source, $filter,
               'ORDER BY ' . $order_by . ' ' . $limit);

            // Filter in time {{{
            $items = $cm->getInTime($items);
            // }}}

            if ($source=='Opinion') {
                for ($i=0, $len=count($items); $i<$len; $i++) {
                    $items[$i]->author =
                        $items[$i]->get_author_name($items[$i]->fk_author);
                    if ($items[$i]->type_opinion == '2') {
                        $items[$i]->author = 'Carta del Director';
                    }
                }
            }
        }

        $this->items = array();
        $className   = 'PConecta_' . $source . '_Newsletter_Item';

        foreach ($items as $content) {
            $item = new $className(); // Newsletter_Item

            foreach ($this->sources[$source]['fields'] as $fld) {
                // Format date
                if ($fld == 'created') {
                    $content->{$fld} = date('H:i d/m/Y',
                        strtotime($content->{$fld}));
                }

                $item->{$fld} = clearslash(strip_tags($content->{$fld}));

                // Fix problem with special indesign characters
                $item->{$fld} = StringUtils::clearBadChars($item->{$fld});
            }

            if ($source=='Article') {
                $ccm = new ContentCategoryManager();
                $content->loadCategoryName($content->id);

                $item->category = $content->category;
                $item->category_name =
                    $ccm->get_title($ccm->get_name($item->category));
            }

            $this->items[] = $item;
            unset($item);
        }

        return $this->items;
    }

    public function buildQuery($source, $filter=null)
    {
        return null; // hide method
    }

    private function _cond2str($filters, $conditions)
    {
        $filterString = $conditions;

        if (!is_null($filters)) {
            if (!is_null($filterString) && strlen(trim($filterString))>0) {
                $filterString .= ' AND ';
            } elseif (is_null($filterString)) {
                $filterString = '';
            }

            $parts = array();
            foreach ($filters as $k => $v) {
                if (!is_numeric($k)) {
                    $parts[] = '`' . $k . '`="' . $v . '"';
                } else {
                    $parts[] = $v;
                }
            }

            $filterString .= implode(' AND ', $parts);
        }

        return $filterString;
    }
}

/**
 * Article item for newsletter in PConecta
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
class PConecta_Article_Newsletter_Item extends Newsletter_Item
{
    public $pk_content;
    public $title;
    public $params;
    public $subtitle;
    public $created;
    public $category;
    public $category_name;
}

/**
 * Opinion item for newsletter in PConecta
 *
 * @package    Onm
 * @subpackage ControllerHelper
 **/
class PConecta_Opinion_Newsletter_Item extends Newsletter_Item
{
    public $pk_content;
    public $title;
    public $permalink;
    public $created;
    public $author;
    public $type_opinion;
}
