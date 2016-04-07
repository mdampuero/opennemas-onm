<p>
  <strong>{t}Thank you for your recent transaction on Opennemas.{/t}</strong>
</p>

<p>
  {t}The purchased items are detailed below.{/t}
</p>

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

<p>
  {t 1=$url}You can view your purchase invoice <a href="%1">here</a>{/t}
</p>

<h4>{t}Billing information{/t}</h4>
{t}Name{/t}: {$client->last_name}, {$client->first_name}<br>
{if $client->company}
  {t}Company{/t}: {$client->company}<br>
{/if}
{t}VAT number{/t}: {$client->vat}<br>
{t}Email{/t}: {$client->email}<br>
{t}Phone{/t}: {$client->phone}<br>
<address>
  {$client->address}<br>
  {$client->postal_code}, {$client->city}, {$client->state}<br>
  {$countries[$client->country]}<br>
</address>
