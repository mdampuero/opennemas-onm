{extends file="base/admin.tpl"}

{block name="header-js" append}
    {script_tag src="/utilsSpecial.js" language="javascript"}

{/block}
 

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Specials manager{/t}::&nbsp; {if $category eq 0}Widget Home{else}{$datos_cat[0]->title}{/if}</h2></div>
            <ul class="old-button">
                {acl isAllowed="SPECIAL_DELETE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mdelete', 0);" name="submit_mult" value="Eliminar" title="Eliminar">
                        <img border="0" src="{$params.IMAGE_DIR}trash.png" title="Eliminar" alt="Eliminar" ><br />Eliminar
                    </a>
                </li>
                {/acl}
                {acl isAllowed="SPECIAL_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 0);" name="submit_mult" value="noFrontpage" title="noFrontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish_no.gif" title="noFrontpage" alt="noFrontpage" ><br />Despublicar
                    </a>
                </li>
                {/acl}
                {acl isAllowed="SPECIAL_AVAILABLE"}
                <li>
                    <a href="#" class="admin_add" onClick="javascript:enviar2(this, '_self', 'mfrontpage', 1);" name="submit_mult" value="Frontpage" title="Frontpage">
                        <img border="0" src="{$params.IMAGE_DIR}publish.gif" title="Publicar" alt="Publicar" ><br />Publicar
                    </a>
                </li>
                {/acl}
                {acl isAllowed="SPECIAL_CREATE"}
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new" onmouseover="return escape('<u>N</u>uevo Special');" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}special.png" title="Nuevo Special" alt="Nuevo Special"><br />Nuevo Special
                    </a>
                </li>
                {/acl}
                {acl isAllowed="SPECIAL_SETTINGS"}
                <li class="separator"></li>
                    <li>
                        <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config special module{/t}">
                            <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                            {t}Configurations{/t}
                        </a>
                    </li>
                {/acl}
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        <ul class="pills clearfix">
            <li>
                <a href="{$smarty.server.SCRIPT_NAME}?action=list&category=favorite" {if $category=='favorite'}class="active"{elseif $ca eq $datos_cat[0]->fk_content_category}{*class="active"*}{/if}>{t}WIDGET HOME{/t}</a>
            </li>

           {include file="menu_categories.tpl" home=$smarty.server.SCRIPT_NAME|cat:"?action=list"}
        </ul>

        {render_messages}

        <table class="listing-table">
            <thead>
                <tr>

                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th class="title">{t}Title{/t}</th>
                    <th class="center" style="width:40px"><img src="{$params.IMAGE_DIR}seeing.png" alt="{t}Views{/t}" title="{t}Views{/t}"></th>
                    {if $category=='favorite'}<th style="width:65px;" class="center">{t}Section{/t}</th>{/if}
                    <th class="center" style="width:100px;">Created</th>
                    <th class="center" style="width:35px;">{t}Published{/t}</th>
                    <th class="center" style="width:35px;">{t}Favorite{/t}</th>
                    <th class="center" style="width:35px;">{t}Actions{/t}</th>
                </tr>
            </thead>

            {section name=as loop=$specials}
            <tr {cycle values="class=row0,class=row1"}>
                <td class="center">
                    <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}" name="selected_fld[]" value="{$specials[as]->id}"  style="cursor:pointer;" >
                </td>
                <td>
                    <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$specials[as]->pk_special}');" title="{$specials[as]->title|clearslash}">
                        {$specials[as]->title|clearslash}
                    </a>
                </td>
                 <td class="center">
                    {$specials[as]->views}
                </td>
                {if $category=='favorite'}
                    <td class="center">
                         {$specials[as]->category_title}
                    </td>
                {/if}
                <td class="center">
                         {$specials[as]->created}
                </td>
                <td class="center">
                    {acl isAllowed="SPECIAL_AVAILABLE"}
                        {if $specials[as]->available == 1}
                                <a href="?id={$specials[as]->pk_special}&amp;action=change_status&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="{t}Published{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_g.png" border="0" alt="{t}Published{/t}" /></a>
                        {else}
                                <a href="?id={$specials[as]->pk_special}&amp;action=change_status&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" title="{t}Pending{/t}">
                                        <img src="{$params.IMAGE_DIR}publish_r.png" border="0" alt={t}"Pending{/t}/></a>
                        {/if}
                    {/acl}
                </td>

                <td class="center">
                    {acl isAllowed="SPECIAL_FAVORITE"}
                        {if $specials[as]->favorite == 1}
                           <a href="?id={$specials[as]->id}&amp;action=change_favorite&amp;status=0&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_on" title="{t}Take out from frontpage{/t}"></a>
                        {else}
                            <a href="?id={$specials[as]->id}&amp;action=change_favorite&amp;status=1&amp;category={$category}&amp;page={$paginacion->_currentPage|default:0}" class="favourite_off" title="{t}Put in frontpage{/t}'></a>
                        {/if}
                    {/acl}
                </td>
                <td class="center">
                    <ul class="action-buttons">
                       {acl isAllowed="SPECIAL_UPDATE"}
                        <li>
                           <a href="#" onClick="javascript:enviar(this, '_self', 'read', '{$specials[as]->pk_special}');" title="{t}Edit{/t}" >
                                   <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                       </li>
                       {/acl}

                       {acl isAllowed="SPECIAL_DELETE"}
                       <li>
                           <a href="#" onClick="javascript:delete_special('{$specials[as]->pk_special}','{$paginacion->_currentPage|default:0}');" title={t}Delete{/t}>
                                   <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
                       </li>
                       {/acl}
                    </ul>
                </td>

            </tr>
            {sectionelse}
            <tr>
                <td class="empty" colspan=9>{t}There is no specials yet{/t}</td>
            </tr>
        {/section}
            <tfoot>
              <td colspan="9">
                {$paginacion->links|default:""}&nbsp;
              </td>
            </tfoot>
        </table>

        <input type="hidden" id="action" name="action" value="" />
        <input type="hidden" name="id" id="id" value="{$id|default:""}" />
    </div>
</form>
{/block}
