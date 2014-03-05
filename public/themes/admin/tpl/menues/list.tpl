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
            <div class="title"><h2>{t}Menus{/t}</h2></div>
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

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th style="width:15px;">
                        <input type="checkbox" class="toggleallcheckbox">
                    </th>
                    <th>{t}Title{/t}</th>
                    {if count($menu_positions) > 1}
                    <th class="nowrap center" style="width:100px;">{t}Menu position assigned{/t}</th>
                    {/if}
                    <th class="center" style="width:100px;">{t}Actions{/t}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$menues item=menu}
                    <tr>
                         <td class="center">
                            <input type="checkbox" class="minput"  id="{$menu->pk_menu}"
                                   name="selected_fld[]" value="{$menu->pk_menu}">
                        </td>
                        <td>
                            {acl isAllowed="MENU_UPDATE"}
                            <a href="{url name=admin_menu_show id=$menu->pk_menu}"
                                title="{t 1=$menu->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                            {/acl}
                                {$menu->name|capitalize}
                            {acl isAllowed="MENU_UPDATE"}
                            </a>
                            {/acl}
                        </td>
                        {if count($menu_positions) > 1}
                        <td class="left">
                            {if !empty($menu->position)}{$menu->position}{else}{t}Unasigned{/t}{/if}
                        </td>
                        {/if}
                        <td class="right">
                            <div class="btn-group">
                            {acl isAllowed="MENU_UPDATE"}
                            <a href="{url name=admin_menu_show id=$menu->pk_menu}"
                                title="{t 1=$menu->name}Edit page '%1'{/t}" class="btn">
                                <i class="icon-pencil"></i> {t}Edit{/t}
                            </a>
                            {/acl}
                            {if $menu->type eq 'user'}
                                {acl isAllowed="MENU_DELETE"}
                                    <a  class="del btn btn-danger" data-controls-modal="modal-from-dom"
                                        data-url="{url name=admin_menu_delete id=$menu->pk_menu}"
                                        data-title="{$menu->name|capitalize}"
                                        href="{url name=admin_menus_delete id=$menu->pk_menu}">
                                        <i class="icon-trash icon-white"></i>
                                    </a>
                                {/acl}
                            {/if}
                            </div>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4">&nbsp;</td>
                </tr>
            </tfoot>
        </table>
    </div><!--fin wrapper-content-->
</form>
{include file="menues/modals/_modalDelete.tpl"}
{include file="menues/modals/_modalBatchDelete.tpl"}
{include file="menues/modals/_modalAccept.tpl"}
{/block}
