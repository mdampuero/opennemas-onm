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
{t}Name{/t}: {$billing['name']}
{if $billing['company']}
  {t}Company{/t}: {$billing['company']}
{/if}
{t}VAT number{/t}: {$billing['vat']}
{t}Email{/t}: {$billing['email']}
{t}Phone{/t}: {$billing['phone']}
{t}Address{/t}: {$billing['address']}
{t}Postal code{/t}: {$billing['postal_code']}
{t}City{/t}: {$billing['city']}
{t}State{/t}: {$billing['state']}
{t}Country{/t}: {$billing['country']}

{t}Our sales department will contact you to get further information{/t}
