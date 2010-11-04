<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

define('PATH_FILES', realpath(dirname(__FILE__).'/../media/files/'));
require_once(SITE_LIBS_PATH.'class.dir.php');

function readAtt($ruta, $category){
    $attachment = new Attachment();	     
    $att = $attachment->readid( $ruta, $category);
    if($id == ""){ //Si no encuentra, busca en cualquier categoria ¿?¿?¿? Q M Dices!!!
        $att = $attachment->readids($ruta);			
    }
    
    return $att;
}

class DialogExplorer {
    var $path = NULL;
    var $root = PATH_FILES; // PATH_UPLOAD, en config.inc.php define('PATH_UPLOAD', realpath(dirname(__FILE__).'/../media/images/'));
    var $directories = array();
    var $files = array();
    var $filter = '';

    function DialogExplorer($path, $root=null, $filter=null) {
    
        $this->path = $this->unurlize($path);

        if(!is_null($root)) {
            $this->root = $root;
        }

        if(!is_null($filter)) {
            $this->filter = '--e '.$filter;
        }

        if( !$this->is_path_valid() ) {
            $this->path = $this->root;
        }

        $dir = new dir( $this->path );
        $this->directories = $dir->LIST_dir();
        $this->files = $dir->LIST_files( $this->filter );
    }

    function __construct($path, $root=null, $filter=null) {
        $this->DialogExplorer($path, $root, $filter);
    }

    function is_path_valid() {
        return( strcmp(realpath($this->root), realpath($this->path)) <= 0 );
    }

    function get_directories() {
        $dirs = array();
        foreach($this->directories as $v) {
            $dirs[] = array( 'basename' => basename($v),
                             'relative' => $this->relativize($v),
                             'url'      => $this->urlize($v) );
        }
        
        return( $dirs );
    }

    function get_files() {
        $files = array();
        foreach($this->files as $v) {
            $details = @stat( $v );
           
            $files[] = array( 'basename' => basename($v),
                              'relative' => $this->relativize($v),
                              'url'      => $this->urlize($v),
                              'mtime'	=> $details['mtime'],                            
							  'size'   => $details['size']);
							  
        }

        return( $files );
    }

    function relativize($path) {
        $path = preg_replace('@^('.$this->normalize(realpath($this->root)).'/)(.*?)@i', '\2',
                             $this->normalize(realpath($path)) );

        return( $path );
    }

    function normalize($path) {
    	return(str_replace("//", "/", str_replace("\\", "/", $path)) );
    }

    function urlize($path) {
        $path = preg_replace('@^('.$this->normalize(realpath($_SERVER['DOCUMENT_ROOT'])).'/)(.*?)@i', '\2',
                             $this->normalize(realpath($path)) );
        return('http://'.$_SERVER['SERVER_NAME'].'/'.$path);
    }

    function unurlize($path) {
        if(preg_match('@^http:\/\/@i', $path) ) {
            $url = parse_url ( $path );
            $path = $_SERVER['DOCUMENT_ROOT'].'/'.$url['path'];
            $path = $this->normalize(realpath($path));

            //return($path);
        }

        return($path);
    }

    function path2url($path) {
        $doc_root = str_replace("//", "/", str_replace("\\", "/", realpath($_SERVER['DOCUMENT_ROOT'])));
        $path = str_replace("//", "/", str_replace("\\", "/", realpath($path)));

        $path = preg_replace('@^('.$doc_root.'/)(.*?)@i', '\2', $path);
        return('http://'.$_SERVER['SERVER_NAME'].'/'.$path);
    }

    function url2path($path) {
        if(preg_match('@^http:\/\/@i', $path) ) {
            $url = parse_url ( $path );
            $path = realpath($_SERVER['DOCUMENT_ROOT'].'/'.$url['path']).'/';
            $path = str_replace("//", "/", str_replace("\\", "/", $path));

            return($path);
        }

        return($path);
    }

	function truncate_r($str, $len, $el = '...') {
		if(strlen($str) > $len) {
			$str = substr($str, -$len);
			$str = $el.$str;
		}
		return $str;
	}

	function new_folder($name, $path) {
		$folder = $path.$name;
		if(!file_exists($folder) && !is_dir($folder)) {
			@mkdir($folder, 0755);
		}
	}
}


$category = $_REQUEST['category'];	
$cc = new ContentCategoryManager();
$cat = $cc->get_name($_REQUEST['category']);

