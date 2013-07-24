{t}User name{/t},{t}Full name{/t},{t}Order id{/t},{t}Payment date{/t},{t}Amount{/t}
{foreach from=$purchases item=purchase}
"{$purchase->username}","{$purchase->name}","{$purchase->id}","{datetime date=$purchase->created}","{$purchase->payment_amount|clearslash} {$money_units[$settings['money_unit']]}"
{/foreach}