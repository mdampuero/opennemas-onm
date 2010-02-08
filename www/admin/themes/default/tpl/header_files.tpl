<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="{php}echo($this->css_dir);{/php}admin.css"/>
<link rel="stylesheet" type="text/css" href="{php}echo($this->css_dir);{/php}style.css"/>
<!--[if IE]>
    <link rel="stylesheet" href="{php}echo($this->css_dir);{/php}ieadmin.css" type="text/css" />
<![endif]-->
<link rel="stylesheet" type="text/css" href="{php}echo($this->css_dir);{/php}botonera.css"/>
<link rel="stylesheet" type="text/css" href="{php}echo($this->css_dir);{/php}lightview.css" />
<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}scriptaculous/scriptaculous.js"></script>
<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}utils.js"></script>
<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}fabtabulous.js"></script>

<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}addFiles.js"></script>


{literal}
<script language="javascript">
<!-- //
var objForm = null;
var dialogo = null;
var editores = null;

function enviar(elto, trg, acc, id) {
    var parentEl = elto.parentNode;
	while(parentEl.nodeName != "FORM") {
		parentEl = parentEl.parentNode;
	}

	parentEl.target = trg;
	parentEl.action.value = acc;
	parentEl.id.value = id;

	if(objForm != null) {
		objForm.submit();
	} else {
		parentEl.submit();
	}
}

function sendFormValidate(elto, trg, acc, id, formID)
{
    var checkForm = new Validation(formID, {immediate:true, onSubmit:true});
    if(!checkForm.validate())
        return;
       
    enviar(elto, trg, acc, id);
}


function seleccionar_fichero(nombre_campo, tipo) {
	if(dialogo)
	{
		if(!dialogo.closed) dialogo.close();
	}

	dialogo = window.open('include/dialogo.archivo.php?campo_retorno='+nombre_campo+'&tipo_archivo='+tipo, 'dialogo', 'toolbar=no, location=no, directories=no, status=no, menub ar=no, scrollbar=no, resizable=no, copyhistory=yes, width=410, height=360, left=100, top=100, screenX=100, screenY=100');
	dialogo.focus();
}

//Operaciones multiples.
function enviar2(elto, trg, acc, id) {
	var Lista=document.getElementsByClassName('minput');	
	var arreglo = $A(Lista);
	var alguno=0;	
	arreglo.each(function(el, indice) {
		if(document.getElementById(el.id).checked!=false){	
		  alguno=1;
		}
	});
	if (alguno != 1){
		alert("No hay ninguna noticia seleccionada");
	}else{
	  if(acc=='mdelete'){
	    if(confirm('¿Está seguro de eliminar esos elementos?'))
	    {
			var parentEl = elto.parentNode;
			while(parentEl.nodeName != "FORM") {
				parentEl = parentEl.parentNode;
			}
		
			parentEl.target = trg;
			parentEl.action.value = acc;
			parentEl.id.value = id;
		
			if(objForm != null) {
				objForm.submit();
			} else {
				parentEl.submit();
			}
		}
	  }else{
	       var parentEl = elto.parentNode;
			while(parentEl.nodeName != "FORM") {
				parentEl = parentEl.parentNode;
			}
		
			parentEl.target = trg;
			parentEl.action.value = acc;
			parentEl.id.value = id;
		
			if(objForm != null) {
				objForm.submit();
			} else {
				parentEl.submit();
			}
	  }
	}
}

//Desde papelera litter
function enviar3(elto, trg, acc, id) {
	var Lista=document.getElementsByClassName('minput');	
	var arreglo = $A(Lista);
	var alguno=0;	
	arreglo.each(function(el, indice) {
		if(document.getElementById(el.id).checked!=false){	
		  alguno=1;
		}
	});
	if (alguno != 1){
		alert("No hay ninguna elemento seleccionada");
	}else{
	  if(acc=='mremove'){
	    if(confirm('¿Está seguro de eliminar definitivamente esos elementos?'))
	    { 
			var parentEl = elto.parentNode;
			while(parentEl.nodeName != "FORM") {
				parentEl = parentEl.parentNode;
			}
		
			parentEl.target = trg;
			parentEl.action.value = acc;
			parentEl.id.value = id;
		
			if(objForm != null) {
				objForm.submit();
			} else {
				parentEl.submit();
			}
		}
	  }else{
	       var parentEl = elto.parentNode;
			while(parentEl.nodeName != "FORM") {
				parentEl = parentEl.parentNode;
			}
		
			parentEl.target = trg;
			parentEl.action.value = acc;
			parentEl.id.value = id;
		
			if(objForm != null) {
				objForm.submit();
			} else {
				parentEl.submit();
			}
	  }
	}
}


// -->
</script>


{/literal}


</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" height="100%">
<tr><td valign="top" align="left"><!-- INICIO: Tabla contenedora -->
<!--form action="#" method="post" name="formulario" id="formulario"-->
<form id="formulario" action="#" method="post" name="formulario" enctype="multipart/form-data">
<table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
<tr>
	<td class="barra_superior" style="height:50px;background-image:url(/admin/images/header_middle.png);">&nbsp;&nbsp;{$titulo_barra}</td>
</tr>
<tr>
	<td style="padding:10px;" align="left" valign="top">
