{include file="header.noform.tpl"}

{include file="bulletin.navigation.tpl"}

<link href="{$params.CSS_DIR}bulletin.css" rel="stylesheet" type="text/css" />
<script language="javascript" type="text/javascript" src="{$params.JS_DIR}base64.js"></script>


{* Form to select news and opinions for the bulletin ***************************************************************** *}
{if ($ACTION == 'select') || ($ACTION == 'step0') }
<script language="javascript" type="text/javascript" defer="defer">
var data = {ldelim}{rdelim};
{if empty($smarty.post.data_bulletin)}
data = {ldelim}'news':[], 'opinions':[], 'mailboxes':null{rdelim};
{else}
data = {$smarty.post.data_bulletin|clearslash};
{/if}
{literal}
function loadData() {
    var lista = document.getElementsByTagName('input');
    var items = filterItems();
    var i;
    
    for(i=0; i<lista.length; i++) {
        if( (lista[i].type == 'checkbox') && (items.indexOf(lista[i].value)!=-1)) {
            lista[i].checked = true;
        }
    }
}

function filterItems() {
    var items = new Array();
    var i;
    
    for(i=0; i<data.news.length; i++) {
        items.push(data.news[i].pk_content);
    }

    for(i=0; i<data.opinions.length; i++) {
        items.push(data.opinions[i].pk_content);
    }
    
    return(items);
}

function serializeData() {
    $('data_bulletin').value = Object.toJSON(data);
}

function sgte() {
    // TODO: comprobar longitud de datos
    $('formulario').submit();
}
</script>
<style>
#container-noticias {
    width: 100%; 
}

#container-noticias h2 {
    font-size: 14px;
}

#container-opiniones {;
    width: 100%;
}

.listado a {
    font-weight: bold;
    font-size: 14px;
}

.listado h2 {
    margin-bottom: 0;
}

.listado a:hover {
    font-weight: bold;    
    font-size: 14px;
}

.listado table {
    margin-left: 10px;
}
</style>
{/literal}

<div id="container">    

<fieldset>
    <legend>Boletines Archivados: </legend>
    <form id="formulario_archivo" name="formulario_archivo" action="{$smarty.server.SCRIPT_NAME}" method="POST">
        <p align="left">
            <img src="{$params.IMAGE_DIR}icon_info.gif" border="0" align="absmiddle" />
            Seleccione un boletín archivado por fecha y pulse en Restaurar para cargar sus noticias y opiniones.
        </p>
        <label for="archivos">Seleccionar por Fecha: </label>
        <select name="archivos" id="archivos">
        {section loop=$archivos name=archs}
            <option value="{$archivos[archs]->id}"> {$archivos[archs]->created|date_format:"%d/%m/%Y - %H:%M:%S"} </option>
        {/section}
        </select>
        <input type="image" src="{$params.IMAGE_DIR}btn_restaurar.gif" alt="Cargar archivo" />
        <input type="hidden" name="action" value="step1" />
    </form>
</fieldset>

<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}" method="POST">    

    <p align="justify">
        <img src="{$params.IMAGE_DIR}icon_info.gif" border="0" align="absmiddle" />
        Seleccione del siguiente listado las noticias y opiniones que desea incluir en el bolet&iacute;n, y
        pulse siguiente para continuar.
        <div id="botones">   
            <a href="#" onclick="sgte();return false;" class="enlace" title="Paso siguiente (Opiniones)">
                <img src="{$params.IMAGE_DIR}btn_sgte.gif" border="0" alt="" align="bottom" /></a>
        </div>
    </p>

    <div id="container-noticias">
        <fieldset>
        <legend>Noticias por categorias: </legend>
    
        {* section name=n loop=$articles *}
        {foreach key=k item=articles from=$articles_agrupados name=arts}
	<a name="a_arts{$smarty.foreach.arts.iteration}"></a>
	<h2><img src="{$params.IMAGE_DIR}iconos/tree_close.gif" border="0" align="top" />
	<a onclick="new Effect.toggle($('arts{$smarty.foreach.arts.iteration}'),'blind');" style="cursor:pointer;">{$k}</a></h2>

	<div id="arts{$smarty.foreach.arts.iteration}" style="display:none;">
	<hr>
                <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
                <tbody>
                    {section name=n loop=$articles}
                    <tr bgcolor="{cycle values="#FFFFFF,#EEEEEE"}">
                        <td>{$articles[n]->title|clearslash}</td>
                        <td align="right"><input type="checkbox" name="articles[]" value="{$articles[n]->id}" /></td>
                    </tr>
                    {/section}
                </tbody>
                </table>
	</div>
        {/foreach}
        <br class="clearer" />
        </fieldset>
    </div>
    <div id="container-opiniones">
        <fieldset>
        <legend>Opiniones</legend>
	<img src="{$params.IMAGE_DIR}iconos/tree_close.gif" border="0" align="top" />
	<a onclick="new Effect.toggle('divOpinions','blind');" style="cursor:pointer;"><b>Semana {php}echo date('W');{/php} y anteriores</b></a>
	<div id="divOpinions" style="display:none;">
	<hr>
		<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
		<tbody>
		  <tr>
		    <th align="left">T&iacute;tulo</th>
		    <th align="center">Semana</th>
		    <th align="center">Seleccionado</th>
		  </tr>
		{section name=o loop=$opinions}
		  <tr bgcolor="{cycle values="#FFFFFF,#EEEEEE"}">
		    <td align="left">{$opinions[o]->title|clearslash}</td>
		    <td align="center">{$opinions[o]->archive-12}</td>
		    <td align="center"><input type="checkbox" name="opinions[]" value="{$opinions[o]->id}" /></td>
		  </tr>
		{/section}
		</tbody>
		</table>
	</div>

        </fieldset>
    </div>    
    <br class="clearer" />
    
    <div id="botones">   
        <a href="#" onclick="sgte();return false;" class="enlace" title="Paso siguiente (Opiniones)">
            <img src="{$params.IMAGE_DIR}btn_sgte.gif" border="0" alt="" align="bottom" /></a>
    </div>

    <input type="hidden" id="data_bulletin" name="data_bulletin" value="{$smarty.post.data_bulletin|default:''}" />
    <input type="hidden" id="action" name="action" value="step1" /> {* Next step *}
