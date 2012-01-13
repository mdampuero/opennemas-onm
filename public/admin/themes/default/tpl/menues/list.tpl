{extends file="base/admin.tpl"}

{block name="js-library" append}

    {script_tag src="/jquery/jquery.min.js"}
    {script_tag src="/jquery/jquery-ui.js"}
    <script>jQuery.noConflict();</script>
{/block}

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
    {css_tag href="/bootstrap/bootstrap.css"}
    {script_tag src="/jquery/bootstrap-modal.js" language="javascript"}
    {script_tag src="/utilsMenues.js" language="javascript"}
{/block}



{block name="content"}
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
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add">
                    <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New menu{/t}
                </a>
                {/acl}
            </li>
        </ul>
    </div>
</div>

<div class="wrapper-content">
    {render_messages}
</div><!-- / -->

<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario">
        <div>
            <table class="listing-table">
                <thead>
                    <tr>
                        <th style="width:15px;"><input type="checkbox" id="toggleallcheckbox"></th>
                        <th>{t}Title{/t}</th>
                        <th align="center" style="width:30px;">{t}Actions{/t}</th>

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
                                <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$menues[m]->name}"
                             {/acl}
                                    title="{t 1=$menues[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                     {$menues[m]->name|capitalize}
                                 </a>
                            </td>
                             <td class="center">
                                 <ul class="action-buttons clearfix">
                                    <li>
                                        {acl isAllowed="MENU_UPDATE"}
                                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$menues[m]->name}" title="{t 1=$menues[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                                <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                            </a>
                                        {/acl}
                                    </li>
                                     <li>
                                         {if $menues[m]->type eq 'user'}
                                        {acl isAllowed="MENU_ADMIN"}
                                            <a class="del" data-controls-modal="modal-from-dom"
                                               data-id="{$menues[m]->pk_menu}" data-title="{$menues[m]->name|capitalize}"  href="#" >
                                                <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                            </a>
                                        {/acl}
                                        {/if}
                                    </li>
                                 </ul>
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
                                        <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$subMenu[s]->name}"
                                        {/acl}
                                           title="{t 1=$subMenu[s]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                            {$subMenu[s]->name|capitalize}
                                        </a>
                                    </td>

                                    <td class="center">
                                         <ul class="action-buttons clearfix">
                                        <li>
                                            {acl isAllowed="MENU_UPDATE"}
                                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$subMenu[s]->name}"
                                               title="{t 1=$subMenu[s]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                                <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                            </a>
                                            {/acl}
                                        </li>
                                        <li>
                                             {if $subMenu[s]->type eq 'user'}
                                            {acl isAllowed="MENU_ADMIN"}
                                                   <a class="del" data-controls-modal="modal-from-dom"
                                                       data-id="{$subMenu[s]->pk_menu}"
                                                       data-title="{$subMenu[s]->name|capitalize}"  href="#" >
                                                    <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                                </a>
                                            {/acl}
                                            {/if}
                                        </li>
                                      </ul>
                                    </td>
                                </tr>
                                 {/section}
                             {/if}
                        {/foreach}
                    {/section}
                    {if !empty($withoutFather)}
                        <tr>
                            <td colspan="3">
                                {t} Have a problem with next submenues. Parent menu was deleted {/t}
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
                                 <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$withoutFather[m]->name}"
                                {/acl}
                                    title="{t 1=$withoutFather[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                     {$withoutFather[m]->name|capitalize}
                                 </a>
                            </td>

                            <td class="center">
                                 <ul class="action-buttons clearfix">
                                    <li>
                                    {acl isAllowed="MENU_UPDATE"}
                                        <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$withoutFather[m]->name}" title="{t 1=$withoutFather[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                            <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                                        </a>
                                    {/acl}
                                    </li>
                                    <li>
                                         {if $withoutFather[m]->type eq 'user'}
                                        {acl isAllowed="MENU_ADMIN"}
                                           <a class="del" data-controls-modal="modal-from-dom"
                                               data-id="{$withoutFather[m]->pk_menu}"
                                               href="#" >
                                                <img src="{$params.IMAGE_DIR}trash.png" border="0" />
                                            </a>
                                        {/acl}
                                        {/if}
                                   </li>
                               </ul>
                            </td>
                        </tr>
                       {/section}
                    {/if}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <input type="hidden" id="action" name="action" value="" />
     </form>
</div><!--fin wrapper-content-->
{include file="menues/modals/_modalDelete.tpl"}
{include file="menues/modals/_modalBatchDelete.tpl"}
{include file="menues/modals/_modalAccept.tpl"}
{/block}
