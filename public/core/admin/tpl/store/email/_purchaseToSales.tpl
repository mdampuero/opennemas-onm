{capture name=user_url}{url name="backend_user_show" id=$user->id absolute=true}{/capture}
<p>{t 1=$instance->name 2=$user->name 3=$smarty.capture.user_url 4=$user->email escape="off"} <a href="%3">%2</a> with email <a href="mailto:%4">%4</a> from instance <a href="http://{$instance->getMainDomain()}">"%1" ({$instance->getMainDomain()})</a> has requested a purchase:{/t}</p>
<br>
<h4>{t}Purchased items{/t}</h4>
<br>
<ul>
  {foreach from=$items key=id item=item}
    <li>
      <p>{$item['description']}</p>
      <small>{$item['uuid']}</small>
    </li>
  {/foreach}
</ul>
<br>
<h4>{t}Billing information{/t}</h4>
{t}Name{/t}: {$client->last_name}, {$client->first_name}<br>
{if $client->company}
  {t}Company{/t}: {$client->company}<br>
{/if}
{t}VAT number{/t}: {$client->vat_number}<br>
{t}Email{/t}: {$client->email}<br>
{t}Phone{/t}: {$client->phone}<br>
<address>
  {$client->address}<br>
  {$client->postal_code}, {$client->city}, {$client->state}<br>
  {$countries[$client->country]}<br>
</address>