</form>

<hr style="margin: 40px 0;"/>



<form action="{$smarty.server.SCRIPT_NAME}" method="post" id="bulletin_wizard">
    <textarea id="data_bulletin" name="data_bulletin">{$smarty.post.data_bulletin|default:''}</textarea>
    <input type="hidden" name="action" id="action" value="news" /> {* Next step *}
</form>

{* include file="bulletin.navigation.tpl" *}
</div> {* DIV#container *}
<script language="javascript" defer="defer" type="text/javascript">
loadData();
</script>
{/if}

{* Form to load archives ********************************************************************************************* *}
{if ($ACTION == 'archive_list')}
<div id="container">    
<fieldset>
    <legend>Boletiones Archivados: </legend>
    <form id="formulario_archivo" name="formulario_archivo" action="{$smarty.server.SCRIPT_NAME}" method="POST">
        <p align="left">
            <img src="{$params.IMAGE_DIR}icon_info.gif" border="0" align="absmiddle" />
            Seleccione un boletín archivado por fecha y pulse en Restaurar para cargar sus noticias y opiniones.
        </p>
        <label for="archivos">Seleccionar por Fecha: </label>
        <select name="archivos" id="archivos">
        {section loop=$archivos name=archs}
            <option value="{$archivos[archs]->id}"> {$archivos[archs]->created|date_format:"%d/%m/%Y - %H:%M:%S"} </option>
        {/section}
        </select>
        <input type="image" src="{$params.IMAGE_DIR}btn_restaurar.gif" alt="Cargar archivo" />
        <input type="hidden" name="action" value="step1" />
    </form>
</fieldset>

<div id="botones">   
    <a href="#" onclick="history.back();return false;" class="enlace" title="Volver al paso anterior">
        <img src="{$params.IMAGE_DIR}btn_volver.gif" border="0" alt="" align="bottom" /></a>
</div>
</div>
{/if}



{* Form to add news to the bulletin ********************************************************************************** *}
{if ($ACTION == 'news') || ($ACTION == 'step1') }
<script language="javascript" type="text/javascript" defer="defer">
var data = {ldelim}{rdelim};
{if empty($smarty.post.data_bulletin)}
data = {ldelim}'news':[], 'opinions':[], 'mailboxes':null{rdelim};
{else}
data = {$smarty.post.data_bulletin|clearslash};
{/if}
{literal}

// {titulo:'titulo', agencia:'agencia', descripcion: 'descripcion', cuerpo: 'cuerpo'}
/* data.news      = new Array();
data.opinions  = new Array();
data.mailboxes = null; */

var tpl_src = '<li>' +
        '<div class="titulo">#{titulo}</div>' +
		'<div class="operaciones"><a href="#" onclick="javascript:readNew(#{id});">' + {/literal}
		'<img src="{$params.IMAGE_DIR}bulletin/editar.gif" border="0" align="absmiddle" /></a>&nbsp;' + {literal}
		'<a href="#" onclick="javascript:deleteNew(#{id});">' + {/literal}
		'<img src="{$params.IMAGE_DIR}bulletin/eliminar.gif" border="0" align="absmiddle" /></a>' +
        '<a href="#noticias_anchor" onclick="javascript:getUp(this);" title="Subir noticia">' +
        '<img src="{$params.IMAGE_DIR}bulletin/arrow_up.gif" border="0" align="absmiddle" /></a>' +
        '<a href="#noticias_anchor" onclick="javascript:getDown(this);" title="Bajar noticia">' +
        '<img src="{$params.IMAGE_DIR}bulletin/arrow_down.gif" border="0" align="absmiddle" /></a></div>' +        
		'<br class="salto" />' + {literal}
		'<div class="agencia">#{agencia}</div>' +
		'<div class="descripcion">#{subtitulo}</div><br class="clearer" />' +
		'<br class="clearer" />' +
        '</li>';

// Template
var templ = new Template(tpl_src);

function loadData(data, container){
    var tmpLi = null;
    $(container).innerHTML = '';
    data.each( function(conv){
        obj = {'id':conv.id, 'titulo': Base64.decode(conv.titulo), 'agencia':Base64.decode(conv.agencia),
        'subtitulo':Base64.decode(conv.subtitulo)}
        $(container).innerHTML += templ.evaluate(obj);
    });
}

function inData(obj, arr) {
    for(var i in arr) {
        if(arr[i].pk_content == obj.pk_content) {
            return(true);
        }
    }
    
    return( false );
}

function getArticle(id) {    
    $('message_getArticle').innerHTML = '<div align="center"><img src="themes/default/images/loading.gif" border="0" align="absmiddle" /> Recuperando datos del servidor</div>';
    new Ajax.Request('./bulletin.php', {
        method:'post',
        parameters: {'id': id, 'action': 'getArticle'},
        onSuccess: function(xhr){
          var response = xhr.responseText || '';
          if(response != '') {
            eval('var obj = ' + response);
            
            if(!inData(obj, data.news)) {
                data.news.push(obj);
                reallocate();
                
                loadData(data.news, 'noticias');
                serializeData();
                
                $('message_getArticle').innerHTML =  '';
            } else {
                $('message_getArticle').innerHTML =  '<div style="color: #F00; margin: 20px; text-align=center;">La noticia ya está en el boletín</div>';
            }
          }
        },
        onFailure: function(){
            $('message_getArticle').innerHTML =  '';
        }
      });
}


function reset(nameForm) {
    $(nameForm).reset();
    $('id').value = '';
    $('pk_content').value = '';
}

function validar() {
    var isOK = true;

    if( $F('titulo').strip().length == '' ) {
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'blur', true, true);
        $('titulo').dispatchEvent(evObj);

        //$('titulo').onblur();
        isOK = false;
    }

    if( $F('agencia').strip().length == '' ) {
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'blur', true, true);
        $('agencia').dispatchEvent(evObj);

        //$('titulo').onblur();
        isOK = false;
    }

    if( $F('subtitulo').strip().length == '' ) {
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'blur', true, true);
        $('subtitulo').dispatchEvent(evObj);

        //$('titulo').onblur();
        isOK = false;
    }

    return(isOK);
}

