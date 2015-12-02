<p>{t 1=$instance->name}You have requested the following purchases for your newspaper "%1"{/t}</p>

<h4>
  {if $create}
    {t}Domains to create{/t}:
  {else}
    {t}Domains to redirect{/t}:
  {/if}
</h4>

<ul>
 {foreach from=$domains item=domain}
   <li>{$domain}</li>
 {/foreach}
</ul>

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

{t}Our sales department will contact you to get further information{/t}
