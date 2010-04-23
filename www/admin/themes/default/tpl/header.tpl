<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
<title>..: Panel de Control :..</title>
<meta http-equiv="pragma" content="no-cache" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}admin.css?cacheburst=1259173764"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}style.css"/>
<!--[if IE]>
    <link rel="stylesheet" href="{$params.CSS_DIR}ieadmin.css" type="text/css" />
<![endif]-->


<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}botonera.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightview.css" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}datepicker.css"/>
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}welcomepanel.css?cacheburst=1257955982" />
<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}lightwindow.css" media="screen" />

<link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}messageboard.css" media="screen" />

{if preg_match('/mediamanager\.php/',$smarty.server.SCRIPT_NAME) || preg_match('/mediagraficos\.php/',$smarty.server.SCRIPT_NAME)}
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}mediamanager.css" />
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
{/if}
    
{scriptsection name="head"}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightview.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}prototype-date-extensions.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=effects,dragdrop,controls"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utils.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}fabtabulous.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}control.maxlength.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}datepicker.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightwindow.js"></script>
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}MessageBoard.js"></script>
{/scriptsection}

{if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
    {if $smarty.request.action == 'list_pendientes'}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    {/if}
{/if}
{if preg_match('/pendiente\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
{/if}
{if $smarty.request.action == 'new' || $smarty.request.action == 'read'}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}validation.js"></script>
    {* FIXME: corregir para que pille bien el path *}
    {dhtml_calendar_init src='themes/default/js/jscalendar/calendar.js' setup_src='themes/default/js/jscalendar/calendar-setup.js'
        lang='themes/default/js/jscalendar/lang/calendar-es.js' css='themes/default/js/jscalendar/calendar-win2k-cold-2.css'}
    {if preg_match('/article\.php/',$smarty.server.SCRIPT_NAME)}
        {* <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce.js"></script> *}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}lightview.js"></script>
    {/if}
    {if preg_match('/pendiente\.php/',$smarty.server.SCRIPT_NAME)}        
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
    {/if}
    {if preg_match('/comment\.php/',$smarty.server.SCRIPT_NAME)}
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
    {/if}    
    {if preg_match('/advertisement\.php/',$smarty.server.SCRIPT_NAME)}        
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsadvertisement.js"></script>
    {/if}        
    {if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}        
        <script language="javascript" type="text/javascript" src="{$params.JS_DIR}tiny_mce/tiny_mce_gzip.js"></script>
    {/if}    
{/if}
{if preg_match('/album\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}cropper.js"></script>
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}scriptaculous/scriptaculous.js?load=builder"></script>

    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsalbum.js"></script>

{/if}    
{if preg_match('/category\.php/',$smarty.server.SCRIPT_NAME)}        
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilscategory.js"></script>
    {if $smarty.request.action == 'new' || $smarty.request.action == 'read'}
        <script type="text/javascript" src="{$params.JS_DIR}MiniColorPicker.js"></script>
    {/if}
{/if}
{if preg_match('/poll\.php/',$smarty.server.SCRIPT_NAME)}
<script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilspoll.js"></script>
{/if}    
{if preg_match('/comment\.php/',$smarty.server.SCRIPT_NAME)}
    {if !isset($smarty.post.action) || $smarty.post.action eq "list"}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilscomment.js"></script>
    {/if}
    {if !isset($smarty.post.action) || $smarty.post.action eq "read"}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}lightview.js"></script>
    {/if}
{/if}
{if preg_match('/opinion\.php/',$smarty.server.SCRIPT_NAME)}        
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsopinion.js"></script>
    {/if}    
{if preg_match('/dashboard\.php/',$smarty.server.SCRIPT_NAME)}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}swfobject.js"></script>
    <link rel="stylesheet" type="text/css" href="{$params.CSS_DIR}dashboard.css" />
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

function validateForm(formID)
{
    var checkForm = new Validation(formID, {immediate:true, onSubmit:true});
    if(!checkForm.validate()) {
        if($$('.validation-advice')) {
            if($('warnings-validation')) {
                $('warnings-validation').update('Existen campos sin cumplimentar o errores en el formulario. Por favor, revise todas las pestañas.');
                new Effect.Highlight('warnings-validation');
            }
        }
        return false;
    } else {        
        if($$('.validation-advice') && $('warnings-validation')) {
            $('warnings-validation').setStyle({display: 'none'});
        }
    }
    return true;
}

function sendFormValidate(elto, trg, acc, id, formID)
{
    if(!validateForm(formID))
        return;
       
    enviar(elto, trg, acc, id);
}

function onSearchKeyEnter(e, elto, trg, acc, id)
{
    ekey = (document.all) ? e.keyCode : e.which;
    if (ekey==13)
    {        
        return enviar(elto, trg, acc, id);
    }
}

function preview(elto, trg, acc, id)
{
    this.blur();
    try { UserVoice.PopIn.show(id); return false; }
    catch(e){}

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


function vaciar(elto, id) {
    if(confirm('¿Está seguro de quitar este elemento de la papelera?')) {
        enviar(elto, '_self', 'remove', id);
    }
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

    if ((alguno != 1) && (id != 6)){
        alert("No hay ninguna noticia seleccionada");
    }else{
      if((acc=='mdelete')){
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

function cancel(action,category,page) {
    if(/index_portada/.test(action)) {
        location.href ='index.php';
    }else if(/opinion/.test(action)) {
        location.href ='opinion.php';
    }else if(/advertisement/.test(action)) {
        location.href ='advertisement.php';
    }else{
        location.href= 'article.php?action='+action+'&category='+category+'&page='+page;
    }
}

function change_att_pos(id, position, id2) {
    location.href= 'article.php?action=set_att_position&id='+id+'&position='+position+'&id2='+id2;
}

function change_pos(id, posic, category) {
    location.href= 'article.php?action=set_position&id='+id+'&posicion='+posic+'&category='+category;
}

function alert_frontpage() {
    window.alert("No puede publicar mas de 21 articulos en la Portada!");
}
// -->
</script>


{/literal}

</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0">
{* scriptsection name="body" *}
<script type="text/javascript" src="{$params.JS_DIR}wz_tooltip.js"></script>
{* /scriptsection *}
 
<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%" height="100%">
<tr><td valign="top" align="left"><!-- INICIO: Tabla contenedora -->
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
<table border="0" cellpadding="0" cellspacing="0" align="left" width="100%" height="100%">
{* <tr>
    <td class="barra_superior" style="height:50px;background-image:url(/admin/images/header_middle.png);">&nbsp;&nbsp;{$titulo_barra}</td>
</tr>  *}
<tr>
    <td style="padding:10px;width:100%;" align="left" valign="top">

{if isset($smarty.session.messages) && !empty($smarty.session.messages)}
    {messageboard type="inline"}
{else}
    {messageboard type="growl"}
{/if}