function addNew(type) {
    if(!validar()) {
        return(0);
    }

    if(($F('id')!='')&&(data.news[$F('id')])) {
        updateNew();
        reset('formulario');
        return(0);
    }

    var tmp = {'id':data.news.length, 'titulo': Base64.encode($F('titulo')), 'agencia':Base64.encode($F('agencia')),
        'subtitulo':Base64.encode($F('subtitulo')), 'pk_content': $F('pk_content')};
    data.news[data.news.length] = tmp;

    loadData(data.news, type);
    //reset('formulario');

    new Effect.Highlight($('noticias').childNodes[$('noticias').childNodes.length-1]);

    serializeData();

    reset('formulario');
}

function readNew(id) {
    // Limpar formulario
    reset('formulario');

    $('id').value = id;
    var i = id;
    $('titulo').value = Base64.decode(data.news[i].titulo);
    $('agencia').value = Base64.decode(data.news[i].agencia);
    $('subtitulo').value = Base64.decode(data.news[i].subtitulo);
    $('pk_content').value = data.news[i].pk_content;
}

function updateNew() {
    var i= $('id').value;
    data.news[i].titulo = Base64.encode( $('titulo').value );
    data.news[i].agencia = Base64.encode($('agencia').value);
    data.news[i].subtitulo = Base64.encode($('subtitulo').value);
    data.news[i].pk_content = $('pk_content').value;

    reset('formulario');
    loadData(data.news, 'noticias');

    serializeData();
}

function deleteNew(id) {
    if(!confirm('¿Está seguro de querer eliminar esta noticia?')) {
        return(0);
    }

    // Método artesanal
    var tmpArr = new Array();
    for(var i=0,j=0;i<data.news.length;i++) {
        if(i!==id) {
            tmpArr[j] = data.news[i];
            j++;
        }
    }

    data.news = new Array();
    for(var i=0;i<tmpArr.length; i++) {
        data.news[i] = tmpArr[i];
        data.news[i].id = i;
    }

    loadData(data.news, 'noticias');

    serializeData();
}

function serializeData() {
    $('data_bulletin').value = Object.toJSON(data);
}

function sgte() {
    if(data.news.length <= 0) {
        alert('Introduzca por lo menos una noticia antes de continuar con el siguiente paso.');
    } else {
        $('bulletin_wizard').submit();
    }
}

function transferData(elto) {
    eval('var d = ' + elto.getAttribute('data'));

    $('titulo').value = Base64.decode( d.titulo );
    $('agencia').value = Base64.decode( d.agencia );
    $('subtitulo').value = Base64.decode( d.subtitulo );
    $('pk_content').value = d.pk_content;

    // Esconder pop-up
    Element.hide('newsLoaderContainer');
}

function newsLoader() {
    Element.toggle('newsLoaderContainer');
}

function requestQ() { // Buscando noticias
    if($('query').value.length > 2) {
        $('newsLoaderList').innerHTML = '<div align="center"><img src="themes/default/images/loading.gif" border="0" /></div>';
        new Ajax.Request('./bulletin.php', {
            method:'post',
            parameters: {'query': $('query').value, 'action': 'searchNew'},
            onSuccess: function(xhr){
              var response = xhr.responseText || '';
              $('newsLoaderList').innerHTML = response;
            },
            onFailure: function(){ $('newsLoaderList').innerHTML = ''; }
          });
        //new Ajax.Updater('newsLoaderList', 'bulletin.php', {parameters: { 'query' : $('query').value } });
    } else {
        $('newsLoaderList').innerHTML = '';
    }
}

function requestA() { // Buscando en el archivo
    if($('queryA').value.length == 10) {
        $('archiveLoaderList').innerHTML = '<div align="center"><img src="themes/default/images/loading.gif" border="0" /></div>';
        new Ajax.Request('./bulletin.php', {
            method:'post',
            parameters: {'queryA': $('queryA').value, 'action': 'searchArchive'},
            onSuccess: function(xhr){
              var response = xhr.responseText || '';
              $('archiveLoaderList').innerHTML = response;
            },
            onFailure: function(){ $('archiveLoaderList').innerHTML = ''; }
          });
        //new Ajax.Updater('newsLoaderList', 'bulletin.php', {parameters: { 'query' : $('query').value } });
    } else {
        $('archiveLoaderList').innerHTML = '';
    }
}