// Comprobación de que existe la carpeta para la sección
if( file_exists(PATH_FILES."/".$cat) && is_dir(PATH_FILES."/".$cat) ) {
    $initial_path = PATH_FILES."/".$cat;
} else {
    $initial_path = PATH_FILES;
}

if(isset($_POST['nueva_carpeta']) && ($_POST['nueva_carpeta']!= '')) {
	DialogExplorer::new_folder($_POST['nueva_carpeta'], DialogExplorer::url2path($_POST['path']) );
}

$path = (isset($_POST['path']))? DialogExplorer::path2url(DialogExplorer::url2path($_POST['path'])) : DialogExplorer::path2url( $initial_path );
$dialog = new DialogExplorer($path);


?>

<html>
<head>
<title>:: Gestor de Archivos ::</title>
<link href="themes/default/css/explorador.css" rel="stylesheet" type="text/css" />

<script src="themes/default/js/utilesarticle.js" language="javascript" type="text/javascript"></script>
<script src="themes/default/js/prototype.js" language="javascript" type="text/javascript"></script>
<script src="themes/default/js/scriptaculous/scriptaculous.js?load=effects" language="javascript" type="text/javascript"></script>

<script language="javascript">

function seleccionado(list) {
    $('campo1').value = list.value;
	$('id').value = list.options[list.selectedIndex].getAttribute('de:id');
	$('titulo').value = list.options[list.selectedIndex].getAttribute('de:titulo');
	
 
}

var MAX_XY = 200;
var carac =new Object(); //para almacenar los datos de la imagen



function change_dir(elto) {
    if(elto.value != '') {
        var form = elto.parentNode;
        while((form.nodeName != 'FORM') && (form.nodeName != 'BODY')) {
            form = form.parentNode;
        }

        // TODO: Para version 2, cambiar a peticion AJAX
        try {
            form.path.value = elto.options[elto.selectedIndex].getAttribute('de:url');
            form.submit();
        } catch(e) {}
    }
}

function subir(frm) {
	if(frm.nodeName != 'FORM') {
		var elto = frm.parentNode;
		while((elto.nodeName != 'FORM') && (elto.nodeName != 'BODY')) {
			elto = elto.parentNode;
		}

		frm = elto;
	}
    frm.path.value = frm.path.value + '/../';
    frm.submit();
}


  function meterLista(eleto){
	  
	  var ul = opener.document.getElementById("thelist2");	   
	  Nodes = opener.document.getElementById("thelist2").getElementsByTagName("li");	  
			var valor = $('titulo').value;
	  		var li = opener.document.createElement('LI');
			li.setAttribute('id', eleto);			
		    li.setAttribute('class', 'family');
		    li.setAttribute('style', 'cursor: move; list-style-type: none;');
		    li.setAttribute('value', valor);
		    li.setAttribute('recordid', '100');
			li.innerHTML =  " - " + valor;				
			ul.appendChild(li);				
	  			
  } 
  

  function meterListaint(eleto){
	   var ulint = opener.document.getElementById('thelist2int');
	  Nodes = opener.document.getElementById('thelist2int').getElementsByTagName('li');	  			
			var valor = $('titulo').value;
	  		var li = opener.document.createElement('LI');
			li.setAttribute('id', eleto);			
		    li.setAttribute('class', 'family');
		    li.setAttribute('style', 'cursor: move; list-style-type: none;');
		    li.setAttribute('value', valor);
		    li.setAttribute('recordid', '100');
			li.innerHTML =  '- ' + valor;					
			ulint.appendChild(li);				

  } 

 
  
