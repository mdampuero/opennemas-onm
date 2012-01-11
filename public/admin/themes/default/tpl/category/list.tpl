{extends file="base/admin.tpl"}


{block name="header-js" append}
    {script_tag src="/utilscategory.js" language="javascript"}
{/block}
{block name="header-css" append}
    <style type="text/css">
        .panel {
            border:none !important;
        }
    </style>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Category manager{/t} :: {t}Listing categories{/t}</h2></div>
            <ul class="old-button">
                 {acl isAllowed="CATEGORY_CREATE"}
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add" accesskey="N" tabindex="1">
                        <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva" alt="Nueva"><br />{t}New section{/t}
                    </a>
                </li>
                {/acl}
                {*acl isAllowed="CATEGORY_SETTINGS"}
                <li class="separator"></li>
                <li>
                    <a href="{$smarty.server.PHP_SELF}?action=config" class="admin_add" title="{t}Config album module{/t}">
                        <img border="0" src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Configurations{/t}
                    </a>
                </li>
                {/acl*}
            </ul>
        </div>
    </div>

    <div class="wrapper-content">

        {render_messages}

        <ul id="tabs">
            <li>
                <a href="#global" id="global-tab" class="active-tab">{t}Article categories{/t}</a>
            </li>
            {is_module_activated name="ALBUM_MANAGER"}
            <li>
                <a href="#album" id="album-tab">{t}Album categories{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="VIDEO_MANAGER"}
            <li>
                <a href="#video" id="video-tab">{t}Video categories{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="KIOSKO_MANAGER"}
            <li>
                <a href="#epapel" id="epapel-tab">{t}ePapel categories{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="POLL_MANAGER"}
            <li>
                <a href="#poll" id="poll-tab">{t}Poll categories{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="SPECIAL_MANAGER"}
            <li>
                <a href="#special" id="special-tab">{t}Special categories{/t}</a>
            </li>
            {/is_module_activated}
            {is_module_activated name="BOOK_MANAGER"}
            <li>
                <a href="#book" id="book-tab">{t}Book categories{/t}</a>
            </li>
            {/is_module_activated}
        </ul>
        <br class="" />

        <div class="panel" id="global">
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th style="width:15px;">{t}Articles{/t}</th>
                        <th style="width:15px;">{t}Photos{/t}</th>
                        <th style="width:15px;">{t}Advertisements{/t}</th>
                        <th style="width:15px;">{t}Published{/t}</th>
                        <th style="width:70px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name=c loop=$categorys}
                        {if $categorys[c]->internal_category eq '1'}
                            {include file="category/_partials/print_list_category.tpl"
                                category=$categorys[c]
                                subcategorys=$subcategorys[c]
                                num_contents=$num_contents[c]
                                num_sub_contents=$num_sub_contents[c]|default:array()}
                        {/if}
                    {sectionelse}
                    <tr>
                        <td class="empty">
                            {t}No available categories for listing{/t}
                        </td>
                    </tr>
                    {/section}
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="10">
                            &nbsp;
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="panel" id="album">
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th style="width:15px;">{t}Articles{/t}</th>
                        <th style="width:15px;">{t}Photos{/t}</th>
                        <th style="width:15px;">{t}Advertisements{/t}</th>
                        <th style="width:15px;">{t}Published{/t}</th>
                        <th style="width:70px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name=c loop=$categorys}
                        {if $categorys[c]->internal_category eq '7'}
                            {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                        {/if}
                    {sectionelse}
                            <tr>
                            <td class="empty">
                                {t}No available categories for listing{/t}
                            </td>
                        </tr>
                    {/section}
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8" align="center"> </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="panel" id="video">
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th style="width:15px;">{t}Articles{/t}</th>
                        <th style="width:15px;">{t}Photos{/t}</th>
                        <th style="width:15px;">{t}Advertisements{/t}</th>
                        <th style="width:15px;">{t}Published{/t}</th>
                        <th style="width:70px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name=c loop=$categorys}
                        {if $categorys[c]->internal_category eq '9'}
                            {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                        {/if}
                    {sectionelse}
                    <tr>
                        <td class="empty">
                            {t}No available categories for listing{/t}
                        </td>
                    </tr>
                    {/section}

                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8" align="center"> </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="panel" id="epapel">
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th style="width:15px;">{t}Articles{/t}</th>
                        <th style="width:15px;">{t}Photos{/t}</th>
                        <th style="width:15px;">{t}Advertisements{/t}</th>
                        <th style="width:15px;">{t}Published{/t}</th>
                        <th style="width:70px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name=c loop=$categorys}
                        {if $categorys[c]->internal_category eq '14'}
                            {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                        {/if}
                    {sectionelse}
                    <tr>
                        <td class="empty">
                            {t}No available categories for listing{/t}
                        </td>
                    </tr>
                    {/section}
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

         <div class="panel" id="poll">
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th style="width:15px;">{t}Articles{/t}</th>
                        <th style="width:15px;">{t}Photos{/t}</th>
                        <th style="width:15px;">{t}Advertisements{/t}</th>
                        <th style="width:15px;">{t}Published{/t}</th>
                        <th style="width:70px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name=c loop=$categorys}
                        {if $categorys[c]->internal_category eq '11'}
                            {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                        {/if}
                    {sectionelse}
                    <tr>
                        <td class="empty">
                            {t}No available categories for listing{/t}
                        </td>
                    </tr>
                    {/section}
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="panel" id="special">
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th style="width:15px;">{t}Articles{/t}</th>
                        <th style="width:15px;">{t}Photos{/t}</th>
                        <th style="width:15px;">{t}Advertisements{/t}</th>
                        <th style="width:15px;">{t}Published{/t}</th>
                        <th style="width:70px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name=c loop=$categorys}
                        {if $categorys[c]->internal_category eq '10'}
                            {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                        {/if}
                    {sectionelse}
                    <tr>
                        <td class="empty">
                            {t}No available categories for listing{/t}
                        </td>
                    </tr>
                    {/section}
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

         <div class="panel" id="book">
            <table class="listing-table">
                <thead>
                    <tr>
                        <th>{t}Title{/t}</th>
                        <th>{t}Internal name{/t}</th>
                        <th style="width:15px;">{t}Articles{/t}</th>
                        <th style="width:15px;">{t}Photos{/t}</th>
                        <th style="width:15px;">{t}Advertisements{/t}</th>
                        <th style="width:15px;">{t}Published{/t}</th>
                        <th style="width:70px;">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {section name=c loop=$categorys}
                        {if $categorys[c]->internal_category eq '15'}
                            {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                        {/if}
                    {sectionelse}
                    <tr>
                        <td class="empty">
                            {t}No available categories for listing{/t}
                        </td>
                    </tr>
                    {/section}
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8">
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>


    </div>
    <input type="hidden" id="action" name="action" value="" />
    <input type="hidden" name="id" id="id" value="{$id|default:""}" />
</form>
<!--fin wrapper-content-->
{/block}
