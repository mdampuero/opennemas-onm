<br>
{if count($purchases) > 0}
<table class="table table-condensed">
    <thead>
        <tr>
            <th class="left">{t}User name{/t}</th>
            <th class="left">{t}Full name{/t}</th>
            <th class="left">{t}Order id{/t}</th>
            <th class="left">{t}Payment date{/t}</th>
            <th class="right">{t}Amount{/t}</th>
        </tr>
    </thead>
    {/if}
    <tbody class="sortable">
    {foreach from=$purchases item=purchase}
    <tr data-id="{$order->id}">
        <td class="left">
            <a href="{url name=admin_acl_user_show id=$purchase->user_id}#paywall">{$purchase->user->username}</a>
        </td>
        <td class="left">
            {$purchase->user->name}
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
    {/foreach}
    </tbody>
    {if count($purchases) > 0}
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
<a href="{url name=admin_paywall_purchases}" class="btn btn-white">{t}Show all…{/t}</a>
{else}
<div class="center">
    {t}No purchases were made yet.{/t}
</div>
{/if}
