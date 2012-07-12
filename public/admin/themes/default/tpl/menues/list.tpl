{extends file="base/admin.tpl"}

{block name="header-js" append}
    <style type="text/css">
        .panel{ border:0 !important; }

        .drag-category{
            cursor:pointer;
            padding:10 px;
            list-style-type: none;
            border: 1px solid #CCCCCC;
            width:200px;
        }
    </style>
    {script_tag src="/utilsMenues.js" language="javascript"}
{/block}



{block name="content"}
<form action="{url name=admin_menus}" method="GET" name="formulario" id="formulario">
    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Menu manager{/t} :: {t}Listing menues{/t}</h2></div>
              <ul class="old-button">
                  {acl isAllowed="MENU_DELETE"}
                <li>
                     <a class="delChecked" data-controls-modal="modal-menu-batchDelete" href="#" title="Eliminar" alt="Eliminar">
                            <img src="{$params.IMAGE_DIR}trash.png" border="0"  title="Eliminar" alt="Eliminar" ><br />Eliminar
                        </a>
                </li>
                {/acl}

                <li class="separator"></li>
                <li>
                    {acl isAllowed="MENU_CREATE"}
                    <a href="{url name=admin_menu_create}" class="admin_add">
                        <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New menu{/t}
                    </a>
                    {/acl}
                </li>
            </ul>
        </div>
    </div>

    <div class="wrapper-content">
        {render_messages}
        <table class="listing-table">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" id="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t}</th>
                    <th class="right" style="width:100px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {section loop=$menues name=m}
                    <tr>
                         <td class="center">
                            <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}"
                                   name="selected_fld[]" value="{$menues[m]->pk_menu}" style="cursor:pointer;" >
                        </td>
                        <td>
                            {acl isAllowed="MENU_UPDATE"}
                            <a href="{url name=admin_menu_show id=$menues[m]->pk_menu}"
                                title="{t 1=$menues[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                            {/acl}
                            {acl isAllowed="MENU_UPDATE"}
                                 {$menues[m]->name|capitalize}
                            </a>
                            {/acl}
                        </td>
                         <td class="right">
                            <div class="btn-group">
                            {acl isAllowed="MENU_UPDATE"}
                            <a href="{url name=admin_menu_show id=$menues[m]->pk_menu}" \
                                title="{t 1=$menues[m]->name}Edit page '%1'{/t}" class="btn">
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>
                            {/acl}
                            {if $menues[m]->type eq 'user'}
                                {acl isAllowed="MENU_ADMIN"}
                                    <a  class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                        data-url="{url name=admin_menu_delete id=$menues[m]->pk_menu}"
                                        data-title="{$menues[m]->name|capitalize}"
                                        href="{url name=admin_menus_delete id=$menues[m]->pk_menu}">
                                        <i class="icon-trash icon-white"></i>
                                    </a>
                                {/acl}
                            {/if}
                            </div>
                        </td>
                    </tr>

                    {foreach key=k item=subMenu from=$subMenues}
                        {if $k eq $menues[m]->pk_menu}
                            {section loop=$subMenu name=s}
                            <tr>
                                 <td class="center">
                                     <input type="checkbox" class="minput"
                                            id="selected_{$smarty.section.as.iteration}"
                                            name="selected_fld[]" value="{$subMenu[s]->pk_menu}"  style="cursor:pointer;" >
                                </td>
                                <td style="padding-left:20px">
                                    <strong>&rArr; </strong>
                                    {acl isAllowed="MENU_UPDATE"}
                                    <a href="{url name=admin_menu_show id=$subMenu[s]->pk_menu}"
                                    {/acl}
                                       title="{t 1=$subMenu[s]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                        {$subMenu[s]->name|capitalize}
                                    </a>
                                </td>

                                <td class="right">
                                    <div class="btn-group">
                                    {acl isAllowed="MENU_UPDATE"}
                                    <a href="{url name=admin_menu_show id=$subMenu[s]->pk_menu}" \
                                        title="{t}Edit{/t}" class="btn">
                                        <i class="icon-pencil"></i> {t}Edit{/t}
                                    </a>
                                    {/acl}
                                    {if $subMenu[s]->type eq 'user'}
                                        {acl isAllowed="MENU_ADMIN"}
                                            <a  class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                                data-url="{url name=admin_menu_delete id=$subMenu[s]->pk_menu}"
                                                data-title="{$subMenu[s]->name|capitalize}"
                                                href="{url name=admin_menu_delete id=$subMenu[s]->pk_menu}">
                                                <i class="icon-trash icon-white"></i>
                                            </a>
                                        {/acl}
                                    {/if}
                                    </div>
                                </td>
                            </tr>
                             {/section}
                         {/if}
                    {/foreach}
                {/section}
                {if !empty($withoutFather)}
                    <tr>
                        <td colspan="3">
                            {t}The next menus don't have a parent. Maybe you have deleted id?{/t}
                        </td>
                    </tr>
                    {section loop=$withoutFather name=m}
                    <tr>
                         <td class="center">
                             <input type="checkbox" class="minput"  id="selected_{$smarty.section.as.iteration}"
                                    name="selected_fld[]" value="{$withoutFather[m]->id}"  style="cursor:pointer;" >
                        </td>
                        <td>
                            {acl isAllowed="MENU_UPDATE"}
                             <a href="{$smarty.server.SCRIPT_NAME}?action=read&amp;name={$withoutFather[m]->name}"
                            {/acl}
                                title="{t 1=$withoutFather[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                 {$withoutFather[m]->name|capitalize}
                             </a>
                        </td>

                        <td class="right">
                            <div class="bnt-group">
                            {acl isAllowed="MENU_UPDATE"}
                                <a  href="{url name=admin_menu_show id=$withoutFather[m]->pk_menu}"
                                    title="{t 1=$subMenu[s]->name}Edit page '%1'{/t}" class="btn">
                                    {t}Edit{/t}
                                </a>
                            {/acl}
                            {if $withoutFather[m]->type eq 'user'}
                                {acl isAllowed="MENU_ADMIN"}
                                    <a  class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                       data-url="{url name=admin_menu_delete id=$withoutFather[m]->pk_menu}" title="{t}Delete{/t}"
                                       data-title="{$widgets[wgt]->title|capitalize}"
                                       href="{url name=admin_menu_delete id=$withoutFather[m]->pk_menu}" >
                                        {t}Delete{/t}
                                    </a>
                                {/acl}
                            {/if}
                            </div>
                        </td>
                    </tr>
                    {/section}
                {/if}
            </tbody>
            <tfoot>
                <tr class="pagination">
                    <td colspan="3">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div><!--fin wrapper-content-->
</form>
{include file="menues/modals/_modalDelete.tpl"}
{include file="menues/modals/_modalBatchDelete.tpl"}
{include file="menues/modals/_modalAccept.tpl"}
{/block}
