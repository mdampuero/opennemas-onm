{extends file="base/admin.tpl"}


{block name="footer-js" append}
<script defer="defer" type="text/javascript" language="javascript" src="{$params.JS_DIR}photos.js"></script>
{if isset($smarty.request.message) && strlen($smarty.request.message) > 0}
    <div class="message" id="console-info">{$smarty.request.message}</div>
    <script defer="defer" type="text/javascript">
        new Effect.Highlight('console-info', {ldelim}startcolor:'#ff99ff', endcolor:'#999999'{rdelim})
    </script>
{/if}

<script defer="defer" type="text/javascript">
function confirmar(url) {
    if(confirm('¿Está seguro de querer eliminar este fichero?')) {
        location.href = url;
    }
}
</script>

{if !empty($smarty.request.alerta)}
    <script type="text/javascript">
    {literal}
        // showMsg({'warn':[' "NO SE PUEDE ELIMINAR. <br />Esta imagen está siendo utilizada en: {/literal}{$smarty.request.alerta}. <br /><br /><a onClick="hideMsgContainer(\'msgBox\');">Aceptar</a>{literal}</br> <br />  ']},'inline');
    {/literal}
        alert("NO SE PUEDE ELIMINAR {$smarty.request.name} .\n Esta imagen está siendo utilizada en: {$smarty.request.alerta}.");
    </script>
{/if}
{/block}

{block name="content"}
{*<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}> *}



<div id="contenedor-gral">
    <ul class="tabs2">
        <li>
            <a href="mediamanager.php?listmode={$listmode}&category=GLOBAL" {if $category==0}style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>
                GLOBAL</a>
        </li>
         {if $smarty.server.PHP_SELF eq '/admin/mediamanager.php'}
           {* <li>
                <a href="{$home}?listmode={$listmode}&category=3" {if $category==3} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
                    ALBUMS</a>
            </li>
            *}
            <li>
                <a href="mediamanager.php?listmode={$listmode}&category=2" {if $category==2} style="color:#000000; font-weight:bold; background-color:#BFD9BF" {/if}>
                    PUBLICIDAD</a>
            </li>
        {/if}
        {include file="menu_categorys.tpl" home="mediamanager.php?listmode="}

    </ul>



    {* ************************************************************************ *}
    {* GLOBAL ***************************************************************** *}
    {if ($action eq 'search')  || ($category eq 'GLOBAL')}
        {if $action eq 'search'}
            {assign value='Búsqueda' var='title_bar'}
         {elseif $action eq 'searchResult'}
            {assign value='Resultado Búsqueda' var='title_bar'}
        {else}
            {assign value='Información' var='title_bar'}
        {/if}
        <br style="clear:both;" />
        <div style='float:left;margin-left:10px;margin-top:10px;'><h2>{$titulo_barra}:: {$title_bar} </h2></div>
        <div id="menu-acciones-admin" >
                <ul>
                     <li>
                        <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=search" onmouseover="return escape('<u>B</u>uscar Imagenes');" name="submit_mult" value="Buscar Imágenes">
                            <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}search.png" alt="Buscar Imágenes"><br />Buscar
                        </a>
                    </li>
                     <li>
                        <a class="admin_add" href="mediamanager.php?category={$category}" onmouseover="return escape('Listado de Categorias');" name="submit_mult" value="Listado de Categorias">
                            <img border="0" style="width:50px;"  src="{$params.IMAGE_DIR}icons.png" alt="Información"><br />Información
                        </a>
                    </li>
                    {if $action eq 'searchResult'}
                        <li>
                            <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                                <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar"><br />Eliminar
                            </a>
                        </li>
                        <li>
                            <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                                <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                            </button>
                        </li>
                    {/if}
               </ul>
        </div>
         {if $action eq 'search'}
            {include file="image_search.tpl"}
         {elseif $action eq 'searchResult'}
            {include file="media-browser.tpl"}
        {else}
            {include file="media_list_infor.tpl"}
        {/if}
   {else}



    </div>

   <br style="clear:both;" />

