<?php
class Bulletin {
    var $id         = null;
    var $pk_bulletin = null;
    var $data       = null;
    var $contact_list = null;
    var $attach_pdf = null;
    var $created    = null;
    var $cron_timestamp = null;

    var $HTML = null; // Contenido del boletín
    var $errors = null;

    function __construct($id=null) {
        // If not exists the schema then setup
        if(!$this->schema_exists()) {
            $this->setup();
        }

        if(!is_null($id)) {
            $this->read($id);
        }
    }

    function Bulletin($id=null) {
        $this->__construct($id);
    }

    function create($request) {
        $data = array();
        $data['data'] = clearslash($request['data_bulletin']);
        $data['contact_list'] = '';
        $data['attach_pdf'] = (isset($request['attach_pdf']))? 1: 0;
        $data['created'] = date("Y-m-d H:i:s");
        //$data['cron_timestamp'] = (isset($request['cron_timestamp_bool']))? $request['cron_timestamp_bool']: 0;
        $data['cron_timestamp'] = 0;

        $sql = "INSERT INTO bulletins_archive (`data`, `contact_list`, `attach_pdf`, `created`, `cron_timestamp`) VALUES (?,?,?,?,?)";

        $values = array(clearslash($data['data']), clearslash($data['contact_list']),
                        $data['pdf_format'], $data['created'], $data['cron_timestamp']);

        if($GLOBALS['application']->conn->Execute($sql, $values) === false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return(false);
        }

        $this->id = $GLOBALS['application']->conn->Insert_ID();

        return(true);
    }

    function read($id) {
        $sql = 'SELECT * FROM bulletins_archive WHERE pk_bulletin = '.intval($id);
        $rs = $GLOBALS['application']->conn->Execute( $sql );

        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }

        $this->id             = $id;
        $this->pk_bulletin    = $id;
        $this->data	          = $rs->fields['data'];
        $this->contact_list   = $rs->fields['contact_list'];
        $this->attach_pdf     = $rs->fields['attach_pdf'];
        $this->created        =  $rs->fields['created'];
        $this->cron_timestamp =  $rs->fields['cron_timestamp'];
        
