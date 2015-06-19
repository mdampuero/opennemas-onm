<p>{t 1=$instance->name}You have requested the following purchases for your newspaper "%1"{/t}</p>

{t}Modules requested{/t}:
<ul>
 {foreach from=$modules item=module}
   <li>{$module}</li>
 {/foreach}
</ul>

{t}Our sales department will contact you to get further information{/t}
