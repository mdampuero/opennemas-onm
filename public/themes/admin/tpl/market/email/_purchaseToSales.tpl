<p>{t 1=$instance->name 2=$user->name}%2 from instance "%1" has requested a purchase:{/t}</p>

{t}Modules to purchase{/t}:
<ul>
  {foreach from=$modules item=module}
    <li>{$module}</li>
  {/foreach}
</ul>