function devolver_campo(elto) {
	var frm = elto.parentNode;
	while(frm.nodeName != "FORM") {
		frm = frm.parentNode;
	}
    var campo_retorno = $('campo_retorno').value;
    
    var existsElement = opener.document.getElementById('capa'+ frm.id.value);
    if( (existsElement != null) && (existsElement.style.display != 'none')) {
        alert('El fichero ya está como adjunto de la noticia');
        return(false);
    }
    
    if(frm.titulo.value == '') {
	 	alert("Escriba un titulo");
	} else {  
   		if(frm.campo1.value != '') {	
           	var nuevo = " <div id='capa"+ frm.id.value + "' style='display: inline;'><table border='0' cellpadding='4' cellspacing='0' class='fuente_cuerpo'  width='100%'> "+
            " <tr bgcolor='#ffffff'> <td width='50%'>  <input type='text' id='titles["+frm.id.value+"]' name='titles["+frm.id.value +"]' class='required' size='70' onChange=cambiarlistas("+frm.id.value+",'titles["+frm.id.value +"]');  value='"  +frm.titulo.value +  "' /> </td> <td>"+ frm.campo1.value +"</td> <td align='center' width='80'> "+
            "<input name='attach_selectos[]' class='pru' value='" +frm.titulo.value + "' id='por"+ frm.id.value + "' type='checkbox' checked='checked' onClick=javascript:probarAttach('por"+ frm.id.value + "','thelist2');></td><td align='center' width='80'><input type='checkbox' id='int"+ frm.id.value +"' value='"+ frm.titulo.value +"' name='att_interior[]' checked='checked' onClick=javascript:probarAttach('int"+ frm.id.value + "','thelist2int');></td> <td align='center' width='80'> <a  href='#'  onclick=javascript:ocultar('"+frm.id.value +"');  title='Desvincular'> <img src='themes/default/images/trash.png' border='0' /> </a>  </td></tr></table> ";
            
            opener.document.getElementById('adjunto').innerHTML = opener.document.getElementById('adjunto').innerHTML + nuevo ;		  					 
            meterLista("por"+ frm.id.value);
            meterListaint("int"+ frm.id.value);
            cerrar();			
		}
	} 
}

function crear_nueva_carpeta(elto) {
	var nombre = prompt('Crear una nueva carpeta. Introduzca el nombre:', 'nueva_carpeta');
	if(nombre != '') {
		var frm = elto.parentNode;
		while(frm.nodeName != "FORM") {
			frm = frm.parentNode;
		}

		frm.nueva_carpeta.value = nombre;
		frm.submit();
	}
}

function cerrar() {
	setTimeout(hdl_cerrar, 200);
}

function hdl_cerrar() {

	window.close();
}

function atras() {
	history.back();
}





/*************************************************************************/
        function uploadFileCancelled(file, queuelength) {
            var li = document.getElementById(file.id);
            li.innerHTML = file.name + " - cancelled";
            li.className = "SWFUploadFileItem uploadCancelled";
            var queueinfo = document.getElementById("queueinfo");
            queueinfo.innerHTML = queuelength + " files queued";
        }

        function uploadFileStart(file, position, queuelength) {
            var div = document.getElementById("queueinfo");
            div.innerHTML = "Subiendo fichero " + position + " de " + queuelength;

            var li = document.getElementById(file.id);
            li.className += " fileUploading";
        }

        function uploadProgress(file, bytesLoaded) {
            var progress = document.getElementById(file.id + "progress");
            var percent = Math.ceil((bytesLoaded / file.size) * 200)
            progress.style.background = "#f0f0f0 url(<?=RELATIVE_PATH?>themes/default/js/SWFUpload/images/progressbar.png) no-repeat -" + (200 - percent) + "px 0";
        }

        function uploadError(errno) {
            SWFUpload.debug(errno);
        }

        function uploadFileComplete(file) {
            var li = document.getElementById(file.id);
            li.className = "SWFUploadFileItem uploadCompleted";

            $('formulario').submit();
        }
