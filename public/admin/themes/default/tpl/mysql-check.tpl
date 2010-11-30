{extends file="base/admin.tpl"}

{block name="content"}
<div>
    {include file="botonera_up.tpl"}
    <table class="adminheading">
        <tr>
            <th nowrap>Check Mysql server performance</th>
        </tr>
    </table>

{if isset($return) && !empty($return)}
    <h3>{if $action == 'check'}Mysql checking{/if}</h3>
    <b>{$checkout}</b><br />
    <pre style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
    <p>
    {foreach from=$return item=foo}
    {$foo}
    {/foreach}
    </p>
    </pre>
{else}
    <b>{$mysqlcheck}</b><br />
     <pre style="background:#F7F7F7 none repeat scroll 0 0;border:1px solid #D7D7D7;padding:0;margin:0.5em 1em;overflow:auto;">
        <div style="text-align:center"><h3>There is an error.</h3></div>
    </pre>
{/if}
</div>
{/block}