function archiveLoader() {
    Element.toggle('archiveLoaderContainer');
}

function transferDataA(elto) { // Pasando datos del archivo
    eval('var d = ' + Base64.decode( elto.getAttribute('data')) );
    data = d;

    loadData(data.news, 'noticias'); // cargar las noticias
    serializeData();

    // Esconder pop-up
    Element.hide('archiveLoaderContainer');
}

// Recuperar os elementos que son fillos da lista, pásase como argumento un irmán
function getListItems(elto) {
    var parentUl = elto.parentNode;
    var eltos = parentUl.getElementsByTagName('LI');
    var output = new Array();
    
    for(var i=0; i<eltos.length; i++) {
        if(eltos[i].parentNode == parentUl) {
            output.push( eltos[i] );
        }
    }
    
    return( output );
}

// Recoloca o elemento unha posición para enriba, OLLO: si é posible
function getUp(elto) {
    var parentLi = elto.parentNode;
    var list = null;
    var tempElto = null;
    
    while(parentLi.nodeName != 'LI') {
        parentLi = parentLi.parentNode;
    }
    
    list = getListItems(parentLi);
    
    for(var i=1; i<list.length; i++) {
        if(list[i] == parentLi) {
            tempElto = data.news[i-1];
            data.news[i-1] = data.news[i];
            data.news[i] = tempElto;
            
            reallocate();
            
            loadData(data.news, 'noticias'); // cargar las noticias
            serializeData();
            
            break;
        }
    }
}

// Recoloca o elemento unha posición para abaixo, OLLO: si é posible
function getDown(elto) {
    var parentLi = elto.parentNode;
    var list = null;
    var tempElto = null;
    
    while(parentLi.nodeName != 'LI') {
        parentLi = parentLi.parentNode;
    }
    
    list = getListItems(parentLi);
    
    for(var i=1; i<list.length; i++) {
        if(list[i-1] == parentLi) {
            tempElto = data.news[i-1];
            data.news[i-1] = data.news[i];
            data.news[i] = tempElto;
            
            reallocate();
            
            loadData(data.news, 'noticias'); // cargar las noticias
            serializeData();
            
            break;
        }
    }
}

function reallocate() {
    for(var idx=0; idx<data.news.length; idx++) {
        data.news[idx].id = idx;
    }
}
</script>
<style>
#newsLoaderList {
    list-style: none;
    padding: 0;
    margin: 0;
}

#newsLoaderList li {
    padding: 4px 0 0 4px;
    margin: 2px 0;
    background-color: #d0d0d0;
    list-style: none;
    cursor:hand, pointer;
}

#newsLoaderList li:hover {
    background-color: #fff transparent;
    color: #666;
    text-decoration: underline;
    padding: 4px 0 0 4px;
}

#contenedor-articulos {
    float: left;
    width: 350px;
    
    position: absolute;
    top: 65px;
    left: 800px;
}

.listado a {
    font-size: 12px;
}

.listado a:hover {
    font-size: 12px;
}

.listado h2 a {
    font-weight: bold;
    font-size: 14px;
}

.listado h2 {
    margin-bottom: 0;
    text-decoration: none;
}

.listado h2 a:hover {
    font-weight: bold;    
    font-size: 14px;
}

.listado table {
    margin-left: 10px;
}

#container {
    float: left;
}
</style>
{/literal}
<div id="container">

<div id="message_getArticle"></div>

<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600" summary="Formulario de envío masivo de noticias">
<tbody>
<tr>
    <td align="right" valign="top"><label for="titulo">Titular:</label></td>
    <td>
        <input type="text" name="titulo" id="titulo" size="60" maxlength="250" class="required" style="width:680px;" />
        <input type="hidden" name="id" id="id" value="" /><input type="hidden" name="pk_content" id="pk_content" value="" />
    </td>
</tr>

<tr>
    <td align="right" valign="top"><label for="agencia">Localizaci&oacute;n:</label></td>
    <td><input type="text" name="agencia" id="agencia" size="60" maxlength="120" class="required" /></td>
</tr>

<tr>
    <td align="right" valign="top"><label for="subtitulo">Subtitulo:</label></td>
    <td><textarea name="subtitulo" id="subtitulo" size="60" maxlength="500" style="width:680px;height:80px;"></textarea></td>
</tr>

<tr>
    <td colspan="2" align="right">
        <a href="javascript:;" onclick="addNew('noticias');" class="enlace">
        <img src="{$params.IMAGE_DIR}bulletin/add.gif" border="0" alt="" align="absmiddle" />
        A&ntilde;adir noticia</a></td>
</tr>

</tbody>
</table>
</form>

<br /><br />
<div id="container-noticias">
    <fieldset>
    <legend>Noticias por categorias: </legend>
    <a name="noticias_anchor"></a>
    <ul id="noticias">

    </ul>
    </fieldset>
</div>

<div id="botones">
    <a onclick="sgte();" class="enlace" title="Paso siguiente (Opiniones)">
        <img src="{$params.IMAGE_DIR}btn_sgte.gif" border="0" alt="" align="bottom" /></a>
</div>

<form action="{$smarty.server.SCRIPT_NAME}" method="post" id="bulletin_wizard">
    <textarea id="data_bulletin" name="data_bulletin">{$smarty.post.data_bulletin|default:''}</textarea>

    <input type="hidden" name="action" id="action" value="opinions" /> {* Next step *}
</form>

