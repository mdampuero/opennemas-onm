<table class="table table-hover table-condensed">

    {if count($purchases) > 0}
    <thead>
        <tr>
            <th class="left">{t}User{/t}</th>
            <th class="left">{t}Order id{/t}</th>
            <th class="left">{t}Created{/t}</th>
            <th class="right">{t}Amount{/t}</th>
        </tr>
    </thead>
    {else}
    <thead>
        <tr>
            <th colspan="11">
                &nbsp;
            </th>

        </tr>
    </thead>
    {/if}
    <tbody class="sortable">
    {foreach from=$purchases item=purchase}
    <tr data-id="{$order->id}">
        <td class="left">
            <a href="{url name=admin_acl_user_show id=$purchase->user_id}#paywall">{$purchase->user->name}</a>
        </td>
        <td class="left">
            {$purchase->payment_id|clearslash}
        </td>
        <td class="left">
            {datetime date=$purchase->created}
        </td>
        <td class="right">
            {$purchase->payment_amount|clearslash} {$money_units[$settings['money_unit']]}
        </td>

    </tr>
    {foreachelse}
    <tr>
        <td class="empty" colspan="11">{t}No purchases available{/t}</td>
    </tr>
    {/foreach}
    </tbody>
    <tfoot>
        <tr>
            <td colspan="11" class="center">
                <div class="pagination">
                    {$pagination->links}
                </div>
            </td>
        </tr>
    </tfoot>
</table>