<p>{t 1=$instance->name}You have requested the following purches for the instance %1{/t}</p>

{t}Modules to purchase{/t}:
<ul>
  {foreach from=$modules item=module}
    <li>{$module}</li>
  {/foreach}
</ul>
