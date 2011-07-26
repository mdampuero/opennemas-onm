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
                    <td>{t}Menues{/t}</td>
                </tr>
            </table>
            <table class="adminlist">
                <thead>
                    <tr>
                        <th style="text-align:left; padding-left:10px">{t}Title{/t}</th>
                        <th align="center">{t}Edit{/t}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$pages item=value key=page}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="padding-left:10px">
                            {$page|capitalize}
                        </td>

                        <td style="padding:5px; width:100px;" align="center">
                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$page}" title="{t 1=$page}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                            </a>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan=2></td>
                    </tr>
                </tfoot>
            </table>
        </div>

     </form>
</div><!--fin wrapper-content-->
{/block}
