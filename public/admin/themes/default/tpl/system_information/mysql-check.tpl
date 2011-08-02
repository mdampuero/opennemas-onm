{extends file="base/admin.tpl"}

{block name="content"}
<div class="top-action-bar clearfix">
    <div class="wrapper-content">
        <div class="title"><h2>{t}Database manager{/t}</h2></div>
    </div>
</div>
<div class="wrapper-content">
    <table class="adminheading">
        <tr>
            <th>Check Mysql server performance</th>
        </tr>
    </table>
    <table class="adminform">
        <tbody>
            <tr>
                <td>
                    {if isset($return) && !empty($return)}
                        <h3>{if $action == 'check'}{t}Mysql checking{/t}{/if}</h3>
                        <strong>{$checkout}</strong>
                        <pre>
                        {foreach from=$return item=foo}
                        {$foo}
                        {/foreach}
                        </pre>
                    {else}
                        <b>{$mysqlcheck}</b><br />
                         <pre>
                            <div style="text-align:center"><h3>There is an error.</h3></div>
                        </pre>
                    {/if}
                </td>
            </tr>
        </tbody>
        <tfoot>
            &nbsp;
        </tfoot>
    </table>
</div>
{/block}
