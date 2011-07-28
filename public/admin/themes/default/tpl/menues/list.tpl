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
          <ul class="old-button">
            <li>
                <a href="{$smarty.server.PHP_SELF}?action=new" class="admin_add">
                    <img border="0" src="{$params.IMAGE_DIR}/article_add.png" alt="Nuevo"><br />{t}New menu{/t}
                </a>
            </li>
        </ul>
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
                    {section loop=$menues name=m}
                    <tr {cycle values="class=row0,class=row1"}>
                        <td style="padding-left:10px">
                            {$menues[m]->name|capitalize}
                        </td>

                        <td style="padding:5px; width:100px;" align="center">
                            <a href="{$smarty.server.SCRIPT_NAME}?action=read&name={$menues[m]->name}" title="{t 1=$menues[m]->name}Edit page '%1'{/t}" title={t}"Edit"{/t}>
                                <img src="{$params.IMAGE_DIR}edit.png" border="0" />
                            </a>
                        </td>
                    </tr>
                    {/section}
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
