{extends file="base/admin.tpl"}


{block name="header-js" append}
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilscategory.js"></script>
    <script type="text/javascript" src="{$params.JS_DIR}MiniColorPicker.js"></script>
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
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add" accesskey="N" tabindex="1">
                    <img border="0" src="{$params.IMAGE_DIR}list-add.png" title="Nueva" alt="Nueva"><br />{t}New section{/t}
                </a>
            </li>
            {acl isAllowed="CATEGORY_SETTINGS"}
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

       {render_messages}
    
        <ul id="tabs">
            <li>
                    <a href="category.php#listado">Listar secciones</a>
            </li>
 
        </ul>

        <div class="panel" id="listado">

            <table class="adminheading">
                <tr>
                    <td>{t}Global categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                            {section name=c loop=$categorys}
                                {if $categorys[c]->internal_category eq '1'}
                                    {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]|default:array()}
                                {/if}
                            {sectionelse}
                                <h2><strong>{t}No available sections{/t}</strong></h2>
                            {/section}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="10" align="center"> </td>
                    </tr>
                </tfoot>
            </table>

            <br>


            <table class="adminheading">
                <tr>
                    <td>{t}Album categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                            {section name=c loop=$categorys}
                                {if $categorys[c]->internal_category eq '7'}
                                    {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                                {/if}
                            {sectionelse}
                                <h2><strong>{t}No available sections{/t}</strong></h2>
                            {/section}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8" align="center"> </td>
                    </tr>
                </tfoot>
            </table>

            <br>

            <table class="adminheading">
                <tr>
                    <td>{t}Video categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '9'}
                                {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                            {/if}
                        {sectionelse}
                            <h2><strong>{t}No available sections{/t}</strong></h2>
                        {/section}
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8" align="center"> </td>
                    </tr>
                </tfoot>
            </table>

            <br>

            <table class="adminheading">
                <tr>
                    <td>{t}ePaper categories{/t}</td>
                </tr>
            </table>
            <table class="adminlist" id="tabla"  width="100%">
                <thead>
                    <tr>
                        <th width="360px">{t}Title{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Name{/t}</th>
                        <th width="120px" padding="0px 10px" align="center">{t}Type{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Articles{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Photos{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Advertisements{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Published{/t}</th>
                        <th align="center" padding="0px 10px" width="100px">{t}Actions{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="10">
                        {section name=c loop=$categorys}
                            {if $categorys[c]->internal_category eq '14'}
                                {include file="category/_partials/print_list_category.tpl" category=$categorys[c] subcategorys=$subcategorys[c] num_contents=$num_contents[c] num_sub_contents=$num_sub_contents[c]}
                            {/if}
                        {sectionelse}
                            <h2><strong>{t}No available sections{/t}</strong></h2>
                        {/section}

                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="pagination">
                        <td colspan="8" align="center"> </td>
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
