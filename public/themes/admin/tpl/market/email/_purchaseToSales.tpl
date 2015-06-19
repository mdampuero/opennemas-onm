{capture name=user_url}{url name="admin_acl_user_show" id=$user->id absolute=true}{/capture}
<p>{t 1=$instance->name 2=$user->name 3=$smarty.capture.user_url 4=$instance_url escape="off"}
  <a href="%3">%2</a> from instance <a href="http://{$instance->getMainDomain()}">"%1"</a> has requested a purchase:{/t}
</p>

{t}Modules to purchase{/t}:
<ul>
  {foreach from=$modules item=module}
    <li>{$module}</li>
  {/foreach}
</ul>