{* BOTONERA: incluir en botonera_up.tpl*}
     {if $smarty.server.PHP_SELF eq '/admin/mediamanager.php'}
        {if $action=='list_today'}
            {assign var=accion value='Fotos de Hoy'}
        {elseif $action=='list_all'}
            {assign var=accion value='Catálogo Fotos'}
        {elseif $action=='upload'  || $action=='results'}
            {assign var=accion value='Subir Fotos'}
        {elseif $action=='search'}
            {assign var=accion value='Buscar Fotos'}
        {elseif $action=='searchResult'}
            {assign var=accion value='Resultado Búsqueda'}
        {/if}
     {else}
        {if $action=='list_today'}
            {assign var='accion' value='Gráficos de Hoy'}
        {elseif $action=='list_all'}
            {assign var='accion' value='Catálogo Gráficos'}
        {elseif $action=='upload'  || $action=='results'}
            {assign var='accion' value='Subir Gráficos'}
        {elseif $action=='search'  || $action=='searchResult'}
            {assign var='accion' value='Buscar Gráficos'}
        {/if}
     {/if}

      {if $action neq 'results'}
        <div style='float:left;margin-left:10px;margin-top:10px;'><h2> {$accion}:: &nbsp;{$datos_cat[0]->title}</h2></div>
	<div id="menu-acciones-admin">
        <ul>
            {if $action neq 'upload'}
            <li>
                <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 6);"  onmouseover="return escape('<u>E</u>liminar todos');" name="submit_mult" value="Eliminar todos">
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar todos"><br />Eliminar todos
                </a>
            </li>
            <li>
                <a class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);"  onmouseover="return escape('<u>E</u>liminar');" name="submit_mult" value="Eliminar">
                    <img border="0" src="{$params.IMAGE_DIR}trash_button.gif" alt="Eliminar"><br />Eliminar
                </a>
            </li>
            <li>
                <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onmouseover="return escape('<u>S</u>eleccionar todo');" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                    <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" alt="Seleccionar Todo"  status="0">
                </button>
            </li>
            {/if}

            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=search" onmouseover="return escape('<u>B</u>uscar Imagenes');" name="submit_mult" value="Buscar Imágenes">
                    <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}search.png" alt="Buscar Imágenes"><br />Buscar
                </a>
            </li>

            {if $smarty.server.PHP_SELF eq '/admin/mediamanager.php'}
            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=upload#upload-photos"   onmouseover="return escape('<u>S</u>ubir Fotos');" name="submit_mult" value="Subir Fotos">
                    <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}upload_web.png" alt="Subir Fotos"><br />Subir Fotos
                </a>
            </li>
            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_all"   onmouseover="return escape('<u>C</u>atálogo de Fotos');" name="submit_mult" value="Catálogo de Fotos">
                    <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}folder_image.png" alt="Catálogo de Fotos"><br />Catálogo de Fotos
                </a>
            </li>
            <li>
                <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_today"   onmouseover="return escape('Fotos de <u>H</u>oy');" name="submit_mult" value="Fotos de Hoy">
                    <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}image_today.png" alt="Fotos de Hoy"><br />Fotos de Hoy
                </a>
            </li>

            {else}
            <li>
                 <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=upload#upload-photos"   onmouseover="return escape('<u>S</u>ubir Fotos');" name="submit_mult" value="Subir Gráficos">
                     <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}upload_web.png" alt="Subir Fotos"><br />Subir Gráficos
                 </a>
            </li>
            <li>
                 <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_all#media-browser"   onmouseover="return escape('<u>C</u>atálogo de Fotos');" name="submit_mult" value="Catálogo de Gráficos">
                     <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}folder_image.png" alt="Catálogo de Fotos"><br />Catálogo de Gráficos
                 </a>
            </li>
            <li>
                 <a class="admin_add" href="mediamanager.php?category={$category}&amp;action=list_today#media-browser"   onmouseover="return escape('Fotos de <u>H</u>oy');" name="submit_mult" value="Gráficos de Hoy">
                     <img border="0" style="width:50px;" src="{$params.IMAGE_DIR}image_today.png" alt="Fotos de Hoy"><br />Gráficos de Hoy
                 </a>
            </li>
            {/if}

        </ul>
	</div>
    {/if}



   <div id="{$category}" class="categ" style="width:100%; padding: 6px 2px;">
        <br style="clear:both;" />

        {* ******************************************************************** *}
        {* UPLOAD ************************************************************* *}
        {if $action == 'upload'}
            <div id="upload-photos" style="width:1110px;clear:both;">
                <table border="0" cellpadding="0" cellspacing="0" class="fuente_cuerpo" width="800" style="border-bottom: 1px solid #666; padding: 6px 2px;">
                <tbody>
                    <tr>
                        <td align="left">
                            <div>
                                <form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=addPhoto" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" id="action" name="action" title="Título" value="addPhoto" />
                                    <table>
                                        <tr>
                                            <td style="border: 1px solid #ccc;">
                                                <a onclick="addFile();" style="cursor:pointer;">
                                                    <img src="{$params.IMAGE_DIR}add.png" border="0" alt="Añadir fichero" width="22px" height="22px" />
                                                </a>
                                            </td>

                                            <td style="border: 1px solid #ccc;">
                                                <a onclick="delFile()" style="cursor:pointer;">
                                                    <img src="{$params.IMAGE_DIR}del.png" border="0" alt="Suprimir fichero" width="22px" height="22px" />
                                                </a>
                                            </td>

                                            <td style="border: 1px solid #ccc;">
                                                <input type="image" src="{$params.IMAGE_DIR}save_all.png" alt="Submit" name="submit" align="middle" width="22px" height="22px" style="cursor:pointer;" />
                                            </td>
                                        </tr>
                                    </table>

                                    <div id="fotosContenedor">
                                        <div class="marcoFoto" id="foto0">
                                            <input type="hidden" name="MAX_FILE_SIZE" value="307200" />
                                            <p> Foto #0:
                                                <input type="hidden" id="title" name="title" title="Título" value="" readonly="readonly" />
                                                <input type="file" name="file[0]" id="fFile0" class="required" size="50" onChange="ckeckName(this,'fileCat[0]');"/>
                                                <div id="fileCat[0]" name="fileCat[0]" style="display:none;">
                                                    <table border="0" bgcolor="red" cellpadding="4">
                                                        <tr>
                                                            <td>
                                                                El nombre es incorrecto. Contiene espacios en blanco o caracteres especiales.
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>

                                                <input type="hidden" name="category" value="{$category}" />

                                                {if $smarty.server.PHP_SELF eq '/admin/mediamanager.php'}
                                                    <input type="hidden" name="media_type" value="image" />
                                                {else}
                                                    <input type="hidden" name="media_type" value="graphic" />
                                                {/if}
                                            </p>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <br />
                            <b>Forma de uso</b>
                            <br /><br />

                            <table>
                                <tr>
                                    <td>
                                        <img src="{$params.IMAGE_DIR}add.png" border="0" alt="Añadir fichero" width="22px" height="22px" />
                                    </td>
                                    <td>
                                        Este signo se utiliza para <b>AÑADIR</b> una foto m&aacute;s al formulario de subida<br />
                                    </td>
                                </tr>

                                <tr>
                                    <td>
                                        <img src="{$params.IMAGE_DIR}del.png" border="0" alt="Añadir fichero" width="22px" height="22px" />
                                    </td>
                                    <td>
                                        Este signo se utiliza para <b>QUITAR</b> una foto del formulario de subida<br />
                                    </td>
                                </tr>
                            </table>

                            <ul>
                                <li>El tamaño máximo de la imagen es de 200k</li>
                                <li><b>SOLO</b> se podrán subir <b>10</b> fotos de una vez</li>
                                <li>Las fotos se guardarán en la carpeta de la categoría seleccionada</li>
                            </ul>
                            <br />
                        </td>
                    </tr>
                </tbody>
                </table>
            </div>

        {elseif $action == 'results'}
            {* ******************************************************************** *}
            {* RESULTS ************************************************************ *}
            <form id="form_upload" action="{$smarty.server.SCRIPT_NAME}?action=updateDatasPhotos" method="POST">
                <input type="hidden" name="category" value="{$category}" />
                <div style='float:left;margin-left:10px;margin-top:10px;'><h2> {$accion}:: &nbsp;{$datos_cat[0]->title}</h2></div>
                <div id="menu-acciones-admin">
                    <ul>
                        <li>
                            <a href="#" class="admin_add" onClick="enviar(this, '_self', 'updateDatasPhotos', '');">
                                <img border="0" src="{$params.IMAGE_DIR}save.gif" title="Guardar y salir"  alt="Guardar y salir" />
                                <br />
                                Guardar
                            </a>
                        </li>
                        <li>
                            <a href="#" class="admin_add" onClick="enviar(this, '_self','{$smarty.session.desde}', 0);" value="Cancelar" title="Cancelar">
                                <img border="0" src="{$params.IMAGE_DIR}cancel.png" title="Cancelar" alt="Cancelar" />
                                <br />
                                Cancelar
                            </a>
                        </li>
                    </ul>
                </div>

                <div id="media_msg" style="float:right;width:300px;display:none;"> </div>

                {if !empty($smarty.request.mensaje)}
                    {literal}
                    <script type="text/javascript">
                    showMsgContainer({'warn':['Ocurrió algún error al subir: <br /> {/literal}{$smarty.request.mensaje}. {literal}<br /> Compruebe su tamaño (MAX 300 Kb). <br /> ']},'inline','media_msg');
                    </script>
                    {/literal}
                {/if}

                <input type="hidden" name="category" value="{$smarty.request.category}" />

                {section name=n loop=$photo}
                    {include file="photo_data.tpl" display="none" photo1=$photo[n]}
                {/section}
            </form>

        {else}
            {if !empty($smarty.request.mensaje)}
                {literal}
                <script type="text/javascript">
                    showMsgContainer({'warn':['Ocurrió algún error al subir: <br /> {/literal}{$smarty.request.mensaje}. {literal}<br /> Compruebe su tamaño (MAX 300 MB). <br /> ']},'inline','media_msg');
                </script>
                {/literal}
            {/if}

            {include file="media-browser.tpl"}
        {/if}

    {/if} {* endif $category eq 'GLOBAL' *}
</div>

<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
