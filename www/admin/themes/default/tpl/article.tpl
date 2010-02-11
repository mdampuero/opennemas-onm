{include file="header.tpl"}
{* LISTADO ******************************************************************* *}
{if !isset($smarty.request.action) || $smarty.request.action eq "list"}
    {* ZONA MENU CATEGORIAS ******* *}
    <ul class="tabs2" style="margin-bottom: 28px;">
        <li>
            <a href="article.php?action=list&category=home" {if $category=='home' } style="color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>HOME</a>
        </li>
        {include file="menu_categorys.tpl" home="article.php?action=list"}
    </ul>

    {* Archivo respuesta cabecera ajax guarda posicion*}
    {* include_php file="cambiapos.php" *}
    <br style="clear: both;" />
    
    {include file="botonera_up.tpl"}
    {*PROVISIONAL alert eliminar varias noticias con relacionados*}
    {if $smarty.get.alert eq 'ok'}       
     <script type="text/javascript" language="javascript">
    {literal}       
           alert('{/literal}{$smarty.get.msg}{literal}');
    {/literal}
    </script>
    {/if}
    {* MENSAJES DE AVISO GUARDAR POS******* *}
    <div id="warnings-validation"></div>

    <input type="hidden"  id="category" name="category" value="{$category}">
    <div id="{$category}">
        <table class="adminheading">
            <tr>
                <th>Articulos en Portada <img  border="0" style="cursor:pointer;" src="{$params.IMAGE_DIR}iconos/info.png" onmouseover="Tip('<img src={$params.IMAGE_DIR}leyenda_programadas.png >', SHADOW, true, ABOVE, true, WIDTH, 300)" onmouseout="UnTip()" ></th>
            </tr>
        </table>
        <div id="pagina">
            {* NOTICIA DESTACADA ******* *}
            <table class="adminlist" border=0>
                 <tr>
                    <th align="center"></th>
                    <th align="center">T&iacute;tulo</th>
                    <th align="center" style="width:50px;">Visto</th>
                    <th align="center" style="width:50px;">Votos</th>
                    <th align="center" style="width:50px;"><img src="{php}echo($this->image_dir);{/php}coment.png" border="0" alt="Numero comentarios" /></th>
                    <th align="center" style="width:70px;">Fecha</th>
                    {if $category neq 'home'}
                        <th align="center" style="width:50px;">Home</th>
                    {else}
                        <th align="center" style="width:50px;">Secci&oacute;n</th>
                    {/if}
                    <th align="center" style="width:110px;">Publisher</th>
                    <th align="center" style="width:110px;">Last Editor</th>
                    <th align="center" style="width:50px;">Editar</th>
                    <th align="center" style="width:50px;">Archivar</th>
                    <th align="center" style="width:50px;">Despub</th>
                    <th align="center" style="width:50px;">Elim</th>
                </tr>
                <tr>
                    <td colspan=13>
                        <div id="des" class="seccion" class="seccion" style="float:left;width:100%; border:1px solid gray;">
                            <br />
                            {section name="p" loop=$destacado}
                                <table id='tabla{$aux}' name='tabla{$aux}' value="{$destacado[p]->id}" width="100%" class="tabla" style="text-align:center;padding:0px;">
                                    <tr class="{cycle values="row0,row1"}{schedule_class item=$destacado[p]}" style="cursor:pointer;" >
                                        <td style="width:10px;" align="left">
                                            <input type="checkbox" class="minput" pos=1 id="selected_fld_des_{$smarty.section.p.iteration}" name="selected_fld[]" value="{$destacado[p]->id}"  style="cursor:pointer;" >
                                        </td>
                                        <td style="" align="left" onClick="javascript:document.getElementById('selected_fld_des_{$smarty.section.p.iteration}').click();" {if $destacado[p]->isScheduled()}onmouseout="UnTip()" onmouseover="Tip('{schedule_info item=$destacado[p]}', SHADOW, true, ABOVE, true, WIDTH, 300)"{/if}>
                                            
                                            {is_clone item=$destacado[p]}{$destacado[p]->title|clearslash}
                                        </td>                                                                                
                                        <td style="width:50px;">
                                            {$destacado[p]->views}
                                        </td>
                                        <td  class='no_view' style="width:50px;" align="center">
                                            {$destacado[p]->rating}
                                        </td>
                                        <td class='no_view' style="width:50px;" align="center">
                                            {$destacado[p]->comment}
                                        </td>
                                        <td style="width:70px;" align="center">
                                            {$destacado[p]->created}
                                        </td>
                                        <td style="width:50px;" align="center">
                                            {if $category neq 'home'}
                                                {if $destacado[p]->in_home == 1}
                                                     <img class="inhome" src="{$params.IMAGE_DIR}gohome.png" border="0" alt="Publicado en home" title="Publicado en home"/></a>
                                                {elseif $destacado[p]->in_home == 2}
                                                        <a href="?id={$destacado[p]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" title="No Sugerir en home">
                                                            <img class="inhome" src="{$params.IMAGE_DIR}gosuggesthome.png" border="0" alt="No Sugerir en home" /></a>
                                                    {else}
                                                        <a href="?id={$destacado[p]->id}&amp;action=inhome_status&amp;status=2&amp;category={$category}" title="Sugerir en home">
                                                            <img class="inhome" src="{$params.IMAGE_DIR}home_no.png" border="0" alt="Sugerir en home" /></a>
                                                    {/if}                                                
                                            {else}
                                                  {$destacado[p]->category_name}
                                            {/if}
                                        </td>
                                        <td  class='no_view' style="width:110px;" align="center">
                                                   {$destacado[p]->publisher}
                                        </td>
                                        <td  class='no_view' style="width:110px;" align="center">
                                                   {$destacado[p]->editor}
                                        </td>
                                        <td style="width:50px;" align="center">
                                            <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$destacado[p]->id}');" title="Editar">
                                                <img src="{$params.IMAGE_DIR}edit.png" border="0" alt="Editar" /></a>
                                        </td>
                                        <td style="width:50px;" align="center">
                                            <a href="?id={$destacado[p]->id}&amp;action=change_status&amp;status=0&amp;category={$category}" onClick="javascript:confirm('¿Está seguro de enviarlo a hemeroteca?');" title="Archivar">
                                                <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar" /></a>
                                        </td>
                                        <td style="width:50px;" align="center">
                                            {if $category neq 'home'}
                                                {if $destacado[p]->frontpage == 1}
                                                    <a href="?id={$destacado[p]->id}&amp;action=frontpage_status&amp;status=0&amp;category={$category}" title="Quitar de portada" alt="Quitar de portada">
                                                        <img class="portada" src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Quitar de home" /></a>
                                                {else}
                                                    <a href="?id={$destacado[p]->id}&amp;action=frontpage_status&amp;status=1&amp;category={$category}" title="Publicar en portada" alt="Publicar en portada">
                                                        <img class="noportada" src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Publicar en home" /></a>
                                                {/if}
                                            {else}
                                                    <a href="?id={$destacado[p]->id}&amp;action=inhome_status&amp;status=0&amp;category={$category}" class="no_home" title="Quitar de home" alt="Quitar de home" ></a>
                                            {/if}
                                        </td>

                                        <td style="width:50px;" align="center">
                                            <a href="#" onClick="javascript:delete_article('{$destacado[p]->id}','{$category}',0);" title="Eliminar">
                                                <img src="{php}echo($this->image_dir);{/php}trash.png" border="0" /></a>
                                        </td>
                                    </tr>
                                </table>
                            {sectionelse}
                                <table>
                                    <tr><td align="center" colspan=6><br /><p><h2><b>Ninguna noticia como cabecera</b></h2></p></td></tr>
                                </table>
                            {/section}
                        </div>
                    </td>
                </tr>
            </table>
            <br />
 {if $category=='home' }
            <table class="adminlist">
                <tr valign="top">
                    <td width="50%">
                    {* COLUMNA IZQ  evenpublished ******* *}
                         <table class="adminlist">
                             <tr>
                                 <th align="center"> </th>
                                 <th align="center">T&iacute;tulo</th>
                                 <th align="center" style="width:30px;">Visto</th>
                                 <th align="center" style="width:60px;">Fecha</th>
                                 {if $category neq 'home'}
                                    <th align="center" style="width:30px;">Home</th>
                                 {else}
                                    <th align="center" style="width:50px;">Secci&oacute;n</th>
                                 {/if}
                                 <th align="center" style="width:30px;">Edit</th>
                                 <th align="center" style="width:30px;">Arch</th>
                                 <th align="center" style="width:30px;">Des</th>
                                 <th align="center" style="width:30px;">El</th>
                            </tr>
                            <tr>
                                <td colspan=13>
                                    <div id="even" class="seccion" style="float:left;width:100%; border:1px solid gray;"> <br>
                                       {assign var=aux value='2'}
                                       {renderarticle items=$evenpublished tpl="article_render_fila.tpl"  placeholder="placeholder_0_1"}

                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="50%">
                    {* COLUMNA DERECHA  oddpublished ******* *}
                        <div id="warnings1" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                        <div id="warnings2" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                        <div id="warnings3" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                        <div id="warnings4" style="color:#bb1313;font-size:16px;font-weight:bold; padding:2px 10px;"></div>
                        {include  file="article_column_right.tpl"}
                    </td>
                </tr>
            </table>             
     {else}
          <table class="adminlist">
                <tr valign="top">
                    <td width="99%">
                    {* COLUMNA IZQ  evenpublished ******* *}
                         <table class="adminlist">
                             <tr>
                                <th align="center">T&iacute;tulo</th>
                                <th align="center" style="width:50px;">Visto</th>
                                <th align="center" style="width:50px;">Votos</th>
                                <th align="center" style="width:50px;"><img src="{php}echo($this->image_dir);{/php}coment.png" border="0" alt="Numero comentarios" /></th>
                                <th align="center" style="width:70px;">Fecha</th>
                                <th align="center" style="width:50px;">Secci&oacute;n</th>
                                <th align="center" style="width:110px;">Publisher</th>
                                <th align="center" style="width:110px;">Last Editor</th>
                                <th align="center" style="width:50px;">Editar</th>
                                <th align="center" style="width:50px;">Archivar</th>
                                <th align="center" style="width:50px;">Despub</th>
                                 <th align="center" style="width:50px;">Elim</th>
                            </tr>
                            <tr>
                                <td colspan=13>
                                    <div id="even" class="seccion" style="float:left;width:100%; border:1px solid gray;"> <br>
                                       {assign var=aux value='2'}
                                       {renderarticlecondition items=$evenpublished tpl="article_render_1column.tpl" }

                                    </div>
                                </td>
                            </tr>
                        </table>
                    </td>

                </tr>
            </table>
     {/if}
       <table class="adminlist" border=0>  <tr><td width="100%">
                        <div id="div_no_home" style="width:100%;min-height:80px;background-color:#F5F5F5;overflow:auto;">
                          {if $category eq 'home'} NO EN HOME {else} NO EN PORTADA DE {$datos_cat[0]->title} {/if}
                        </div>
                    </td>
                  </tr>
                </table>
            {* CONTENEDOR INFERIOR  otros articulos  o en HOME articulos sugeridos u otras portadas ******* *}
            {if $category neq 'home'}
                <table class="adminheading">
                    <tr>
                        <td><b>Otros art&iacute;culos</b></td><td style="font-size: 10px;" align="right"><em>(estos art&iacute;culos <b>NO</b> apareceran en la portada. )</em></td>
                    </tr>
                </table>

            {else}
                
                <div id="down_menu">
                    {* EN HOME MENU PARA VER EL RESTO D PORTADAS *************** *}
                    <ul class="tabs">
                    <li>
                        <a id="link_suggested" {if $other_category=='suggested' } style="cursor:pointer;color:#000000; font-weight:bold; background-color:#BFD9BF"{/if}>SUGERIDAS </a>
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
                        {include file="menu_categorys.tpl" home="" }
                    </div>
                </div>
            {/if}
            <div id="frontpages" class="seccion" style="width:100%;clear:both;">
                 {include file="article_others_articles.tpl"}                 
             </div>
             
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
              {literal}
                // Controlar o cambio de posicións para amosar un aviso
                var posicionesIniciales = null;
                var posicionesInicialesWarning = false; // Mellorar o rendemento
                avisoGuardarPosiciones = function() {
                    //Provisional repite innecesariamente.
                   
                    {/literal}{if $category eq 'home'}
                        changedTables({$category});
                        alertsDiv();
                        {/if}
                    {literal}
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
                make_sortable_divs_portadas({/literal}'{$category}'{literal});

                Draggables.observers.each(function(item){
                    item.onEnd= avisoGuardarPosiciones;
                });


 
              {/literal}
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
{literal}
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
{/literal}
</script>
{include file="footer.tpl"}                            