        //return( $this );
    }

    function search($filter=null) {
        $bulletins = array();
        
        if(is_null($filter)) {
            $filter = '1=1';
        }
        
        $sql = 'SELECT * FROM bulletins_archive WHERE '.$filter;
        $rs = $GLOBALS['application']->conn->Execute( $sql );
        
        if (!$rs) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;
            
            return( $bulletins );
        }
        
        while(!$rs->EOF) {
            $bulletins[] = new Bulletin( $rs->fields['pk_bulletin'] );
            $rs->MoveNext();
        }
        
        return( $bulletins );
    }

    function update() {
        // Nothing
    }

    function delete($id) {
		$sql = 'DELETE FROM bulletins_archive WHERE pk_bulletin='.intval($id);

        if($GLOBALS['application']->conn->Execute($sql)===false) {
            $error_msg = $GLOBALS['application']->conn->ErrorMsg();
            $GLOBALS['application']->logger->debug('Error: '.$error_msg);
            $GLOBALS['application']->errors[] = 'Error: '.$error_msg;

            return;
        }
    }

    function purge_mailboxes($destinatarios) {
        $output = array();

		$destinatarios = preg_replace('/\s\s+/', ' ', $destinatarios);
		$destinatarios = str_replace("\n", ',', $destinatarios);
		$destinatarios = str_replace("\r", ',', $destinatarios);
		$destinatarios = str_replace("\t", ',', $destinatarios);
		$destinatarios = str_replace(" ", ',', $destinatarios);

		$destinatarios = explode(',', $destinatarios);
		foreach($destinatarios as $destinatario) {
			$destinatario = trim($destinatario);
			if( preg_match('/^[a-zA-Z0-9_\.\-]+\@[a-zA-Z0-9-]+\.[a-zA-Z0-9\-\.]+$/', $destinatario) ) {
				$output[] = $destinatario;
			} else {
                if(strlen(trim($destinatario))>0) {
                    $this->errors[] = 'Destinatario ['.$destinatario.']no incluído en el envío.';
                }
            }
		}

		return( $output );
    }
    
    function prependItems($items_id, $type="Article") {
        $bulk_data = array();
        if(is_array($items_id)) {
            foreach($items_id as $i => $id) {
                $tmp = new stdClass();
                
                //$obj = new $type($id);
                //$properties = get_object_vars($obj);
                //foreach($properties as $property => $value) {
                //    $tmp->{$property} = base64_encode($value);
                //}
                //$tmp->id = $i;

                if($type == 'Article') {
                    $article = new Article($id);
                    $tmp->id         = $i;
                    $tmp->pk_content = $article->id;
                    $tmp->title      = base64_encode($article->title);
                    $tmp->subtitle   = base64_encode($article->subtitle);
                    $tmp->summary    = base64_encode($article->summary);                                         
                    $tmp->permalink  = base64_encode($article->permalink);
                    
                } else {
                    $opinion = new Opinion($id); // new $type($id);
                    $tmp->id         = $i;
                    $tmp->pk_content = $opinion->id;
                    $tmp->title      = base64_encode($opinion->title);
                    
                    $summary         = Bulletin::filterString($opinion->body);
                    $tmp->summary    = base64_encode( $summary );
                    
                }
                
                $bulk_data[] = $tmp;
            }
        }      
        
        return($bulk_data);
    }
    
    function filterString($string) {        
        $string  = String_Utils::str_stop( strip_tags( stripslashes($string) ), 60);
        
        //$string  = utf8_encode( html_entity_decode($string) ); 
        
        //$string =  String_Utils::unhtmlentities($string);
        $string  = preg_replace('/&[^;]+;/', '', $string);
        
        return($string);
    }
    
    function sortArticles($articles) {        
        $grouped_by_category = array();
        foreach($articles as $article) {
            if($article->content_status == 1) { // Publicado
                $grouped_by_category[ $article->category_name ][] = $article;
            }
        }
        
        return( $grouped_by_category );
    }    
    
    

    function send($mailboxes, $htmlcontent, $params) {
        $mailboxes = $this->purge_mailboxes($mailboxes);

        foreach($mailboxes as $mailbox) {
            $this->send_to_user($mailbox, $htmlcontent, $params);
        }
    }

	function send_to_user( $destinatario, $htmlcontent, $params ) {
        require_once('libs/phpmailer/class.phpmailer.php');

		$mail = new PHPMailer();
        $mail->SetLanguage('es');
		$mail->IsSMTP();
		$mail->Host = MAILHOST;
		$mail->SMTPAuth = true;

		$mail->Username = MAILUSER;
		$mail->Password = MAILPASS;

        // FIXME: Eliminar las cadenas y poner constantes Ó implementar una clase especializada para envíos
		$mail->From = "boletin@xornal.com";
		$mail->FromName = utf8_decode("Xornal de Galicia");
		$mail->IsHTML(true);
        $this->HTML = $htmlcontent;

		$mail->AddAddress($destinatario, $destinatario);
        
        // Embeber el logotipo
        // FIXME: crear una plantilla de boletín donde se especifiquen los detalles, logo, dirección destinatario, ...
        $mail->AddEmbeddedImage(PATH_APP.'../media/xornal-boletin.jpg', 'logo-cid', 'Logotipo');

		/* for($i=0; $i < count($this->imgs); $i++) {
			$imaxen = preg_replace('', '<img src="cid:my-photo" />', $this->HTML);
			$mail->AddEmbeddedImage(dirname(__FILE__).'/'.$imaxen, 'img'.($i+1), $imaxen);
            
            // <img src="cid:my-photo-cid" alt="my-photo" />
            // $mail->AddEmbeddedImage('my-photo.jpg', 'my-photo-cid', 'Name'));
		} */

        if(intval($params['attach_pdf'])==1) {
            $mail->AddAttachment($params['pdf_filename'], 'xornal.com-boletin.pdf');
        }

		//$meses = array('', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII');
        $meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		$mail->Subject  = utf8_decode("Xornal de Galicia").' ['.date("d").'/'.$meses[(int)date('m')].'/'.date("Y").']';

		/* $this->HTML = preg_replace('/>[^<]*"[^<]*</', '&#34;', $this->HTML);
		$this->HTML = preg_replace("/>[^<]*'[^<]*</", '&#39;', $this->HTML); */

        // TODO: crear un filtro
		$this->HTML = preg_replace('/(>[^<"]*)["]+([^<"]*<)/', "$1&#34;$2", $this->HTML);
		$this->HTML = preg_replace("/(>[^<']*)[']+([^<']*<)/", "$1&#39;$2", $this->HTML);
		$this->HTML = str_replace('“', '&#8220;', $this->HTML);
		$this->HTML = str_replace('”', '&#8221;', $this->HTML);
		$this->HTML = str_replace('‘', '&#8216;', $this->HTML);
		$this->HTML = str_replace('’', '&#8217;', $this->HTML);

		$mail->Body = utf8_decode( $this->HTML );

		if(!$mail->Send()) {
            $this->errors[] = "Error en el envío del mensaje " . $mail->ErrorInfo;
            echo('<pre>');
            print_r($mail);
            echo('</pre>');
		}
	}

    function get_pdf($htmlcontent, $filename, $action='F') {
        // Descargar as fontes dende http://developer.jelix.org/browser/trunk/lib/fonts?rev=677
        require_once('../libs/tcpdf/tcpdf.php');

        $doc_title    = "Xornal de Galicia";
        $doc_subject  = "Boletín - Xornal de Galicia";
        $doc_keywords = "noticias, periódico, xornal, Galicia";

        $l = array('a_meta_charset' => "UTF-8", 'a_meta_dir' => "ltr",
                   'a_meta_language' => "es", 'w_page' => "página");

        //create new PDF document (document units are set by default to millimeters)
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

        // set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor(PDF_AUTHOR);
        $pdf->SetTitle($doc_title);
        $pdf->SetSubject($doc_subject);
        $pdf->SetKeywords($doc_keywords);

        $meses = array('', 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
		$fecha = "                                    Xornal de Galicia".
		' ['.date("d").'/'.$meses[(int)date('m')].'/'.date("Y").']';
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, $fecha);

        //set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        //set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO); //set image scale factor

        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

        $pdf->setLanguageArray($l); //set language items

        //initialize document
        $pdf->AliasNbPages();

        $pdf->AddPage();

        // output some HTML code
        $pdf->writeHTML($htmlcontent, true, 0);

        //Close and output PDF document
        $pdf->Output($filename, $action);
    }

    function schema_exists() {
        $dict = NewDataDictionary($GLOBALS['application']->conn);
        $tables = $dict->MetaTables();

        return( in_array('bulletins_archive', $tables) );
    }

    function setup() {
        require_once(dirname(__FILE__).'/../libs/adodb5/adodb-xmlschema.inc.php');
        $schema = new adoSchema( $GLOBALS['application']->conn );

        // Schema for bulletins support.
        $axmls = '<?xml version="1.0"?>
                <schema version="0.2">
                  <table name="bulletins_archive">
                    <desc>Table to archive bulletins.</desc>

                    <field name="pk_bulletin" type="I">
                      <descr>Identificator.</descr>
                      <KEY/>
                      <AUTOINCREMENT/>
                    </field>

                    <field name="data" type="XL"></field>

                    <field name="contact_list" type="XL"></field>

                    <field name="attach_pdf" type="L"></field>

                    <field name="created" type="T">
                        <DEFTIMESTAMP />
                    </field>

                    <field name="cron_timestamp" type="T"></field>

                  </table>

                  <sql>
                    <descr>Insert some data into the users table.</descr>
                  </sql>
                </schema>';


        $sql = $schema->ParseSchemaString( $axmls );
        $result = $schema->ExecuteSchema();
    }
}

