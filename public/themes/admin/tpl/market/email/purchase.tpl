<p>{t 1=$instance->name}The instance %1 requested a purchase{/t}</p>

{t}Modules to purchase{/t}:
<ul>
  {foreach from=$modules item=module}
    <li>{$module}</li>
  {/foreach}
</ul>
