{extends file="base/admin.tpl"}

{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsarticle.js"></script>
    {if $smarty.request.action == 'list_pendientes' || $smarty.request.action == 'list_agency'}
        <script type="text/javascript" language="javascript" src="{$params.JS_DIR}editables.js"></script>
    {/if}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
    {* ZONA MENU CATEGORIAS ******* *}
    <ul class="tabs2" style="margin-bottom: 28px;">
        <li>
            <a href="article.php?action=list&category=home" id='link_home' {if $category=='home'} style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>HOME</a>
        </li>
        <script type="text/javascript">
        // <![CDATA[
            Event.observe($('link_home'), 'mouseover', function(event) {
                $('menu_subcats').setOpacity(0);
                e = setTimeout("show_subcat('{$category}','{$home|urlencode}');$('menu_subcats').setOpacity(1);",1000);
            });
        // ]]>
        </script>
        {include file="menu_categorys.tpl" home="article.php?action=list"}
    </ul>

    {* Archivo respuesta cabecera ajax guarda posicion*}
    {* include_php file="cambiapos.php" *}

    {include file="botonera_up.tpl"}

    {*PROVISIONAL alert eliminar varias noticias con relacionados*}
    {if $smarty.get.alert eq 'ok'}
    <script type="text/javascript" language="javascript">
        alert('{$smarty.get.msg}');
    </script>
    {/if}
    {* MENSAJES DE AVISO GUARDAR POS******* *}
    <div id="warnings-validation"></div>

    <input type="hidden"  id="category" name="category" value="{$category}">
    <div id="{$category}">
        <table class="adminheading">
            <tr>
                <th align="center">Articulos en Portada <img  border="0" style="margin-left:10px; cursor:pointer;" src="{$params.IMAGE_DIR}iconos/info.png" onmouseover="Tip('<img src={$params.IMAGE_DIR}leyenda_programadas.png >', SHADOW, true, ABOVE, true, WIDTH, 300)" onmouseout="UnTip()" ></th>
            </tr>
        </table>
        <div id="pagina">
            {* NOTICIA DESTACADA *******

                       {include file="article_destacada.tpl"}

          *}
            <table class="adminlist">
                 <tr valign="top">
                    <td colspan="3">
                        <div id="warnings1" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                        <div id="warnings2" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                        <div id="warnings3" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                        <div id="warnings4" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                     </td>
                </tr>
                <tr id="columns" valign="top">
                    <td width="33%">
                          {include  file="article_column.tpl" place='placeholder_0'}
                    </td>
                     <td width="33%">
                          {include  file="article_column.tpl" place='placeholder_1'}
                    </td>
                     <td width="33%">
                          {include  file="article_column.tpl" place='placeholder_2'}
                    </td>
                </tr>
            </table>

            <div id="no_en_home" style="margin:10px 0">
                <table class="adminheading">
                    <tr>
                        <th align="center">Articulos en Portada <img  border="0" style="margin-left:10px; cursor:pointer;" src="http://demo.opennemasweb.es/admin/themes/default/images/iconos/info.png" onmouseover="Tip('<img src=http://demo.opennemasweb.es/admin/themes/default/images/leyenda_programadas.png >', SHADOW, true, ABOVE, true, WIDTH, 300)" onmouseout="UnTip()" ></th>
                    </tr>
                </table>
                <table class="adminlist" border=0 style="border:1px solid #ccc !important;">
                    <tr><td width="100%">
                        <div id="div_no_home" style="width:100%;min-height:80px;padding:5px;overflow:auto;">
                          {if $category eq 'home'} NO EN HOME {else} NO EN PORTADA DE {$datos_cat[0]->title} {/if}
                        </div>
                    </td>
                  </tr>
                </table>
            </div>

            {* CONTENEDOR INFERIOR  otros articulos  o en HOME articulos sugeridos u otras portadas ******* *}
            {if $category neq 'home'}
                <table class="adminheading">
                    <tr>
                        <td colspan="2"align="center"><b>Otros art&iacute;culos</b> - <em>(estos art&iacute;culos <b>NO</b> apareceran en la portada. )</em></td>
                    </tr>
                </table>
                 <div id="frontpages" class="seccion" style="width:100%;clear:both;">
                     {include file="article_others_articles.tpl"}
                 </div>
            {else}
                    <div id="art" style="display:none;"> </div>
                {*
                <div id="down_menu">
                    {* EN HOME MENU PARA VER EL RESTO D PORTADAS *************** *
                    <ul class="tabs">
                    <li>
                        <a id="link_suggested" {if $other_category=='suggested'} style="cursor:pointer;color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>SUGERIDAS </a>
                        </li>
                    </ul>
                     <script type="text/javascript">
                    // <![CDATA[
                        {literal}
                             Event.observe($('link_suggested'), 'click', function(event) {
                                    get_suggested_articles();
                                    change_style_link('link_suggested');
                             });
                        {/literal}
                    // ]]>
                    </script>
                     <div id="menu_front_category" style="width:100%;margin-bottom:20px;">
                        {include file="menu_categorys.tpl" home=""}
                    </div>
                </div> *}
            {/if}


            <table style="width:100%">
                    <tr align="right">
                        <td>
                            {include file="botonera_down.tpl"}
                        </td>
                    </tr>
            </table>

        </div> {* div id=pagina *}
        <script type="text/javascript">
            // <![CDATA[
            make_sortable_divs_portadas( '{$category}');
            // Controlar o cambio de posicións para amosar un aviso
            var posicionesIniciales = null;
            var posicionesInicialesWarning = false; // Mellorar o rendemento
            avisoGuardarPosiciones = function() {
                //Provisional repite innecesariamente.
                {if $category eq 'home'}
                changedTables({$category});

                {/if}
                if(!posicionesInicialesWarning) {
                    $$('input[type=checkbox]').each( function(item, idx) {
                        if(item.value != posicionesIniciales[idx].value) {
                            $('warnings-validation').update('Recuerde guardar posiciones');
                            posicionesInicialesWarning = true;
                            $break;
                        }
                    });
                }
            };

            document.observe('dom:loaded', function() {
                posicionesIniciales = $$('input[type=checkbox]');
            });
            make_sortable_divs_portadas('{$category}');

            Draggables.observers.each(function(item){
                item.onEnd= avisoGuardarPosiciones;
            });
        // ]]>
        </script>
    </div> {* div id=$category *}
{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "new"}
    {include file="botonera_up.tpl"}
    {include file="article_new.tpl"} {* FORMULARIO PARA ENGADIR UN CONTENIDO ************************************** *}
{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "read"}
    {include file="botonera_up.tpl"}
    {include  file="article_edit.tpl"} {* FORMULARIO PARA ACTUALIZAR UN CONTENIDO *********************************** *}
{/if}
{if  $smarty.request.action eq "read" || $smarty.request.action eq "new"}
        {* Susbtituted by the Control.DatePicker prototype widget *}
        {* dhtml_calendar inputField="starttime" button="triggerstart" singleClick=true ifFormat="%Y-%m-%d %H:%M:%S" firstDay=1 align="CR"}
        {dhtml_calendar inputField="endtime" button="triggerend" singleClick=true ifFormat="%Y-%m-%d %H:%M:%S" firstDay=1 align="CR" *}

        {* This line add a generic images browser to TinyMCE *}
        {* <script type="text/javascript" src="{$params.JS_DIR}/swampy_browser/sb.js"></script> *}

        <script type="text/javascript" src="{$params.JS_DIR}/tiny_mce/opennemas-config.js"></script>
        {literal}
        <script type="text/javascript" language="javascript">
            tinyMCE_GZ.init( OpenNeMas.tinyMceConfig.tinyMCE_GZ );
            {/literal}

            {if isset($article) && $article->isClone()}
            OpenNeMas.tinyMceConfig.simple.readonly   = 1;
            OpenNeMas.tinyMceConfig.advanced.readonly = 1;
            {/if}

            {literal}
            OpenNeMas.tinyMceConfig.simple.elements = "summary";
            tinyMCE.init( OpenNeMas.tinyMceConfig.simple );

            OpenNeMas.tinyMceConfig.advanced.elements = "body";
            tinyMCE.init( OpenNeMas.tinyMceConfig.advanced );
        </script>
        {/literal}

        <div id="reloadPreview" style="display: none; background-color: #FFE9AF; color: #666; border: 1px solid #996699; padding: 10px; font-size: 1.1em; font-weight: bold; width: 550px; position: absolute; right: 0; top: 0;">
            <img src="{$params.IMAGE_DIR}loading.gif" border="0" align="absmiddle" />
            <span id="reloadPreviewText"></span>
        </div>
        <div id="savePreview" style="display: none; background-color: #FFE9AF; color: #666; border: 1px solid #996699; padding: 10px; font-size: 1.1em; font-weight: bold; width: 550px; position: absolute; right: 0; top: 0;">
            <img src="{$params.IMAGE_DIR}btn_filesave.png" border="0" align="absmiddle" />
            <span id="savePreviewText"></span>
        </div>
{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "list_pendientes"}
    {include  file="article_pendientes.tpl"}
    {* FORMULARIO PARA LISTAR PENDIENTES *********************************** *}
{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "list_agency"}
    {include  file="article_agencys.tpl"}
    {* FORMULARIO PARA LISTAR PENDIENTES *********************************** *}
{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "list_hemeroteca"}
    {include  file="article_hemeroteca.tpl"}
    {* FORMULARIO PARA LISTAR HEMEROTECA *********************************** *}
{/if}

{if isset($smarty.request.action) && $smarty.request.action eq "only_read"}
    {include file="botonera_up.tpl"}
    {include  file="article_only_read.tpl"} {* CONSULTAR UNA NOTICIA ******** *}
{/if}

<td valign="top" align="right" style="padding:4px;" width="30%">

<script type="text/javascript" language="javascript">
document.observe('dom:loaded', function() {
    if($('title')){
        new OpenNeMas.Maxlength($('title'), {});
        $('title').focus(); // Set focus first element
    }
});

if($('starttime')) {
    new Control.DatePicker($('starttime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });

    new Control.DatePicker($('endtime'), {
        icon: './themes/default/images/template_manager/update16x16.png',
        locale: 'es_ES',
        timePicker: true,
        timePickerAdjacent: true,
        dateTimeFormat: 'yyyy-MM-dd HH:mm:ss'
    });
}
</script>


<input type="hidden" id="action" name="action" value="" />
<input type="hidden" name="id" id="id" value="{$id}" />
</form>
{/block}
