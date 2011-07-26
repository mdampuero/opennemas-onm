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
    <script type="text/javascript" language="javascript" src="{$params.JS_DIR}utilsMenues.js"></script>

{/block}



{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Menu manager{/t} :: {t}Listing menues{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <form action="#" method="post" name="formulario" id="formulario" {$formAttrs}>
        <div id="{$category}">
            <table class="adminheading">
                <tr>
                    <th nowrap>{t}Menues{/t}</th>
                </tr>
            </table>
            <table class="adminlist">
                <tbody>
                    <tr>
                        <th style="padding:10px;" align='left'>{t}Title{/t}</th>
                        <th>{t}Edit{/t}</th>
                    </tr>

                    {foreach from=$pages item=value key=page}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="padding:10px;font-size: 11px;width:60%;">
                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$page}" title="{$page}">
                                {$page}</a>
                        </td>

                        <td style="padding:10px;width:10%;" align="center">
                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$page}" title="{$page}" title={t}"Edit"{/t}>
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>

     </form>
</div><!--fin wrapper-content-->
{/block}
