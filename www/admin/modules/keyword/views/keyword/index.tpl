{extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Keyword Manager{/t}{/block}

{block name="head-js"}
    <script type="text/javascript" src="{$params.JS_DIR}jquery.dataTables.js"></script>
{/block}

{block name="head-css"}
    <link rel="stylesheet" href="{$params.CSS_DIR}datatables/style/table_jui.css" type="text/css" />
{/block}

{block name="body-content"}
    <form action="{baseurl}/widget/{$request->getActionName()}" method="post">

    {flashmessenger}

    {toolbar_route toolbar="toolbar-top"
        icon="new" route="keyword-keyword-create" text="New Keyword"}

    <div id="menu-acciones-admin">
        <div style="float: left; margin-left: 10px; margin-top: 10px;">
            <h2>{t}Keyword Manager{/t}</h2>
        </div>
        {toolbar name="toolbar-top"}
    </div>

    <table border="0" cellpadding="4" cellspacing="0" class="adminlist" id="datagrid">
    <thead>
    <tr>
        <th>{t}Keyword{/t}</th>
        <th>{t}Value{/t}</th>
        <th>{t}Type{/t}</th>
        <th>&nbsp;</th>
        <th>&nbsp;</th>
    </tr>
    </thead>

    <tbody>
    {section name=k loop=$terms}
    <tr>
        <td>
            {$terms[k]->word}
        </td>

        <td width="240">
            {$terms[k]->value}
        </td>

        <td width="100">
            {$terms[k]->type}
        </td>

        <td width="24">
            <a href="{baseurl}/{url route="keyword-keyword-update" pk_keyword=$terms[k]->pk_keyword}" title="{t}Edit{/t}">
                <img src="{$params.IMAGE_DIR}edit.png" border="0" /></a>
        </td>
        <td width="24">
            <a href="{baseurl}/{url route="keyword-keyword-delete" pk_keyword=$terms[k]->pk_keyword}"
               onclick="" title="{t}Delete{/t}">
                <img src="{$params.IMAGE_DIR}trash.png" border="0" /></a>
        </td>
    </tr>
    {sectionelse}
    <tr>
        <td align="center" colspan="5"><b>{t}No keywords found{/t}.</b></td>
    </tr>
    {/section}
    </tbody>
    </table>

    <script type="text/javascript">
    $(document).ready(function(){
        $('#datagrid').dataTable({
            "bJQueryUI": true
        });
    });
    </script>


    </form>
{/block}