{include file="bulletin.navigation.tpl"}
</div> {* DIV#container *}

<div id="contenedor-articulos">
    <fieldset>
    <legend>Noticias por categorias:</legend>

    {foreach key=k item=articles from=$articles_agrupados name=arts}
        <div style="width: 350px; float:left;" class="listado">
            <a name="a_arts{$smarty.foreach.arts.iteration}"></a>
            <h2><img src="{$params.IMAGE_DIR}iconos/tree_close.gif" border="0" align="top" />
            <a href="#a_arts{$smarty.foreach.arts.iteration}" onclick="Element.toggle('arts'+{$smarty.foreach.arts.iteration});">{$k}</a></h2>
            <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="335"
                id="arts{$smarty.foreach.arts.iteration}" style="display:{if $smarty.foreach.arts.iteration != 1}none{else}{/if};">
            <tbody>
                {section name=n loop=$articles}
                <tr bgcolor="{cycle values="#FFFFFF,#EEEEEE"}">
                    <td><a href="#" onclick="javascript:getArticle({$articles[n]->id});">{$articles[n]->title|clearslash}</a></td>
                </tr>
                {/section}
            </tbody>
            </table>
        </div>
    {/foreach}

    </fieldset>
</div>

<script language="javascript" defer="defer" type="text/javascript">
loadData(data.news, 'noticias');
</script>
{/if}


{* FORM OPINIONS ********************************************************************************** *}
{if ($ACTION == 'opinions') || ($ACTION == 'step2') }
<script language="javascript" type="text/javascript" defer="defer">
var data = {ldelim}{rdelim};
{if empty($smarty.post.data_bulletin)}
data = {ldelim}'news':[], 'opinions':[], 'mailboxes':null{rdelim};
{else}
data = {$smarty.post.data_bulletin|clearslash};
{/if}
{literal}

// {titulo:'titulo', agencia:'agencia', descripcion: 'descripcion', cuerpo: 'cuerpo'}
/* data.news      = new Array();
data.opinions  = new Array();
data.mailboxes = null; */

var tpl_src = '<li>' +
        '<div class="titulo">#{titulo}</div>' +
		'<div class="operaciones"><a href="#" onclick="javascript:readNew(#{id});">' + {/literal}
		'<img src="{$params.IMAGE_DIR}bulletin/editar.gif" border="0" align="absmiddle" /></a>&nbsp;' + {literal}
		'<a href="#" onclick="javascript:deleteNew(#{id});">' + {/literal}
		'<img src="{$params.IMAGE_DIR}bulletin/eliminar.gif" border="0" align="absmiddle" /></a>' +
        '<a href="#opiniones_anchor" onclick="javascript:getUp(this);" title="Subir noticia">' +
        '<img src="{$params.IMAGE_DIR}bulletin/arrow_up.gif" border="0" align="absmiddle" /></a>' +
        '<a href="#opiniones_anchor" onclick="javascript:getDown(this);" title="Bajar noticia">' +
        '<img src="{$params.IMAGE_DIR}bulletin/arrow_down.gif" border="0" align="absmiddle" /></a></div>' +        
		'<br class="salto" />' + {literal}
		'<div class="agencia">#{agencia}</div>' +
		'<br class="clearer" />' +
        '</li>';

// Template
var templ = new Template(tpl_src);

function loadData(data, container){
    var tmpLi = null;
    $(container).innerHTML = '';
    data.each( function(conv){
        obj = {'id':conv.id, 'titulo': Base64.decode(conv.titulo), 'agencia':Base64.decode(conv.agencia)}
        $(container).innerHTML += templ.evaluate(obj);
    });
}

function inData(obj, arr) {
    for(var i in arr) {
        if(arr[i].pk_content == obj.pk_content) {
            return(true);
        }
    }
    
    return( false );
}

function getOpinion(id) {    
    $('message_getOpinion').innerHTML = '<div align="center"><img src="themes/default/images/loading.gif" border="0" align="absmiddle" /> Recuperando datos del servidor</div>';
    new Ajax.Request('./bulletin.php', {
        method:'post',
        parameters: {'id': id, 'action': 'getOpinion'},
        onSuccess: function(xhr){
          var response = xhr.responseText || '';
          if(response != '') {
            eval('var obj = ' + response);
            
            if(!inData(obj, data.opinions)) {
                data.opinions.push(obj);
                reallocate();
                
                loadData(data.opinions, 'opiniones');
                serializeData();
                
                $('message_getOpinion').innerHTML =  '';
            } else {
                $('message_getOpinion').innerHTML =  '<div style="color: #F00; margin: 20px; text-align=center;">La opinión ya está en el boletín</div>';
            }
          }
        },
        onFailure: function(){
            $('message_getOpinion').innerHTML =  '';
        }
      });
}

function reset(nameForm) {
    $(nameForm).reset();
    $('id').value = '';
    $('pk_content').value = '';
}

function validar() {
    var isOK = true;

    if( $F('titulo').strip().length == '' ) {
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'blur', true, true);
        $('titulo').dispatchEvent(evObj);

        //$('titulo').onblur();
        isOK = false;
    }

    if( $F('agencia').strip().length == '' ) {
        var evObj = document.createEvent('HTMLEvents');
        evObj.initEvent( 'blur', true, true);
        $('agencia').dispatchEvent(evObj);

        //$('titulo').onblur();
        isOK = false;
    }

    return(isOK);
}

