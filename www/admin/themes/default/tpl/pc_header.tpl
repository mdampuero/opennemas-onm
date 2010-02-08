<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>..: Plan Conecta: Administración :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<link rel="stylesheet" type="text/css" href="{php}echo($this->css_dir);{/php}admin.css"/>
<link rel="stylesheet" type="text/css" href="{php}echo($this->css_dir);{/php}style.css"/>
<!--[if IE]>
    <link rel="stylesheet" href="{php}echo($this->css_dir);{/php}ieadmin.css" type="text/css" />
<![endif]-->
<link rel="stylesheet" type="text/css" href="{php}echo($this->css_dir);{/php}botonera.css"/>

<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}scriptaculous/scriptaculous.js"></script>

{if $smarty.request.action == 'new' || $smarty.request.action == 'read'}
	<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}validation.js"></script>
	<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}fabtabulous.js"></script>
         <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
	
	{dhtml_calendar_init src='themes/default/js/jscalendar/calendar.js' setup_src='themes/default/js/jscalendar/calendar-setup.js'
		lang='themes/default/js/jscalendar/lang/calendar-es.js' css='themes/default/js/jscalendar/calendar-win2k-cold-2.css'}
	
	{if preg_match('/letter\.php/',$smarty.server.SCRIPT_NAME) || preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}
		{* <script language="javascript" type="text/javascript" src="{php}echo($this->js_dir);{/php}tiny_mce/tiny_mce.js"></script> *}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
	{/if}

	<script type="text/javascript" language="javascript" src="{php}echo($this->js_dir);{/php}utilsconecta.js"></script>
	 

{/if}
	

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
//Para list seleccionar todos
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
		alert("No hay ninguna elemento seleccionado");
	}else{
	  if(acc=='mdelete'){ //Eliminar multiple
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
	  }else{ //Publicar o despublicar, 
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
	
	if ((alguno != 1) && (id != 6)){
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

function vaciar(elto, id) {
	if(confirm('¿Está seguro de quitar este elemento de la papelera?')) {
		enviar(elto, '_self', 'remove', id);
	}
}


function pc_cancel(mtype,pc_from,category,page) {
	location.href= pc_from+'.php?action=list&category='+category+'&mytype='+mtype+'&page='+page;
}

function sendFormValidate(elto, trg, acc, id, formID)
{
    var checkForm = new Validation(formID, {immediate:true, onSubmit:true});
    if(!checkForm.validate())
        return;
       
    enviar(elto, trg, acc, id);
}

function change_att_pos(id, position, id2) {
	location.href= 'article.php?action=set_att_position&id='+id+'&position='+position+'&id2='+id2;
}
function change_pos(id, posic, category) {
	location.href= 'article.php?action=set_position&id='+id+'&posicion='+posic+'&category='+category;
}


function confirmar(elto, id) {
	if(confirm('¿Está seguro de querer eliminar este elemento?')) {
		enviar(elto, '_self', 'delete', id);
	}
}

function confirmar_hemeroteca(eleto,category, id) {
    if(confirm('¿Está seguro de enviarlo a hemeroteca?')){
        if(id==0){
            enviar2(eleto, '_self', 'mstatus', 0);
        }else{
            var ruta='article.php?id='+id+'&action=change_status&status=0&category='+category+' ';
            location.href= ruta;
        }
    }
}


function checkAll(field,img)
{
	if(field){
		if( $( img ).getAttribute('status')==0){
			var status=true;
			$( img ).src='/admin/themes/default/images/deselect_button.png';
			$( img ).setAttribute('status','1');
		}else{
			var status=false;
			$( img ).src='/admin/themes/default/images/select_button.png';
			$( img ).setAttribute('status','0');
		}
		if(field.length){
			for (i = 0; i < field.length; i++) {
				$( field[i].id ).checked = status;
			}
		}else{ //Solo hay un elemento a de/seleccionar
			 	$( field ).checked = status;
		}
	}
}


// -->
</script>

{/literal}


</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
<script type="text/javascript" src="{$params.JS_DIR}wz_tooltip.js"></script>
	<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" height="100%">
		<tr><td valign="top" align="left"> <!-- INICIO: Tabla contenedora -->
			<form action="#" method="post" name="formulario" id="formulario" action="{$smarty.server.SCRIPT_NAME}" enctype="multipart/form-data"> 
			<table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
			<tr>
				<td class="barra_superior">&nbsp;&nbsp;{$titulo_barra}&nbsp;<div style="text-align:right; ">{* include file="toolbar.tpl" *}</div></td>
			</tr>
			<tr>
				<td style="padding:10px;" align="left" valign="top">
				
