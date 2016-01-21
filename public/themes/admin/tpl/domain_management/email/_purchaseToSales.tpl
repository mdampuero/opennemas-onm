{capture name=user_url}{url name="admin_acl_user_show" id=$user->id absolute=true}{/capture}
<p>{t 1=$instance->name 2=$user->name 3=$smarty.capture.user_url 4=$user->email escape="off"} <a href="%3">%2</a> with email <a href="mailto:%4">%4</a> from instance <a href="http://{$instance->getMainDomain()}">"%1" ({$instance->getMainDomain()})</a> has requested a domain:{/t}</p>
<br>
<h4>
  {if $create}
    {t}Domains to create{/t}:
  {else}
    {t}Domains to redirect{/t}:
  {/if}
</h4>
<br>
<ul>
 {foreach from=$domains item=domain}
   <li>{$domain}</li>
 {/foreach}
</ul>
<br>
<h4>{t}Billing information{/t}</h4>
{t}Name{/t}: {$billing['name']}<br>
{if $billing['company']}
  {t}Company{/t}: {$billing['company']}<br>
{/if}
{t}VAT number{/t}: {$billing['vat']}<br>
{t}Email{/t}: {$billing['email']}<br>
{t}Phone{/t}: {$billing['phone']}<br>
<address>
  {$billing['address']}<br>
  {$billing['postal_code']}, {$billing['city']}, {$billing['state']}<br>
  {$countries[$billing['country']]}<br>
</address>