function addNew(type) {
    if(!validar()) {
        return(0);
    }

    if(($F('id')!='')&&(data.opinions[$F('id')])) {
        updateNew();
        reset('formulario');
        return(0);
    }

    var tmp = {'id':data.opinions.length, 'titulo': Base64.encode($F('titulo')),
               'agencia':Base64.encode($F('agencia')),'pk_content': $F('pk_content')};
    data.opinions[data.opinions.length] = tmp;

    loadData(data.opinions, type);
    //reset('formulario');

    new Effect.Highlight($('opiniones').childNodes[$('opiniones').childNodes.length-1]);

    serializeData();

    reset('formulario');
}

function readNew(id) {
    // Limpar formulario
    reset('formulario');

    $('id').value = id;
    var i = id;
    $('titulo').value = Base64.decode(data.opinions[i].titulo);
    $('agencia').value = Base64.decode(data.opinions[i].agencia);
    $('pk_content').value = data.opinions[i].pk_content;
}

function updateNew() {
    var i= $('id').value;
    data.opinions[i].titulo = Base64.encode( $('titulo').value );
    data.opinions[i].agencia = Base64.encode($('agencia').value);
    data.opinions[i].pk_content = $('pk_content').value;

    reset('formulario');
    loadData(data.opinions, 'opiniones');

    serializeData();
}

function deleteNew(id) {
    if(!confirm('¿Está seguro de querer eliminar esta opinion?')) {
        return(0);
    }

    // Método artesanal
    var tmpArr = new Array();
    for(var i=0,j=0;i<data.opinions.length;i++) {
        if(i!==id) {
            tmpArr[j] = data.opinions[i];
            j++;
        }
    }

    data.opinions = new Array();
    for(var i=0;i<tmpArr.length; i++) {
        data.opinions[i] = tmpArr[i];
        data.opinions[i].id = i;
    }

    loadData(data.opinions, 'opiniones');

    serializeData();
}

function serializeData() {
    $('data_bulletin').value = Object.toJSON(data);
}

function sgte() {
    $('bulletin_wizard').submit();
}

function previo() {
    $('action').value = 'news';    
    $('bulletin_wizard').submit();
}

function transferData(elto) {
    eval('var d = ' + elto.getAttribute('data'));

    $('titulo').value = Base64.decode( d.titulo );
    $('agencia').value = Base64.decode( d.agencia );
    $('pk_content').value = d.pk_content;

    // Esconder pop-up
    Element.hide('newsLoaderContainer');
}

function newsLoader() {
    Element.toggle('newsLoaderContainer');
}

function requestQ() {
    if($('query').value.length > 2) {
        $('newsLoaderList').innerHTML = '<div align="center"><img src="themes/default/images/loading.gif" border="0" /></div>';
        new Ajax.Request('./bulletin.php', {
            method:'post',
            parameters: {'query': $('query').value, 'action': 'searchNew'},
            onSuccess: function(xhr){
              var response = xhr.responseText || '';
              $('newsLoaderList').innerHTML = response;
            },
            onFailure: function(){ $('newsLoaderList').innerHTML = ''; }
          });
    } else {
        $('newsLoaderList').innerHTML = '';
    }
}

// Recuperar os elementos que son fillos da lista, pásase como argumento un irmán
function getListItems(elto) {
    var parentUl = elto.parentNode;
    var eltos = parentUl.getElementsByTagName('LI');
    var output = new Array();
    
    for(var i=0; i<eltos.length; i++) {
        if(eltos[i].parentNode == parentUl) {
            output.push( eltos[i] );
        }
    }
    
    return( output );
}

// Recoloca o elemento unha posición para enriba, OLLO: si é posible
function getUp(elto) {
    var parentLi = elto.parentNode;
    var list = null;
    var tempElto = null;
    
    while(parentLi.nodeName != 'LI') {
        parentLi = parentLi.parentNode;
    }
    
    list = getListItems(parentLi);
    
    for(var i=1; i<list.length; i++) {
        if(list[i] == parentLi) {
            tempElto = data.opinions[i-1];
            data.opinions[i-1] = data.opinions[i];
            data.opinions[i] = tempElto;
            
            reallocate();
            
            loadData(data.opinions, 'opiniones'); // cargar las noticias
            serializeData();
            
            break;
        }
    }
}

// Recoloca o elemento unha posición para abaixo, OLLO: si é posible
function getDown(elto) {
    var parentLi = elto.parentNode;
    var list = null;
    var tempElto = null;
    
    while(parentLi.nodeName != 'LI') {
        parentLi = parentLi.parentNode;
    }
    
    list = getListItems(parentLi);
    
    for(var i=1; i<list.length; i++) {
        if(list[i-1] == parentLi) {
            tempElto = data.opinions[i-1];
            data.opinions[i-1] = data.opinions[i];
            data.opinions[i] = tempElto;
            
            reallocate();
            
            loadData(data.opinions, 'opiniones'); // cargar las noticias
            serializeData();
            
            break;
        }
    }
}

function reallocate() {
    for(var idx=0; idx<data.opinions.length; idx++) {
        data.opinions[idx].id = idx;
    }
}
</script>
<style>
#newsLoaderList {
    list-style: none;
    padding: 0;
    margin: 0;
}

#newsLoaderList li {
    padding: 4px 0 0 4px;
    margin: 2px 0;
    background-color: #d0d0d0;
    list-style: none;
    cursor:hand, pointer;
}

#newsLoaderList li:hover {
    background-color: #fff transparent;
    color: #666;
    text-decoration: underline;
    padding: 4px 0 0 4px;
}

#contenedor-opiniones {
    float: left;
    width: 350px;
    
    position: absolute;
    top: 65px;
    left: 800px;
}

