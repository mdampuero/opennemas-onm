{extends file="base/admin.tpl"}
{block name="footer-js" append}
    {script_tag src="/utilspoll.js" language="javascript"}
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
    <div class="top-action-bar">
        <div class="wrapper-content">
            <div class="title"><h2>{$titulo_barra}</h2></div>
            <ul class="old-button">
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />{t}Delete{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />{t}Unpublish{/t}
                    </a>
                </li>
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />{t}Publish{/t}
                    </a>
                </li>
                <li class="separator"></li>
                <li>
                    <a href="#" onclick="enviar(this, '_self', 'new', 0);" onmouseover="return escape('<u>N</u>ueva carta');" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}/poll-new.png" title="Nueva encuesta" alt="Nuevo Encuesta"><br />{t}New poll{/t}
                    </a>
                </li>
                {acl isAllowed="POLL_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config album module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Configurations{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        <ul class="pills" style="margin-bottom: 28px;">
            {include file="menu_categories.tpl" home="poll.php?action=list"}
        </ul>

        <table class="listing-table">

            <thead>
               <tr>
                    {if count($polls) > 0}
                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t}</th>
                    <th>{t}Subtitle{/t}</th>
                    <th class="center" style="width:40px">{t}Votes{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    <th style="width:110px;" class="center">{t}Date{/t}</th>
                    <th style="width:40px;" class="center">{t}Favorite{/t}</th>
                    <th style="width:40px;" class="center">{t}Published{/t}</th>
                    <th style="width:40px;" class="center">{t}Actions{/t}</th>
                    {else}
                    <th scope="col" colspan=9>&nbsp;</th>
                    {/if}
                </tr>
            </thead>
            <tbody>
                {section name=c loop=$polls}
                <tr >
                    <td>
                        <input type="checkbox" class="minput"  id="selected_{$smarty.section.c.iteration}" name="selected_fld[]" value="{$polls[c]->id}"  style="cursor:pointer;" >
                    </td>
                    <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();" >
                        <a href="{$smarty.server.PHP_SELF}?action=read&id={$polls[c]->id}" title="Modificar">
                            {$polls[c]->title|clearslash}
                        </a>
                    </td>
                    <td onClick="javascript:document.getElementById('selected_{$smarty.section.c.iteration}').click();"  >
                        {$polls[c]->subtitle|clearslash}
                    </td>
                    <td class="center">
                        {$polls[c]->total_votes}
                    </td>
                    <td class="center">
                        {$polls[c]->views}
                    </td>
                    <td class="center">
                        {$polls[c]->created}
                    </td>
                    <td class="center">
                        {if $polls[c]->favorite == 1}
                        <a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=0&amp;page={$paginacion->_currentPage}" class="favourite_on" title="Publicado">
                            &nbsp;
                        </a>
                        {else}
                        <a href="?id={$polls[c]->id}&amp;action=change_favorite&amp;status=1&amp;page={$paginacion->_currentPage}" class="favourite_off" title="Pendiente">
                            &nbsp;
                        </a>
                        {/if}
                    </td>

                    <td class="center">
                        {if $polls[c]->available == 1}
                        <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=0&amp;page={$paginacion->_currentPage}" title="Publicado">
                            <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="Publicado" />
                        </a>
                        {else}
                        <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Pendiente">
                            <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt="Pendiente" />
                        </a>
                        {/if}
                   </td>
                   <td class="center">
                        <ul class="action-buttons">
                            <li>
                               <a href="?id={$polls[c]->id}&amp;action=change_status&amp;status=1&amp;page={$paginacion->_currentPage}" title="Archivar a Hemeroteca">
                                    <img src="{$params.IMAGE_DIR}save_hemeroteca_icon.png" border="0" alt="Archivar a Hemeroteca" />
                               </a>
                            </li>
                            <li>
                                <a href="{$smarty.server.PHP_SELF}?action=read&id={$polls[c]->id}" title="Modificar">
                                    <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                </a>
                           </li>
                            <li>
                                <a href="#" onClick="javascript:confirmar(this, '{$polls[c]->id}');" title="Eliminar">
                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                </a>
                            </li>
                       </ul>
                    </td>
                </tr>

               {sectionelse}
               <tr>
                   <td class="empty" colspan=10>
                        {t}There is no polls yet.{/t}
                   </td>
               </tr>
               {/section}
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="9">
                        {$paginacion->links}&nbsp;
                    </td>
                </tr>
            </tfoot>

         </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />

    </div>
</form>
{dialogo script="print"}

{/block}
