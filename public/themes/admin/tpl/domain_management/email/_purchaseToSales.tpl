{capture name=user_url}{url name="admin_acl_user_show" id=$user->id absolute=true}{/capture}
<p>{t 1=$instance->name 2=$user->name 3=$smarty.capture.user_url escape="off"}<a href="%3">%2</a> from instance <a href="http://{$instance->getMainDomain()}">"%1"</a> has requested a domain:{/t}</p>

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