.listado a {
    font-weight: bold;
    font-size: 12px;
}

.listado a:hover {
    font-weight: bold;    
    font-size: 12px;
}

#container {
    float: left;
}
</style>
{/literal}
<div id="container">
    
<div id="message_getOpinion"></div>

{* Opiniones *}
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}">
<table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="600" summary="Formulario de envío masivo de noticias">
<tbody>
<tr>
    <td align="right" valign="top"><label for="titulo">Título:</label></td>
    <td>
        <input type="text" name="titulo" id="titulo" size="60" maxlength="250" class="required" style="width:630px;" />
        <input type="hidden" name="id" id="id" value="" /><input type="hidden" name="pk_content" id="pk_content" value="" />
    </td>
</tr>

<tr>
    <td align="right" valign="top"><label for="agencia" style="white-space: nowrap;">Nombre del autor:</label></td>
    <td><input type="text" name="agencia" id="agencia" size="60" maxlength="120" class="required" /></td>
</tr>

<!--<tr>
    <td align="right" valign="top"><label for="descripcion">Entradilla:</label></td>
    <td><textarea name="descripcion" id="descripcion" size="60" maxlength="500" style="width:630px;height:80px;"></textarea></td>
</tr>-->

<tr>
    <td colspan="2" align="right">
        <a href="javascript:;" onclick="addNew('opiniones');" class="enlace" title="Añadir opinión al boletín">
        <img src="{$params.IMAGE_DIR}bulletin/add.gif" border="0" alt="" align="absmiddle" />
        A&ntilde;adir opinión</a></td>
</tr>

</tbody>
</table>
</form>

<br /><br />
<div id="container-noticias">
    <fieldset>
    <legend>Opiniones: </legend>
    <a name="opiniones_anchor"></a>
    <ul id="opiniones">

    </ul>
    </fieldset>
</div>

<div id="botones">   
    <a href="#" onclick="previo();return false;" class="enlace" title="Paso anterior (Noticias)">
        <img src="{$params.IMAGE_DIR}btn_prev.gif" border="0" alt="" align="bottom" /></a>
    <a href="#" onclick="sgte();return false;" class="enlace" title="Paso siguiente (Opiniones)">
        <img src="{$params.IMAGE_DIR}btn_sgte.gif" border="0" alt="" align="bottom" /></a>
</div>

<form action="{$smarty.server.SCRIPT_NAME}" method="post" id="bulletin_wizard">
    <textarea id="data_bulletin" name="data_bulletin">{$smarty.post.data_bulletin|default:''|clearslash}</textarea>
    <input type="hidden" name="action" id="action" value="mailboxes" /> {* Next step *}
</form>

