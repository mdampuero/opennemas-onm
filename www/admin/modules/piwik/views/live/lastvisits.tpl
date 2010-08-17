{* extends file="layout.tpl"}

{block name="head-title"}{$smarty.block.parent} - {t}Piwik Last Visits{/t}{/block}

{block name="head-js"}
    <script type="text/javascript" src="{$params.JS_DIR}jquery.dataTables.js"></script>
{/block}

{block name="head-css"}
    <link rel="stylesheet" href="{$params.CSS_DIR}datatables/style/table_jui.css" type="text/css" />
{/block}

{block name="body-content" *}

    <table id="datagrid">
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        {foreach key=key item=item from=$visits}
        <tr>
            {foreach key=prop item=elem from=$item}
            <td>
                {$elem}
            </td>
            {/foreach}
        </tr>    
        {/foreach}
        </tbody>
    </table>

    <script type="text/javascript">
    $(document).ready(function(){
        $('#datagrid').dataTable({
            "bJQueryUI": true
        });
    });    
    </script>
{* /block *}