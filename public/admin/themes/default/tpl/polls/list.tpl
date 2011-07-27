{extends file="base/admin.tpl"}
{block name="footer-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilspoll.js"></script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{$titulo_barra}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
                    </a>
                </li>
                <li>
                    <button type="button" style="cursor:pointer; background-color: #e1e3e5; border: 0px; width: 95px;" onClick="javascript:checkAll(this.form['selected_fld[]'],'select_button');">
                        <img id="select_button" class="icon" src="{$params.IMAGE_DIR}select_button.png" title="Seleccionar Todo" alt="Seleccionar Todo"  status="0">
                    </button>
                </li>
                <li>
                    <a href="#" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva carta');" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/poll-new.png" title="Nueva encuesta" alt="Nuevo Encuesta"><br />Nueva Encuesta
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        <ul class="tabs2" style="margin-bottom: 28px;">
            {include file="menu_categorys.tpl" home="poll.php?action=list"}
        </ul>

         <div id="{$category}">

             <table class="adminheading">
                 <tr>
                     <th nowrap>Encuestas</th>
                 </tr>
             </table>

             <table class="adminlist">
             <thead>
                <tr>
                    <th class="title"></th>
                    <th class="title">T&iacute;tulo</th>
                    <th class="title">Subt&iacute;tulo</th>
                    <th align="center">Votos</th>
                    <th align="center">Visto</th>
                    <th align="center">Fecha</th>
                    <th align="center">Favorito</th>
                    <th align="center">Publicado</th>
                    <th align="center">Archivar</th>
                    <th align="center">Modificar</th>
                    <th align="center">Eliminar</th>
                </tr>
             </thead>

             {section name=c loop=$polls}
             <tr {cycle values="class=row0,class=row1"}  style="cursor:pointer;" >
                 <td style="padding:10px;font-size: 11px;">
                     <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$polls[c]->id}"  style="cursor:pointer;" >
                 </td>
                   <td style="padding:10px;font-size: 11px;width:40%;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();" >
                     {$polls[c]->title|clearslash}
                 </td>
                  <td style="padding:10px;font-size: 11px;width:20%;" onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();"  >
                     {$polls[c]->subtitle|clearslash}
                 </td>
                  <td style="padding:10px;font-size: 11px;width:10%;" align="center">
                      {$polls[c]->total_votes}
                 </td>

                 <td style="padding:1px;font-size: 11px;width:10%;" align="center">
                     {$polls[c]->views}
                 </td>
                 <td style="padding:1px;width:10%;font-size: 11px;" align="center">
                         {$polls[c]->created}
                 </td>
                  <td style="padding:10px;font-size: 11px;width:6%;" align="center">
                 {if $polls[c]->favorite == 1}
                     <a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=0&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Publicado"></a>
                 {else}
                     <a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=1&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Pendiente"></a>
                 {/if}
                     </td>

                 <td style="padding:10px;font-size: 11px;width:10%;" align="center">
                     {if $polls[c]->available == 1}
                         <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
                             <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" /></a>
                     {else}
                         <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
                             <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" /></a>
                     {/if}

                 </td>
                     <td style="padding:1px;width:10%;font-size: 11px;" align="center">
                         <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Archivar a Hemeroteca">
                         <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar a Hemeroteca" /></a>
                     </td>
                 <td style="padding:10px;font-size: 11px;width:10%;" align="center">
                     <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$polls[c]->id}');" title="Modificar">
                         <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                 </td>
                 <td style="padding:10px;font-size: 11px;width:10%;" align="center">
                     <a href="#" onClick="javascript:confirmar(this, '{$polls[c]->id}');" title="Eliminar">
                         <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                 </td>
             </tr>

             {sectionelse}
             <tr>
                 <td align="center" colspan=10><br><br><p><h2><b>Ninguna encuesta guardada</b></h2></p><br><br></td>
             </tr>
             {/section}

             {if count($polls) gt 0}
               <tr>
                   <td colspan="9" align="center">{$paginacion->links}</td>
               </tr>
             {/if}
             </table>

         </div>


        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id}" />

    </div>
</form>
{dialogo script="print"}

{/block}