{include file="bulletin.navigation.tpl"}
</div> {* DIV#container *}

<div id="contenedor-opiniones">
    <fieldset>
    <legend>Opiniones: </legend>

    <div class="listado">
    {section name=o loop=$opinions}
    <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="100%">
    <tbody>
    <tr bgcolor="{cycle values="#FFFFFF,#EEEEEE"}">
        <td>
            <a href="#" onclick="javascript:getOpinion({$opinions[o]->id});">{$opinions[o]->title|clearslash}</a>
        </td>        
    </tr>    
    </tbody>
    </table>            
    {/section}
    </div>

    </fieldset>
</div> 

<script language="javascript" defer="defer" type="text/javascript">
loadData(data.opinions, 'opiniones');
</script>
{/if}


{* FORM MAILBOXES ********************************************************************************** *}
{if ($ACTION == 'mailboxes') || ($ACTION == 'step3') }
<script language="javascript" type="text/javascript" defer="defer">
var data = {ldelim}{rdelim};
{if empty($smarty.post.data_bulletin)}
data = {ldelim}'news':[], 'opinions':[], 'mailboxes':null{rdelim};
{else}
data = {$smarty.post.data_bulletin|clearslash};
{/if}
{literal}
function saveMailboxes(textarea,limit) {
    var val=textarea.value.replace(/\r/g,'').split('\n');
    rest = val.length - limit;
    
    if(val.length>limit){
       alert('No se pueden introducir mas de 1500 direcciones de email ');
       textarea.value=val.slice(0,-rest).join('\n')
    }

    data.mailboxes = Base64.encode($F('destinatarios'));
    serializeData();
}

function serializeData() {
    $('data_bulletin').value = Object.toJSON(data);
}

function sgte() {
  $('bulletin_wizard').submit();
	
}

function previo() {
    $('action').value = 'opinions';    
    $('bulletin_wizard').submit();
}
</script>
{/literal}
<div id="container">
<form id="formulario" name="formulario" action="{$smarty.server.SCRIPT_NAME}">
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="fuente_cuerpo" summary="Formulario de envío masivo de noticias">
<tbody>
    <tr>
		<td align="left" colspan=2 valign="top"><label for="destinatarios">Destinatarios separados por comas, espacios o en diferentes lineas <sup>(*)</sup>:</label>
		<textarea type="text" name="destinatarios" id="destinatarios" style="width: 100%; height: 350px;"
		class="required" onblur="saveMailboxes(this,1500);" wrap="hard"></textarea>
        <label>(*) Solo se admitiran los primeros 1500 emails. El resto se borraran. </label>
        </td>
	</tr>

    <tr>
        <td align="right" colspan=2 valign="top">
            <a onclick="previo();" class="enlace" title="Paso anterior (Opiniones)">
                <img src="{$params.IMAGE_DIR}btn_prev.gif" border="0" alt="" align="bottom" /></a>
            <a onclick="sgte();" class="enlace" title="Paso siguiente (Vista previa)">
                <img src="{$params.IMAGE_DIR}btn_sgte.gif" border="0" alt="" align="bottom" /></a>
            
        </td>
    </tr>
</tbody>
</table>
</form>

{* <div id="botones">
    <a onclick="sgte();" class="enlace" title="Paso siguiente (Vista previa)">
        <img src="{$params.IMAGE_DIR}btn_sgte.gif" border="0" alt="" align="bottom" /></a>
</div> *}

<form action="{$smarty.server.SCRIPT_NAME}" method="post" id="bulletin_wizard">
    <textarea  id="data_bulletin" name="data_bulletin">{$smarty.post.data_bulletin|default:''}</textarea>
    <input type="hidden" name="action" id="action" value="preview" /> {* Next step *}
</form>

{literal}
<script language="javascript" type="text/javascript">
try {
    $('destinatarios').value = Base64.decode( data.mailboxes );
} catch(e) {}
</script>
{/literal}

{include file="bulletin.navigation.tpl"}
</div> {* DIV#container *}
{/if}


{* FORM PREVIEW ********************************************************************************** *}
{if ($ACTION == 'preview') || ($ACTION == 'step4') }
{* <script language="javascript" type="text/javascript" src="{$params.JS_DIR}calendar_date_select.js"></script> *}

<script language="javascript" type="text/javascript" defer="defer">
var data = {ldelim}{rdelim};
{if empty($smarty.post.data_bulletin)}
data = {ldelim}'news':[], 'opinions':[], 'mailboxes':null{rdelim};
{else}
data = {$smarty.post.data_bulletin|clearslash};
{/if}
{literal}

function sgte() {
    $('action').value = 'send';
    $('bulletin_wizard').submit();
}

function view_pdf() {
    $('action').value = 'view_pdf';
    $('bulletin_wizard').submit();
}

function previo() {
    $('action').value = 'mailboxes';    
    $('bulletin_wizard').submit();
}

function attach_pdf_confirm(elto) {
    $('attach_pdf').value = (elto.checked)? 1: 0;
}
</script>
{/literal}
<div id="container_preview">

    {* <fieldset>
        <legend>Opciones de envío</legend>

        <label for="attach_pdf">
            Adjuntar fichero PDF:
            <input type="checkbox" value="1" onblur="attach_pdf_confirm(this);" />
        </label>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <a onclick="view_pdf();" class="enlace" title="Ver PDF">
            <img src="{$params.IMAGE_DIR}btn_ver_pdf.gif" border="0" alt="" /></a>
    </fieldset> *}

    <div id="botones">
        <a onclick="previo();" class="enlace" title="Paso anterior (Opiniones)">
            <img src="{$params.IMAGE_DIR}btn_prev.gif" border="0" alt="" align="bottom" /></a>        
        <a onclick="sgte();" class="enlace" title="Paso siguiente (Envío del boletín)">
            <img src="{$params.IMAGE_DIR}btn_sgte.gif" border="0" alt="" align="bottom" /></a>
    </div>
    <br />

    {include file="bulletin.navigation.tpl"}

    <br /><br />

    <fieldset>
        <legend>Vista previa</legend>

        {include file="bulletin.html.tpl"}

    </fieldset>



    {* <div id="cron_timestamp_container" style="display: none;">
        <label for="cron_timestamp_bool">
            Programar el envío:
            <input id="cron_timestamp_bool" name="cron_timestamp_bool" type="checkbox" value="1" />
        </label>
        <input id="cron_timestamp" name="cron_timestamp" type="hidden" />
        <span id="cron_timestamp_widget" style="display: none; position: absolute;"></span>
        <script type="text/javascript">
        //<![CDATA[ {literal}
        _translations = {
            "OK": "OK",
            "Now": "Ahora",
            "Today": "Hoy"
        }

        Date.weekdays = $w("D L Ma Mi J V S");

        Date.months = $w("Enero Febrero Marzo Abril Mayo Junio Julio Agosto Septiembre Octubre Noviembre Deciembre" );
        new CalendarDateSelect( $('cron_timestamp_widget').previo(), {time:true, embedded:true, year_range:[2007, 2010]} );
        //]]> {/literal}
        </script> 
    </div> *}



<form action="{$smarty.server.SCRIPT_NAME}" method="post" id="bulletin_wizard">
    <textarea id="data_bulletin" name="data_bulletin">{$smarty.post.data_bulletin|default:''}</textarea>
    <input id="attach_pdf" name="attach_pdf" type="hidden" value="" />
    <input type="hidden" name="action" id="action" value="send" /> {* Next step *}
</form>

</div> {* DIV#container *}
{/if}


{* SENDING ********************************************************************************** *}
{if ($ACTION == 'send') || ($ACTION == 'step5') }
<script language="javascript" type="text/javascript" src="{$params.JS_DIR}calendar_date_select.js"></script>

{literal}
<script language="javascript" type="text/javascript" defer="defer">

</script>
{/literal}
<div id="container">


{section name=err loop=$errors}
<strong>{$errors[err]}</strong><br />
{sectionelse}
	<strong>El boletín ha sido enviado con éxito.</strong>
	{literal}
	<script language="javascript" type="text/javascript">
	function redirectInit() {
		location.href = 'bulletin.php';
	}

	window.setTimeout(redirectInit, 5000);
	</script>
	{/literal}
{/section}

{* include file="bulletin.navigation.tpl" *}
</div> {* DIV#container *}
{/if}



{include file="footer.noform.tpl"}