</script>
<style type="text/css">

		.swfuploadbtn {
			display: block;
			width: 100px;
			padding: 0 0 0 20px;
		}

		.browsebtn { background: url(<?=RELATIVE_PATH?>themes/default/js/SWFUpload/images/add.png) no-repeat 0 4px; }
		.uploadbtn {
			display: none;
			background: url(<?=RELATIVE_PATH?>themes/default/js/SWFUpload/images/accept.png) no-repeat 0 4px;
		}

		.cancelbtn {
			display: block;
			width: 16px;
			height: 16px;
			float: right;
			background: url(<?=RELATIVE_PATH?>themes/default/js/SWFUpload/images/cancel.png) no-repeat;
		}

		#cancelqueuebtn {
			display: block;
			display: none;
			background: url(<?=RELATIVE_PATH?>themes/default/js/SWFUpload/images/cancel.png) no-repeat 0 4px;
			margin: 10px 0;
		}

		#SWFUploadFileListingFiles ul {
			margin: 0;
			padding: 0;
			list-style: none;
			position: absolute;
		}

		.SWFUploadFileItem {

			display: block;
			width: 230px;
			height: 70px;
			float: left;
			background: #eaefea;
			margin: 0 10px 10px 0;
			padding: 5px;

		}

		.fileUploading { background: #fee727; }
		.uploadCompleted { background: #d2fa7c; }
		.uploadCancelled { background: #f77c7c; }

		.uploadCompleted .cancelbtn, .uploadCancelled .cancelbtn {
			display: none;
		}

		span.progressBar {
			width: 200px;
			display: block;
			font-size: 10px;
			height: 4px;
			margin-top: 2px;
			margin-bottom: 10px;
			background-color: #CCC;
		}

		h4 {
			font-size: 10px;
			font-family: Arial, Verdana, Courier;
			font-weight: normal;
		}

	</style>
</head>
<body>

<form action="#" method="POST" id="formulario">
	<table border="0" cellpadding="2" cellspacing="0" align="center" width="100%">
	<tr>
		<td colspan="2">
			<h2 class="path"><?=$dialog->truncate_r($dialog->path2url($dialog->path), 60)?></h2>
		</td>
	</tr>

	<tr>
		<td colspan="2">
			<label for="dirs">Directorios:</label>
			<select id="dirs" name="dirs" class="campo" style="width: 210px;" onchange="change_dir(this);">
	            <option value=""  selected="selected"></option>
			<?php
			    $directories = $dialog->get_directories();
				for($i=0; $i<count($directories); $i++) {
						
					echo "\n".'<option value="'.$directories[$i]['relative'].'" de:url="'.$directories[$i]['url'].'"';					
					echo('>'.$directories[$i]['basename'].'</option>');
				}
			?>
			    <option value="<?=$path.'/../'?>" de:url="<?=$path.'/../'?>">..</option>
			</select>
			&nbsp;&nbsp;
			<a href="javascript:;" onclick="$('formulario').submit();">
				<img src="images/iconos/reload.gif" border="0" /></a>
			<a href="javascript:;" onclick="atras();">
				<img src="images/iconos/atras.gif" border="0" /></a>
			<a href="javascript:;" onclick="subir(this);">
				<img src="images/iconos/subir.gif" border="0" /></a>
			<a href="javascript:;" onclick="crear_nueva_carpeta(this);">
				<img src="images/iconos/nueva_carpeta.gif" border="0" /></a>
		</td>
	</tr>

	<tr>
		<td>
			<label for="files">Ficheros:</label>
		</td>
		<td >
			<!-- <label>Vista previa:</label> --> &nbsp;
		</td>
	</tr>
	<tr>
	<td align="right"> &nbsp;
        	
		</td>
		<td >
			<div id="files" align="right">
	            <select id="de_file" name="de_file" class="campo" size="10" style="width:340px; overflow:scroll;" onchange="seleccionado(this);"> 
	    		<?php
	    		    $ficheros = $dialog->get_files();
	    			for($i=0; $i<count($ficheros); $i++) {
	    			// echo " -". $ficheros[$i]['basename']." -cc- ".$_REQUEST['category'];
	    			    $att = readAtt($ficheros[$i]['basename'],$_REQUEST['category']);
	    			    if($att['id']){
	    				 echo('<option  de:id="'.$att['id'].'" de:titulo="'.$att['titulo'].'" value="'.$ficheros[$i]['relative'].'" de:url="'.$ficheros[$i]['url'].'" >'.$ficheros[$i]['basename'].'</option>'."\n");	    				 
            			}	
	    			}
	    		?>
	    		</select> 
	        </div>
		</td>
		
	</tr>

	<tr>
		<td colspan="2" nowrap>
			<label>Archivo: </label><input type="text" class="campo" name="campo1" id="campo1" value="" style="width: 80%;" />
		</td>
      </tr>
	<tr>
		<td colspan="2" nowrap>
			<label>T&iacute;tulo: </label><input type="text" class="titulo" name="titulo" id="titulo" value="" style="width: 84%;" />				
				<input type="hidden" class="campo" name="id" id="id" value="" style="width: 100%;" />
		</td>
	</tr>
	<tr>
		
		<td align="right" valign="top" colspan="2">
			<input type="button" class="boton" accesskey="S" onclick="javascript:devolver_campo(this);" value=" Seleccionar " />
            <input type="button" class="boton" accesskey="C" onclick="javascript:cerrar();" value=" Cancelar " />
        </td>
	</tr>
	</table>

	<input type="hidden" name="nueva_carpeta" value="" />
    <input type="hidden" name="path" value="<?=$path?>" />
    <input type="hidden" name="campo_retorno" id="campo_retorno" value="<?=$_REQUEST['campo_retorno']?>" />
</form>

</body>
</html>