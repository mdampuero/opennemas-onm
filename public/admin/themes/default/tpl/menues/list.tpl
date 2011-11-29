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
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Menu manager{/t} :: {t}Listing menues{/t}</h2></div>
          <ul class="old-button">
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
    <form action="#" method="post" name="formulario" id="formulario">
        <div>
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th align="center" style="width:30px;">{t}Actions{/t}</th>
                        
                    </tr>
                </thead>
                <tbody>
                    {section loop=$menues name=m}
                        <tr>
                            <td>
                            {acl isAllowed="MENU_UPDATE"}
                                <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$menues[m]->name}"
                             {/acl}
                                    title="{t 1=$menues[s]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
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
                                            <a onclick="confirm({t}'Do you want delete this menu?'{/t});return false;" href="{$smarty.server.SCRIPT_NAME}?action=delete&name={$menues[m]->name}" title="{t 1=$menues[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
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
                                                <a onclick="confirm({t}'Do you want delete this menu?'{/t});return false;" href="{$smarty.server.SCRIPT_NAME}?action=delete&name={$subMenu[s]->name}" title="{t 1=$subMenu[s]->name}Delete page '%1'{/t}" title={t}"Delete"{/t}>
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
                            <td>
                            {acl isAllowed="MENU_UPDATE"}
                                <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$withoutFather[m]->name}"
                             {/acl}
                                    title="{t 1=$withoutFather[s]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
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
                                            <a onclick="confirm({t}'Do you want delete this menu?'{/t});return false;" href="{$smarty.server.SCRIPT_NAME}?action=delete&name={$withoutFather[m]->name}" title="{t 1=$withoutFather[m]->name}Delete page '%1'{/t}" title={t}"Delete"{/t}>
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
                        <td colspan=2>&nbsp;</td>
                    </tr>
                </tfoot>
            </table>
        </div>

     </form>
</div><!--fin wrapper-content-->
{/block}
