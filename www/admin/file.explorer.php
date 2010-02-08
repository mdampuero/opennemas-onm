<?php
require_once('./config.inc.php');
require_once('./session_bootstrap.php');

require_once "libs/class.dir.php";

class DialogExplorer {
    var $path = NULL;
    var $root = PATH_UPLOAD; // PATH_UPLOAD, en config.inc.php
    var $directories = array();
    var $files = array();
    var $filter = '--e jpg,jpeg,png,gif,swf';

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
        arsort($this->files);
		reset($this->files);
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

	$category=$_REQUEST['category'];	
	if($category){	 
		if($category==1){
		$cat="cronicas";
		}elseif($category==2){
		$cat="galicia";
		}elseif($category==3){
		$cat="asturias";
		}elseif($category==4){
		$cat="canarias";
		}elseif($category==5){
		$cat="castillaleon";
		}elseif($category==6){
		$cat="cantabria";
		}elseif($category==7){
		$cat="madrid";
		}elseif($category==8){
		$cat="baleares";
		}elseif($category==9){
		$cat="andalucia";
		}elseif($category==10){
		$cat="opinion";
		}elseif($category==11){
		$cat="advertisements";
		}elseif($category==12){
		$cat="eventos";
		}
		
	}

if(isset($_POST['nueva_carpeta']) && ($_POST['nueva_carpeta']!= '')) {
	DialogExplorer::new_folder($_POST['nueva_carpeta'], DialogExplorer::url2path($_POST['path']) );
}

$path = (isset($_POST['path']))? DialogExplorer::path2url(DialogExplorer::url2path($_POST['path'])): 	DialogExplorer::path2url(PATH_UPLOAD."/".$cat);
$dialog = new DialogExplorer($path);
?>

<html>
<head>
<title>:: Gestor Multimedia ::</title>
<link href="themes/default/css/explorador.css" rel="stylesheet" type="text/css" />

<!-- script src="themes/default/js/SWFUpload/SWFUpload.js" language="javascript" type="text/javascript" ></script -->
<script src="themes/default/js/swfobject.js" language="javascript" type="text/javascript"></script>

<script src="themes/default/js/prototype.js" language="javascript" type="text/javascript"></script>
<script src="themes/default/js/scriptaculous/scriptaculous.js?load=effects" language="javascript" type="text/javascript"></script>

<script language="javascript">

function isNameOk(name) {
//ojo ruta: 
	var posic=name.lastIndexOf('/');
	posic=posic+1; //Para que coja la /
	var nombre= name.substring(posic);	
	var filter=/^[0-9A-Za-z_]+\.[A-Za-z][A-Za-z][A-Za-z]$/;
	
	if (filter.test(nombre))
	   return true;
	else
	   return false;	
}

function seleccionado(list) {
    $('campo1').value = list.value;
	$('fecha').value = list.options[list.selectedIndex].getAttribute('de:fecha');
	$('tamanho').value= list.options[list.selectedIndex].getAttribute('de:tam');
    preview( list.options[list.selectedIndex].getAttribute('de:url') );
}

var MAX_XY = 200;
var carac =new Object(); //para almacenar los datos de la imagen


function scale(img) {
	if((img.clientWidth > img.clientHeight) && (img.clientWidth > MAX_XY)) {
        img.style.height = Math.round( (img.clientHeight * MAX_XY) /img.clientWidth ) + 'px';
        img.style.width = MAX_XY + 'px';
    } else {
	    if((img.clientHeight > img.clientWidth) && (img.clientHeight > MAX_XY)) {
	    	img.style.width = Math.round( (img.clientWidth * MAX_XY) /img.clientHeight ) + 'px';
	        img.style.height = MAX_XY + 'px';
	    }
	}
}


function preview(src) {
    if( /\.(gif|png|jpg|jpeg)$/i.test(src) ) {
        var imagen = new Image();
        imagen.border = 0;

        // Imagen mientras precarga
        $('preview').innerHTML = '<img src="<?=RELATIVE_PATH?>themes/default/images/cargando.gif" border="0" />'

        imagen.onload = function() {
            $('preview').innerHTML = '';
            $('preview').appendChild( imagen ); 
                    
                   //Caracteristicas de la imagen  
            carac.ancho= imagen.clientWidth;
            carac.alto= imagen.clientHeight;
            carac.peso=  imagen.fileSize; //solo IE {math x=$listFiles[f]->size y=""px <br> <b>tama�o: </b>" +$('tamanho').value" equation="round(x/y)"} KB &nbsp;
            carac.src =src;
            
            scale( imagen );

            // Centrar imagen verticalmente
            // No funciona en IE utilizando el padding, con margin
            //imagen.style.paddingTop = Math.floor((MAX_XY - imagen.clientHeight)/2) + 'px';
            imagen.style.marginTop = Math.floor((MAX_XY - imagen.clientHeight)/2) + 'px';

            /*
            Element.makeClipping( 'preview' );
            imagen.style.cursor = 'move';
            // Mirar en los options la propiedad: "snap"
            new Draggable(imagen);
            */
        };

        // Establecemos la fuente de la imagen
        imagen.src = src;
    } else {
        var so = new SWFObject(src, "mymovie", MAX_XY, MAX_XY, "7", "#FFFFFF");
    	so.addParam("wmode", "transparent");
    	so.addParam("quality", "high");
    	so.write('preview');
    	
    }
}

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

function preview_in_opener(frm, campo_retorno) {
	var fijo=carac.ancho;
	if(fijo>300){
		fijo=300;
	}
	var alti=carac.alto;
	if(alti>200){
		alti=200;
	}
	if(carac.ancho < carac.alto) {
		    var html_str = '<img src="<?=URL_UPLOAD?>/'+frm.campo1.value+'" border="0"  height="'+alti+'"  style="margin-left: 60px;" />';
		    opener.document.getElementById('preview_'+campo_retorno).innerHTML = html_str;
    }else{
		     var html_str = '<img src="<?=URL_UPLOAD?>/'+frm.campo1.value+'" border="0"  width="'+fijo+'"  style="margin-left: 10px;" />';
		    opener.document.getElementById('preview_'+campo_retorno).innerHTML = html_str;
    }
      if( /\.(swf)$/i.test(frm.campo1.value) ) {
           var html_str = '<object ><param name="movie" value="<?=URL_UPLOAD?>/'+frm.campo1.value+'"><embed src="<?=URL_UPLOAD?>/'+frm.campo1.value+'" width="300" ></embed></object>';
		    opener.document.getElementById('preview_'+campo_retorno).innerHTML = html_str;
      }

}


function devolver_campo(elto) {
	var frm = elto.parentNode;
	while(frm.nodeName != "FORM") {
		frm = frm.parentNode;
	}

    var campo_retorno = $('campo_retorno').value;

    if(frm.campo1.value != '') {
      if(campo_retorno != 'imgs' ) { //opinion    
		   if(campo_retorno == 'img1' ) {
		     		if(opener.document.getElementById( 'posic' ).value == "0") {			     			
				   		  	opener.document.getElementById( 'informa' ).innerHTML = " <table border='0' bgcolor='#ccffbb'  width='370' cellpadding='4'><tr><td> <b> Recuerde:</b> <br>El ancho de la imagen correcto para la noticia de  cabecera <br> es de 650px, 300px y 130px en las columnas</td></tr></table>" ;	  		  						   		
			        } else{			        
				         if(opener.document.getElementById( 'posic' ).value == "1") {		     
					   		  if((carac.ancho == 650) || (carac.ancho == 300)) {
					   		  	opener.document.getElementById( 'informa' ).innerHTML = " <table border='0' bgcolor='#ccffbb' width='370' cellpadding='4'><tr><td> <b> Aceptada. </b> <br>El ancho de la imagen es correcto para la portada. </td></tr></table>" ;	  		  		
					   		  }else{		   		  	
					   		  	opener.document.getElementById( 'informa' ).innerHTML = " <table border='0' bgcolor='red' width='370' cellpadding='4'><tr><td> <b> Cuidado: </b> <br>El ancho no es correcto para la portada. </td></tr></table>" ;
					   		  }		  
				   		 }else {
				   		   if(carac.ancho <= 130) {
					   		  	opener.document.getElementById( 'informa' ).innerHTML = " <table border='0' bgcolor='#ccffbb' width='370' cellpadding='4'><tr><td> <b> Aceptada. </b> <br>El ancho de la imagen es correcto para las columnas de la portada. </td></tr></table>" ;	  		  		
					   		  }else{		   		  	
					   		  	opener.document.getElementById( 'informa' ).innerHTML = " <table border='0' bgcolor='red' width='370' cellpadding='4'><tr><td> <b> Cuidado: </b> <br>El ancho no es correcto para las columnas de la portada. </td></tr></table>" ;
					   		  }	
				   		 
				   		 } 
				   		 var infor2= opener.document.getElementById( 'informa' ).innerHTML ;
				   		 if(carac.ancho == 650) {
					   		  	opener.document.getElementById( 'informa' ).innerHTML = infor2 + " <table border='0' bgcolor='#ccffbb' width='370' cellpadding='4'><tr><td> <b> La imagen ocupar&aacute; dos columnas en la portada </b></td></tr></table>" ;	  		  		
					     }else if(carac.ancho == 300){		   		  	
					   		  	opener.document.getElementById( 'informa' ).innerHTML = infor2 + " <table border='0' bgcolor='#ccffbb' width='370' cellpadding='4'><tr><td> <b> La imagen ocupar&aacute; una columna en la portada </b> </td></tr></table>" ;
					   	 }else{
					   	 		opener.document.getElementById( 'informa' ).innerHTML = infor2 + " <table border='0' bgcolor='red' width='370' cellpadding='4'><tr><td> <b> La imagen no tiene un ancho correcto para la portada. <br>Debe tener 130px, 650px o 300px (una o dos columnas la principal)  </b></td></tr></table>" ;
					   	 }	 
				   	}     	
			          opener.document.getElementById('informa').innerHTML = opener.document.getElementById('informa').innerHTML +"<table border='0'width='370' cellpadding='4'><tr><td> <b> Archivo: </b>" + frm.campo1.value + "<br> <b>Ancho: </b>" + carac.ancho + "px <br> <b>Alto: </b>"+ carac.alto + "px <br> <b>Peso: </b>" +$('tamanho').value +" KB <br> <b>Fecha de creacion: </b>" +$('fecha').value +"</td></tr></table>";		          
			          opener.document.getElementById( 'noinfor' ).style.display = "inline";
					  opener.document.getElementById('informa').style.display = "inline";
					  opener.document.getElementById('noimag').style.display = "none";
					if(!isNameOk(frm.campo1.value)){
					   	  opener.document.getElementById( 'noinfor' ).innerHTML =  "<table border='0' bgcolor='orange' width='370' cellpadding='4'><tr><td> <b> Cuidado: </b> <br>El Nombre contiene espacios en blanco o caracteres especiales.</td></tr></table>" ;
					}
					if(opener.document.getElementById('eje1')){
						opener.document.getElementById('eje1').style.display = "none";
					}
		     }else{
		    	  if(carac.ancho == 300) {
		   		  	opener.document.getElementById( 'informa2' ).innerHTML = " <table border='0' bgcolor='#ccffbb' width='370' cellpadding='4'><tr><td> <b> Aceptada. </b> <br>El ancho de la imagen es correcto para el interior. </td></tr></table>" ;	  		  		
		   		  }else{		   		  
		   		  	opener.document.getElementById( 'informa2' ).innerHTML = " <table border='0' bgcolor='red' width='370' cellpadding='4'><tr><td> <b> Cuidado: </b> <br>El ancho de la imagen no es correcto para el interior. </td></tr></table>" ;
		   		  }		        	
		          opener.document.getElementById('informa2').innerHTML =  opener.document.getElementById('informa2').innerHTML+ "<table border='0' width='370' cellpadding='4'><tr><td> <b> Archivo: </b>" + frm.campo1.value + "<br> <b>Ancho: </b>" + carac.ancho + "px <br> <b>Alto: </b>"+ carac.alto +"px <br> <b>Peso: </b>" +$('tamanho').value +" KB <br> <b>Fecha de creacion: </b>" +$('fecha').value +"</td></tr></table>";		          
		          opener.document.getElementById( 'noinfor2' ).style.display = "inline";
				  opener.document.getElementById('informa2').style.display = "inline"; 
				  opener.document.getElementById('noimag2').style.display = "none"; 
				  if(!isNameOk(frm.campo1.value)){
				      	opener.document.getElementById( 'noinfor2' ).innerHTML =  "<table border='0' bgcolor='orange' cellpadding='4' width='370'><tr><td> <b> Cuidado: </b> <br>El Nombre contiene espacios en blanco o caracteres especiales.</td></tr></table>" ;
				}
				if(opener.document.getElementById('eje2')){
				  opener.document.getElementById('eje2').style.display = "none";				  
				}
				
			} 
		}  
			if(opener.document.getElementById('ejep')){ //publicidad
					opener.document.getElementById('ejep').style.display = "none";	
					opener.document.getElementById('informa2').innerHTML =  "<table border='0' width='370' cellpadding='4'><tr><td> <b> Archivo: </b>" + frm.campo1.value + "<br> <b>Ancho: </b>" + carac.ancho + "px <br> <b>Alto: </b>"+ carac.alto +"px <br> <b>Peso: </b>" +$('tamanho').value +" KB <br> <b>Fecha de creacion: </b>" +$('fecha').value +"</td></tr></table>";		          
				  opener.document.getElementById('informa2').style.display = "inline"; 
				  opener.document.getElementById('noimag2').style.display = "none"; 
  		          opener.document.getElementById( 'noinfor2' ).style.display = "inline";
				  if(!isNameOk(frm.campo1.value)){
				      	opener.document.getElementById( 'noinfor2' ).innerHTML =  "<table border='0' bgcolor='orange' cellpadding='4' width='370'><tr><td> <b> Cuidado: </b> <br>El Nombre contiene espacios en blanco o caracteres especiales.</td></tr></table>" ;
					}else{
					   	opener.document.getElementById( 'noinfor2' ).innerHTML =  "<table border='0' bgcolor='#ccffbb' cellpadding='4' width='370'><tr><td> <b> Aceptada: </b> <br> Imagen correcta.</td></tr></table>" ;				
				    }
				}         		
		           		
	           				//eval('opener.document.forms[0].'+campo_retorno+'.value = "' + frm.campo1.value + '";');
		  eval('opener.document.getElementById( \'' + campo_retorno + '\' ).value = "' + frm.campo1.value + '"');
	       					 //eval('opener.document.getElementById(\'preview_\'+campo_retorno).innerHTML = \'<img id="pun" src="<?=$path?>/'+frm.campo1.value+'" border="0" width="200" onload="scale(this);" onerror="this.style.display=\'none\';" />\';');
	      preview_in_opener(frm, campo_retorno);
	      cerrar();
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
		<td style="width: 210px;">
			<label>Vista previa:</label>
		</td>
	</tr>
	<tr>
		<td>
			<div id="files">
	            <select id="de_file" name="de_file" class="campo" size="12" style="width: 210px; overflow:scroll;" onchange="seleccionado(this);">
	    		<?php
	    		    $ficheros = $dialog->get_files();
	    			for($i=0; $i<count($ficheros); $i++) {
	    				echo('<option de:tam="'.round($ficheros[$i]['size']/1024,2).'" de:fecha="'.date("d/m/Y",$ficheros[$i]['mtime']).'" value="'.$ficheros[$i]['relative'].'" de:url="'.$ficheros[$i]['url'].'" >'.$ficheros[$i]['basename'].'</option>');
	    			}
	    		?>
	    		</select>
	        </div>
		</td>
		<td align="right">
        	<div id="preview"></div>
		</td>
	</tr>

	<tr>
		<td colspan="2">
			<input type="text" class="campo" name="campo1" id="campo1" value="" style="width: 100%;" />
				<input type="hidden" class="campo" name="tamanho" id="tamanho" value="" style="width: 100%;" />
				<input type="hidden" class="campo" name="fecha" id="fecha" value="" style="width: 100%;" />
		</td>
	</tr>
	<!-- tr>
		<td align="right" colspan="2">
			<input type="button" class="boton" accesskey="S" onclick="javascript:devolver_campo(this);" value=" Seleccionar " />
            <input type="button" class="boton" accesskey="C" onclick="javascript:cerrar();" value=" Cancelar " />
        </td>
	</tr -->

	<tr>
		<td valign="bottom">

			<!-- div id="uploader">
				<div id="SWFUploadTarget"></div>

				<h4 id="queueinfo"></h4>
				<div id="SWFUploadFileListingFiles"><br /><br /></div>

				<script type="text/javascript" defer="defer">
					function fileCompletedFunction(file) {

						$('formulario').submit();
					}
					var swfu = new SWFUpload({
							upload_script : "<?=RELATIVE_PATH?>upload.php?path=<?=$path?>",
							target : "SWFUploadTarget",
							flash_path : "<?=RELATIVE_PATH?>themes/default/js/SWFUpload/SWFUpload.swf",
							allowed_filesize : 5120,	// 5 MB
							allowed_filetypes : "*.jpg;*.gif;*.png;*.jpeg;*.swf",
							allowed_filetypes_description : "Animaciones Flash o Imágenes ",
							browse_link_innerhtml : "Subir",
							upload_link_innerhtml : "Subir encolados",
							browse_link_class : "swfuploadbtn browsebtn",
							upload_link_class : "swfuploadbtn uploadbtn",
							flash_loaded_callback : 'swfu.flashLoaded',
							upload_file_start_callback : 'uploadFileStart',
							upload_progress_callback : 'uploadProgress',
							/* upload_file_complete_callback : 'uploadFileComplete', */
							upload_file_complete_callback : 'fileCompletedFunction',
							upload_file_cancel_callback : 'uploadFileCancelled',
							upload_error_callback : 'uploadError',
							upload_cancel_callback : 'uploadCancel',
							auto_upload : true,
							debug: true
						});
				</script>
			</div -->

		</td>

		<td align="right" valign="top">
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
