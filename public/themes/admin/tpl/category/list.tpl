{extends file="base/admin.tpl"}

{block name="header-css" append}
<style type="text/css">
    .ui-tabs .ui-tabs-panel {
        padding:0 !important;
    }
    .ui-tabs-panel, .tabs > div {
        border:0 none !important;
    }
</style>
{/block}

{block name="footer-js" append}
<script>
jQuery(document).ready(function ($){
    $('#categories-types').tabs();
});
</script>
{/block}

{block name="content"}
<form action="#" method="post" name="formulario" id="formulario" {$formAttrs|default:""}>

    <div class="top-action-bar clearfix">
        <div class="wrapper-content">
            <div class="title"><h2>{t}Categories{/t}</h2></div>
            <ul class="old-button">
                {acl isAllowed="CATEGORY_CREATE"}
                <li>
                    <a href="{url name=admin_category_create}" class="admin_add" accesskey="N" tabindex="1">
                        <img src="{$params.IMAGE_DIR}list-add.png" title="Nueva" alt="Nueva"><br />{t}New category{/t}
                    </a>
                </li>
                {/acl}
                <li class="separator"></li>
                <li>
                    <a href="{url name=admin_categories_config}" class="admin_add" title="{t}Config categories module{/t}">
                        <img src="{$params.IMAGE_DIR}template_manager/configure48x48.png" alt="" /><br />
                        {t}Settings{/t}
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="wrapper-content">

        {render_messages}

        <div id="categories-types" class="tabs">

            <ul>
                <li>
                    <a href="#global" id="global-tab" class="active-tab">{t}For articles{/t}</a>
                </li>
                {is_module_activated name="ALBUM_MANAGER"}
                <li>
                    <a href="#album" id="album-tab">{t}For albums{/t}</a>
                </li>
                {/is_module_activated}
                {is_module_activated name="VIDEO_MANAGER"}
                <li>
                    <a href="#video" id="video-tab">{t}For videos{/t}</a>
                </li>
                {/is_module_activated}
                {is_module_activated name="KIOSKO_MANAGER"}
                <li>
                    <a href="#epapel" id="epapel-tab">{t}For ePapers{/t}</a>
                </li>
                {/is_module_activated}
                {is_module_activated name="POLL_MANAGER"}
                <li>
                    <a href="#poll" id="poll-tab">{t}For polls{/t}</a>
                </li>
                {/is_module_activated}
                {is_module_activated name="SPECIAL_MANAGER"}
                <li>
                    <a href="#special" id="special-tab">{t}For Specials{/t}</a>
                </li>
                {/is_module_activated}
                {is_module_activated name="BOOK_MANAGER"}
                <li>
                    <a href="#book" id="book-tab">{t}For books{/t}</a>
                </li>
                {/is_module_activated}
            </ul>

            <div id="global">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Title{/t}</th>
                            <th>{t}Internal name{/t}</th>
                            <th style="width:15px;">{t}Articles{/t}</th>
                            {acl isAllowed="CATEGORY_AVAILABLE"}
                            <th style="width:15px;">{t}Available{/t}</th>
                            <th style="width:15px;" class="nowrap">{t}Show in rss{/t}</th>
                            {/acl}
                            <th style="width:100px;"></th>
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
                        <tr>
                            <td colspan="8">
                                &nbsp;
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
             {is_module_activated name="ALBUM_MANAGER"}
            <div id="album">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Title{/t}</th>
                            <th>{t}Internal name{/t}</th>
                            <th style="width:15px;">{t}Articles{/t}</th>
                            {acl isAllowed="CATEGORY_AVAILABLE"}
                            <th style="width:15px;">{t}Available{/t}</th>
                            {/acl}
                            <th style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '7'}
                                {include file="category/_partials/print_list_category.tpl"
                                    category=$categorys[c]
                                    subcategorys=$subcategorys[c]
                                    num_contents=$num_contents[c]
                                    num_sub_contents=$num_sub_contents[c]}
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
                        <tr>
                            <td colspan="7"> </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {/is_module_activated}
            {is_module_activated name="VIDEO_MANAGER"}
            <div id="video">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Title{/t}</th>
                            <th>{t}Internal name{/t}</th>
                            <th style="width:15px;">{t}Articles{/t}</th>
                            {acl isAllowed="CATEGORY_AVAILABLE"}
                            <th style="width:15px;">{t}Available{/t}</th>
                            {/acl}
                            <th style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '9'}
                                {include file="category/_partials/print_list_category.tpl"
                                    category=$categorys[c]
                                    subcategorys=$subcategorys[c]
                                    num_contents=$num_contents[c]
                                    num_sub_contents=$num_sub_contents[c]}
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
                        <tr>
                            <td colspan="7" > </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {/is_module_activated}
            {is_module_activated name="KIOSKO_MANAGER"}
            <div id="epapel">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Title{/t}</th>
                            <th>{t}Internal name{/t}</th>
                            <th style="width:15px;">{t}Advertisements{/t}</th>
                            {acl isAllowed="CATEGORY_AVAILABLE"}
                            <th style="width:15px;">{t}Available{/t}</th>
                            {/acl}
                            <th style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '14'}
                                {include file="category/_partials/print_list_category.tpl"
                                    category=$categorys[c]
                                    subcategorys=$subcategorys[c]
                                    num_contents=$num_contents[c]
                                    num_sub_contents=$num_sub_contents[c]}
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
                        <tr>
                            <td colspan="7">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
             {/is_module_activated}
             {is_module_activated name="POLL_MANAGER"}
            <div id="poll">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Title{/t}</th>
                            <th>{t}Internal name{/t}</th>
                            <th style="width:15px;">{t}Articles{/t}</th>
                            {acl isAllowed="CATEGORY_AVAILABLE"}
                            <th style="width:15px;">{t}Available{/t}</th>
                            {/acl}
                            <th style="width:100px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '11'}
                                {include file="category/_partials/print_list_category.tpl"
                                    category=$categorys[c]
                                    subcategorys=$subcategorys[c]
                                    num_contents=$num_contents[c]
                                    num_sub_contents=$num_sub_contents[c]}
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
                        <tr >
                            <td colspan="7">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {/is_module_activated}
            {is_module_activated name="SPECIAL_MANAGER"}
            <div id="special">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Title{/t}</th>
                            <th>{t}Internal name{/t}</th>
                            <th style="width:15px;">{t}Articles{/t}</th>
                            {acl isAllowed="CATEGORY_AVAILABLE"}
                            <th style="width:15px;">{t}Available{/t}</th>
                            {/acl}
                            <th style="width:70px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '10'}
                                {include file="category/_partials/print_list_category.tpl"
                                    category=$categorys[c]
                                    subcategorys=$subcategorys[c]
                                    num_contents=$num_contents[c]
                                    num_sub_contents=$num_sub_contents[c]}
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
                        <tr >
                            <td colspan="7">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {/is_module_activated}
            {is_module_activated name="BOOK_MANAGER"}
            <div id="book">
                <table class="table table-hover table-condensed">
                    <thead>
                        <tr>
                            <th>{t}Title{/t}</th>
                            <th>{t}Internal name{/t}</th>
                            <th style="width:15px;">{t}Articles{/t}</th>
                            {acl isAllowed="CATEGORY_AVAILABLE"}
                            <th style="width:15px;">{t}Available{/t}</th>
                            {/acl}
                            <th style="width:70px;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '15'}
                                {include file="category/_partials/print_list_category.tpl"
                                    category=$categorys[c]
                                    subcategorys=$subcategorys[c]
                                    num_contents=$num_contents[c]
                                    num_sub_contents=$num_sub_contents[c]}
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
                        <tr>
                            <td colspan="7">
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            {/is_module_activated}
        </div><!-- categories-tabs -->

    </div>
</form>
<!--fin wrapper-content-->
{include file="category/modals/_modalDelete.tpl"}
{include file="category/modals/_modalEmpty.tpl"}
{/block}
